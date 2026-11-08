<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Product::with('category')->latest();

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $products = $query->get();
        $categories = Category::withCount('products')->orderBy('name')->get();

        return view('product.index', compact('products', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::orderBy('name')->pluck('name', 'id')->toArray();

        return view('product.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100|min:2',
            'category_id' => 'required|integer|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'image' => 'required|image|mimes:jpg,jpeg,png,gif,webp,JPG,JPEG,PNG,GIF,WEBP|max:5120',
            'description' => 'required|string|max:1000|min:5',
        ]);

        if ($validator->fails()) {
            return redirect()->route('product.create')
                ->withInput()
                ->withErrors($validator);
        }

        // Upload Product Image
        $image = $request->file('image');
        $uploadDir = public_path('img/');
        if (!file_exists($uploadDir)) {
            @mkdir($uploadDir, 0755, true);
        }
        $extension = $image->getClientOriginalExtension() ?: 'png';
        $filename = time() . '_' . uniqid() . '.' . $extension;
        $image->move($uploadDir, $filename);

        $product = new Product;
        $product->name = $request->input('name');
        $product->category_id = $request->input('category_id');
        $product->price = $request->input('price');
        $product->image = $filename;
        $product->description = $request->input('description');
        $product->save();

        Session::flash('product_create', 'Product "' . $product->name . '" has been created successfully!');

        return redirect()->route('product.index');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $product = Product::with('category')->findOrFail($id);

        return view('product.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $product = Product::with('category')->findOrFail($id);
        $categories = Category::orderBy('name')->pluck('name', 'id')->toArray();

        return view('product.edit', compact('product', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100|min:2',
            'category_id' => 'required|integer|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp,JPG,JPEG,PNG,GIF,WEBP|max:5120',
            'description' => 'required|string|max:1000|min:5',
        ]);

        if ($validator->fails()) {
            return redirect()->route('product.edit', $id)
                ->withInput()
                ->withErrors($validator);
        }

        // Handle Image Replacement
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $uploadDir = public_path('img/');
            if (!file_exists($uploadDir)) {
                @mkdir($uploadDir, 0755, true);
            }
            $extension = $image->getClientOriginalExtension() ?: 'png';
            $filename = time() . '_' . uniqid() . '.' . $extension;
            $image->move($uploadDir, $filename);

            // Clean up old image if exists locally
            if ($product->image && file_exists(public_path('img/' . $product->image))) {
                @unlink(public_path('img/' . $product->image));
            }

            $product->image = $filename;
        }

        $product->name = $request->input('name');
        $product->category_id = $request->input('category_id');
        $product->price = $request->input('price');
        $product->description = $request->input('description');
        $product->save();

        Session::flash('product_update', 'Product "' . $product->name . '" has been updated successfully!');

        return redirect()->route('product.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        if ($product->image && file_exists(public_path('img/' . $product->image))) {
            @unlink(public_path('img/' . $product->image));
        }
        $name = $product->name;
        $product->delete();
        Session::flash('product_delete', 'Product "' . $name . '" has been deleted.');

        return redirect()->route('product.index');
    }
}
