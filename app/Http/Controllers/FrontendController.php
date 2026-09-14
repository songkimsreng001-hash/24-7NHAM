<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FrontendController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::all();
        // Show all products on the home page
        $products = Product::orderBy('created_at', 'DESC')->get();

        return view('frontend.index')
            ->with('products', $products)
            ->with('categories', $categories);
    }

    public function list(Request $request)
    {
        $categories = Category::all();
        $query = Product::with('category')->orderBy('created_at', 'DESC');

        if ($request->filled('category')) {
            $cat = trim((string) $request->query('category'));
            $clean = str_replace(['-', '_'], ' ', strtolower($cat));
            $collapsed = str_replace(['-', '_', ' '], '', strtolower($cat));

            $query->whereHas('category', function ($q) use ($cat, $clean, $collapsed) {
                if (is_numeric($cat)) {
                    $q->where('id', $cat);
                } else {
                    $q->where(DB::raw('LOWER(name)'), 'LIKE', "%{$clean}%")
                        ->orWhere(DB::raw('LOWER(name)'), 'LIKE', "%{$cat}%")
                        ->orWhere(DB::raw("REPLACE(LOWER(name), ' ', '')"), 'LIKE', "%{$collapsed}%");
                }
            });
        }

        $products = $query->paginate(12)->withQueryString();

        return view('frontend.list')->with('products', $products)->with('categories', $categories);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $categories = Category::all();

        return view('frontend.show')->with('product', Product::find($id))->with('categories', $categories);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function getBySearch(Request $request)
    {
        $keyword = trim((string) $request->input('keyword', $request->input('title', '')));
        $products = ($keyword !== '')
            ? Product::where('name', 'LIKE', '%'.$keyword.'%')->paginate(12)->withQueryString()
            : Product::paginate(12)->withQueryString();

        return view('frontend.search')
            ->with('products', $products)
            ->with('keyword', $keyword);
    }

    public function navbarSearch(Request $request)
    {
        $keyword = trim((string) $request->input('keyword', $request->input('title', '')));
        if (mb_strlen($keyword) < 1) {
            return response()->json([]);
        }

        return response()->json(
            Product::where('name', 'like', '%'.$keyword.'%')
                ->orderBy('name')
                ->limit(10)
                ->get()
                ->map(fn ($product) => [
                    'id' => $product->id,
                    'title' => $product->name,
                    'price' => number_format((float) $product->price, 2),
                    'image' => $product->image_url,
                    'url' => url('/show/'.$product->id),
                    'admin_url' => url('/product/'.$product->id),
                    'edit_url' => url('/product/'.$product->id.'/edit'),
                ])
                ->values()
        );
    }

    public function categories()
    {
        $categories = Category::all();
        $products = Product::orderBy('created_at', 'DESC')->paginate(12);

        return view('frontend.category')
            ->with('categories', $categories)
            ->with('products', $products)
            ->with('selectedCategory', null);
    }

    public function getByCategory($id)
    {
        $categories = Category::all();
        $clean = str_replace(['-', '_'], ' ', strtolower($id));
        $collapsed = str_replace(['-', '_', ' '], '', strtolower($id));

        $selectedCategory = is_numeric($id)
            ? Category::find($id)
            : Category::where(DB::raw('LOWER(name)'), 'LIKE', "%{$clean}%")
                ->orWhere(DB::raw('LOWER(name)'), 'LIKE', "%{$id}%")
                ->orWhere(DB::raw("REPLACE(LOWER(name), ' ', '')"), 'LIKE', "%{$collapsed}%")
                ->first();

        $products = collect();

        if ($selectedCategory) {
            $products = Product::where('category_id', $selectedCategory->id)->paginate(12)->withQueryString();
        }

        return view('frontend.category')
            ->with('products', $products)
            ->with('categories', $categories)
            ->with('selectedCategory', $selectedCategory);
    }
}
