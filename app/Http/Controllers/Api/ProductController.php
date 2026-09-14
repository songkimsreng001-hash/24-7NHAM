<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Throwable;

class ProductController extends Controller
{
    /**
     * List all products with optional category filter and search.
     */
    public function index(Request $request)
    {
        try {
            $query = Product::with('category');

            if ($request->filled('category_id')) {
                $query->where('category_id', $request->category_id);
            }

            if ($request->filled('search')) {
                $query->where('name', 'like', '%'.$request->search.'%');
            }

            $products = $query->orderBy('created_at', 'desc')->get();

            // Append full image URL so Flutter can load it directly
            $products->transform(function ($product) {
                $product->image_url = $product->image
                    ? asset('img/'.$product->image)
                    : null;

                return $product;
            });

            return response()->json([
                'message' => 'Products fetched',
                'products' => $products,
            ]);

        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Failed to fetch products',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show a single product.
     */
    public function show($id)
    {
        try {
            $product = Product::with('category')->findOrFail($id);
            $product->image_url = $product->image
                ? asset('img/'.$product->image)
                : null;

            return response()->json([
                'message' => 'Product fetched',
                'product' => $product,
            ]);

        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Product not found',
                'error' => $e->getMessage(),
            ], 404);
        }
    }

    /**
     * List all categories (useful for Flutter filter UI).
     */
    public function categories()
    {
        return response()->json([
            'categories' => Category::all(),
        ]);
    }
}
