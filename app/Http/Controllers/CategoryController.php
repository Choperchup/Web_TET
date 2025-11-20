<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        // Logic to retrieve and display all categories
        $categories = Category::all();
        return view('categories.index', compact('categories'));
    }

    public function create()
    {
        // Logic to show the form for creating a new category
        return view('categories.create');
    }

    public function store(StoreCategoryRequest $request)
    {
        // Logic to store a new category
        $category = Category::create([
            'name' => $request->get('name'),
            'description' => $request->get('description'),
        ]);

        return redirect()->route('categories.index')
            ->with('success', 'Category created successfully.');
    }

    public function edit($id)
    {
        // Logic to show the form for editing an existing category
        $category = Category::findOrFail($id);
        return view('categories.edit', compact('category'));
    }

    public function update(StoreCategoryRequest $request, $id)
    {
        // Logic to update an existing category
        $category = Category::findOrFail($id);
        $category->update([
            'name' => $request->get('name'),
            'description' => $request->get('description'),
        ]);

        return redirect()->route('categories.index')
            ->with('success', 'Category updated successfully.');
    }

    public function destroy($id)
    {
        // Logic to delete a category
        $category = Category::findOrFail($id);
        $category->delete();

        return back()->with('success', 'Category deleted successfully.');
    }

    public function destroyAll()
    {
        // Logic to delete all categories
        Category::truncate();

        return back()->with('success', 'All categories deleted successfully.');
    }
}
