<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryStoreRequest;
use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::all();
        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CategoryStoreRequest $request)
    {
        // Validate the request (CategoryStoreRequest should already handle validation)
    
    // Handle the image upload
    $imagePath = null;
    if ($request->hasFile('image')) {
        // Store the image in the 'categories' folder inside the 'public' disk
        $imagePath = $request->file('image')->store('categories', 'public');
    }

    // Create the category
    Category::create([
        'name' => $request->name,
        'description' => $request->description,
        'image' => $imagePath,  // Store the image path in the 'image' column
    ]);

    return to_route('admin.categories.index')->with('success', 'Category created successfully.');
        
    
    
   
}

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        // Validate the request
        $request->validate([
            'name' => 'required',
            'description' => 'required'
        ]);
    
        // Handle the image upload
        $imagePath = $category->image;  // Keep the old image path if no new image is uploaded
        if ($request->hasFile('image')) {
            // Delete the old image if it exists
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }
            
            // Store the new image in the 'categories' folder inside the 'public' disk
            $imagePath = $request->file('image')->store('categories', 'public');
        }
    
        // Update the category
        $category->update([
            'name' => $request->name,
            'description' => $request->description,
            'image' => $imagePath,  // Update with the new image path or keep the old one
        ]);
    
        return to_route('admin.categories.index')->with('success', 'Category updated successfully.');
    }
    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        Storage::delete($category->image);
        $category->delete();
        return to_route('admin.categories.index')->with('danger', 'Category deleted successfully.');
    }
}
