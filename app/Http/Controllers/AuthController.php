<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function index()
    {
        return view('auth.login');
    }

    public function registration()
    {
        return view('auth.registration');
    }

    public function postLogin(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);

        $remember = $request->has('remember');
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();

            if ($user->role === 'admin') {
                return redirect()->intended('/dashboard')->withSuccess('Welcome back!');
            }

            return redirect()->intended('/')->withSuccess('Welcome back!');
        }

        return redirect('login')->withErrors('Invalid email or password.');
    }

    public function postRegistration(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed|min:6',
            'password_confirmation' => 'required|min:6',
        ]);

        $this->create($request->all());

        return redirect('/login')->withSuccess('Account created! Please log in.');
    }

    public function dashboard()
    {
        if (! Auth::check()) {
            return redirect('login')->withErrors('You do not have access!');
        }

        $user = Auth::user();

        // Latest products for table
        $products = Product::with('category')
            ->orderBy('created_at', 'desc')
            ->take(12)
            ->get();

        // Stat card counts
        $totalProducts = Product::count();
        $totalOrders = Order::count();
        $totalCustomers = User::count();
        $totalCategories = DB::table('categories')->count();
        $totalRevenue = Order::where('status', 2)->sum('amount'); // Paid only

        $startOfWeek = now()->subDays(6)->startOfDay();
        $dailyRevenue = Order::whereBetween('created_at', [$startOfWeek, now()->endOfDay()])
            ->selectRaw('DATE(created_at) as order_date, SUM(amount) as revenue')
            ->groupBy(DB::raw('DATE(created_at)'))
            ->get()
            ->keyBy('order_date')
            ->map(fn ($row) => (float) $row->revenue);

        $weeklySales = collect(range(0, 6))->map(function ($offset) use ($startOfWeek, $dailyRevenue) {
            $date = $startOfWeek->copy()->addDays($offset);
            $key = $date->format('Y-m-d');

            return [
                'day' => $date->format('D'),
                'short' => $date->format('D'),
                'revenue' => (float) ($dailyRevenue[$key] ?? 0),
            ];
        });
        $weeklyMax = max($weeklySales->max('revenue') ?? 0, 0);

        // Top products by units sold
        $topProducts = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->select('products.name', DB::raw('SUM(order_items.quantity) as total_quantity'))
            ->groupBy('products.name')
            ->orderByDesc('total_quantity')
            ->take(5)
            ->get();

        // Order status breakdown for donut
        $approvedOrders = Order::where('status', 1)->count();
        $pendingOrders = Order::where('status', 2)->count(); // Paid = pending shipment
        $rejectedOrders = Order::where('status', 0)->count();

        // Recent orders for team collaboration panel
        $recentOrders = Order::with('user')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Users for dashboard role management
        $dashboardUsers = User::orderBy('created_at', 'desc')->get();
        $totalAdmins = User::where('role', 'admin')->count();
        $totalDrivers = User::where('role', 'driver')->count();
        $totalCustomersCount = User::whereIn('role', ['customer', 'user', null])->count();

        return view('auth.dashboard', compact(
            'user',
            'products',
            'totalProducts',
            'totalOrders',
            'totalCustomers',
            'totalCategories',
            'totalRevenue',
            'weeklySales',
            'weeklyMax',
            'topProducts',
            'approvedOrders',
            'pendingOrders',
            'rejectedOrders',
            'recentOrders',
            'dashboardUsers',
            'totalAdmins',
            'totalDrivers',
            'totalCustomersCount'
        ));
    }

    public function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'] ?? 'user',
        ]);
    }

    public function logout()
    {
        Session::flush();
        Auth::logout();

        return redirect('login')->withSuccess('You have been logged out.');
    }

    /**
     * Redirect the user to the Google OAuth page.
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    /**
     * Resolve Google user with robust SSL certificate verification and local fallback.
     */
    protected function getGoogleUser(): \Laravel\Socialite\Two\User
    {
        $caPath = base_path('storage/cacert.pem');
        $verify = file_exists($caPath) ? $caPath : (file_exists('D:/php/cacert.pem') ? 'D:/php/cacert.pem' : true);

        try {
            $driver = Socialite::driver('google')->stateless();
            $driver->setHttpClient(new Client([
                'verify' => $verify,
                'timeout' => 20,
            ]));

            return $driver->user();
        } catch (\Throwable $e) {
            // If SSL verification fails in local development environment (cURL error 60), retry with verify => false
            if (str_contains($e->getMessage(), 'cURL error 60') || str_contains($e->getMessage(), 'certificate') || str_contains($e->getMessage(), 'SSL')) {
                Log::warning('Retrying Google OAuth token exchange without SSL CA verification: '.$e->getMessage());
                $driver = Socialite::driver('google')->stateless();
                $driver->setHttpClient(new Client([
                    'verify' => false,
                    'timeout' => 20,
                ]));

                return $driver->user();
            }

            throw $e;
        }
    }

    /**
     * Obtain the user information from Google and log them in.
     */
    public function handleGoogleCallback(Request $request)
    {
        // Handle cancellation or OAuth errors from Google
        if ($request->has('error')) {
            $errorMsg = $request->get('error_description') ?: $request->get('error');

            return redirect('/login')->withErrors('Google authentication was cancelled: '.$errorMsg);
        }

        try {
            $googleUser = $this->getGoogleUser();

            $email = $googleUser->getEmail();
            $googleId = (string) $googleUser->getId();

            if (! $email) {
                return redirect('/login')->withErrors('Unable to retrieve email from your Google account.');
            }

            // Find user by google_id or by email
            $user = User::where('google_id', $googleId)
                ->orWhere('email', $email)
                ->first();

            if (! $user) {
                // New user registration via Google
                $user = User::create([
                    'name' => $googleUser->getName() ?: ($googleUser->getNickname() ?: explode('@', $email)[0]),
                    'email' => $email,
                    'google_id' => $googleId,
                    'password' => Hash::make(Str::random(32)),
                    'role' => 'customer',
                    'avatar' => $googleUser->getAvatar(),
                    'email_verified_at' => now(),
                    'status' => 'active',
                ]);
            } else {
                // Link Google credentials and update profile if needed
                $dirty = false;
                if (empty($user->google_id)) {
                    $user->google_id = $googleId;
                    $dirty = true;
                }
                if (empty($user->avatar) && $googleUser->getAvatar()) {
                    $user->avatar = $googleUser->getAvatar();
                    $dirty = true;
                }
                if (empty($user->email_verified_at)) {
                    $user->email_verified_at = now();
                    $dirty = true;
                }
                if ($dirty) {
                    $user->save();
                }
            }

            if ($user->isSuspended()) {
                return redirect('/login')->withErrors('Your account has been suspended. Please contact support.');
            }

            Auth::login($user, true);
            $request->session()->regenerate();

            if ($user->role === 'admin') {
                return redirect()->intended('/dashboard')->withSuccess('Welcome back, '.$user->name.'!');
            }

            if ($user->role === 'driver') {
                return redirect()->intended('/driver')->withSuccess('Welcome back, '.$user->name.'!');
            }

            return redirect()->intended('/')->withSuccess('Welcome back, '.$user->name.'!');

        } catch (\Throwable $e) {
            Log::error('Google Login Error: '.$e->getMessage(), [
                'exception' => $e,
            ]);

            return redirect('/login')->withErrors('Google login failed: '.$e->getMessage());
        }
    }
}
