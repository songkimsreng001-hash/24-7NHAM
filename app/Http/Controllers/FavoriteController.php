<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    public function index()
    {
        $favorites = Auth::user()->favorites()->with('product.category')->paginate(12);

        return view('frontend.favorites.index', compact('favorites'));
    }

    public function toggle($productId)
    {
        $user = Auth::user();
        $product = Product::findOrFail($productId);

        $fav = Favorite::where('user_id', $user->id)->where('product_id', $product->id)->first();

        if ($fav) {
            $fav->delete();
            $favorited = false;
            $msg = 'Removed from favorites.';
        } else {
            Favorite::create(['user_id' => $user->id, 'product_id' => $product->id]);
            $favorited = true;
            $msg = 'Added to favorites!';
        }

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'favorited' => $favorited,
                'message' => $msg,
            ]);
        }

        return redirect()->back()->with('success', $msg);
    }
}
