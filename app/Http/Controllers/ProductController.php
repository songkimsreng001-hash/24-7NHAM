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

        $product = new Product;
        $product->name = $request->input('name');
        $product->category_id = $request->input('category_id');
        $product->price = $request->input('price');
        $product->image = $this->storeUploadedImage($request->file('image'));
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
            // Clean up old image if exists locally and is not a Data URI
            if ($product->image && !str_starts_with($product->image, 'data:image') && file_exists(public_path('img/' . $product->image))) {
                @unlink(public_path('img/' . $product->image));
            }

            $product->image = $this->storeUploadedImage($request->file('image'));
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
        if ($product->image && !str_starts_with($product->image, 'data:image') && file_exists(public_path('img/' . $product->image))) {
            @unlink(public_path('img/' . $product->image));
        }
        $name = $product->name;
        $product->delete();
        Session::flash('product_delete', 'Product "' . $name . '" has been deleted.');

        return redirect()->route('product.index');
    }

    /**
     * Store uploaded image to public/img if writable, or as a Data URI on serverless environments.
     */
    protected function storeUploadedImage($uploadedFile): string
    {
        $uploadDir = public_path('img/');

        // If local public directory is writable (Local / VPS), save as file
        if (@is_writable($uploadDir) || (!file_exists($uploadDir) && @mkdir($uploadDir, 0755, true) && @is_writable($uploadDir))) {
            try {
                $extension = strtolower($uploadedFile->getClientOriginalExtension() ?: 'png');
                $filename = time() . '_' . uniqid() . '.' . $extension;
                $uploadedFile->move($uploadDir, $filename);

                return $filename;
            } catch (\Throwable $e) {
                // Fall back to Data URI if disk write fails
            }
        }

        // On Serverless platforms (e.g. Vercel) where filesystem is read-only:
        // Convert to Base64 Data URI so it persists in the database without needing local disk writes
        $mime = $uploadedFile->getMimeType() ?: 'image/jpeg';
        $content = file_get_contents($uploadedFile->getRealPath());

        return 'data:' . $mime . ';base64,' . base64_encode($content);
    }
}
