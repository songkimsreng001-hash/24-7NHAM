<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::all();

        return view('category.index')->with('categories', $categories);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('category.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
            'description' => 'required|max:1000',
        ]);
        // Create The Category
        $category = new Category;
        $category->name = $request->name;
        $category->description = $request->description;
        $category->save();
        Session::flash('category_create', 'Category is created.');

        return redirect('/category/create');
    }

    /**
     * Store a newly created resource in storage.
     */

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $category = Category::find($id);

        return view('category.show')->with('category', $category);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $category = Category::find($id);

        return view('category.edit')->with('category', $category);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255|min:3',
            'description' => 'required|max:1000|min:3',
        ]);
        if ($validator->fails()) {
            return redirect('category/'.$id.'/edit')
                ->withInput()
                ->withErrors($validator);
        }
        // Create The Category
        $category = Category::find($id);
        $category->name = $request->Input('name');
        $category->description = $request->Input('description');
        $category->save();
        Session::flash('category_update', 'Category is updated.');

        return redirect('category/'.$id.'/edit');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $category = Category::find($id);
        $category->delete();
        Session::flash('category_delete', 'Category is deleted.');

        return redirect('category');
    }
}
