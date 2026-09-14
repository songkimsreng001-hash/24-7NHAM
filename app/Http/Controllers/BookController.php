<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class BookController extends Controller
{
    public function index()
    {
        $books = Book::all();

        return view('book.index')
            ->with('books', $books);
    }

    public function create()
    {
        return view('book.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'Title' => 'required|min:3|max:255',
            'Author' => 'required|min:3|max:150',
            'ISBN' => 'required|unique:books,ISBN',
            'PublishYear' => 'required|digits:4',
        ]);

        if ($validator->fails()) {
            return redirect('book/create')
                ->withInput()
                ->withErrors($validator);
        }

        $book = new Book;

        $book->Title = $request->Title;
        $book->Author = $request->Author;
        $book->ISBN = $request->ISBN;
        $book->PublishYear = $request->PublishYear;

        $book->save();

        Session::flash('book_create', 'New book created successfully.');

        return redirect('book/create');
    }

    public function show($id)
    {
        $book = Book::findOrFail($id);

        return view('book.show')
            ->with('book', $book);
    }

    public function edit($id)
    {
        $book = Book::findOrFail($id);

        return view('book.edit')
            ->with('book', $book);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'Title' => 'required|min:3|max:255',
            'Author' => 'required|min:3|max:150',
            'ISBN' => 'required',
            'PublishYear' => 'required|digits:4',
        ]);

        if ($validator->fails()) {
            return redirect('book/'.$id.'/edit')
                ->withInput()
                ->withErrors($validator);
        }

        $book = Book::findOrFail($id);

        $book->Title = $request->Title;
        $book->Author = $request->Author;
        $book->ISBN = $request->ISBN;
        $book->PublishYear = $request->PublishYear;

        $book->save();

        Session::flash('book_update', 'Book updated successfully.');

        return redirect('book/'.$id.'/edit');
    }

    public function destroy($id)
    {
        $book = Book::findOrFail($id);

        $book->delete();

        Session::flash('book_delete', 'Book deleted successfully.');

        return redirect('book');
    }
}
