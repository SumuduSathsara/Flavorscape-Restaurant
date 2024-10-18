<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MenuStoreRequest;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Menu;
use Illuminate\Support\Facades\Storage;

class MenuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $menus = Menu::all();
        return view('admin.menus.index', compact('menus'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();
        return view('admin.menus.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(MenuStoreRequest $request)
{
    // Validate the request (MenuStoreRequest should already handle validation)

    // Handle the image upload
    $imagePath = null;
    if ($request->hasFile('image')) {
        // Store the image in the 'menus' folder inside the 'public' disk
        $imagePath = $request->file('image')->store('menus', 'public');
    }

    // Create the menu
    $menu = Menu::create([
        'name' => $request->name,
        'description' => $request->description,
        'image' => $imagePath,  // Store the image path in the 'image' column
        'price' => $request->price
    ]);

    // Attach categories to the menu, if provided
    if ($request->has('categories')) {
        $menu->categories()->attach($request->categories);
    }

    return to_route('admin.menus.index')->with('success', 'Menu created successfully.');
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
    public function edit(Menu $menu)
    {
        $categories = Category::all();
        return view('admin.menus.edit', compact('menu','categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Menu $menu)
    {
        // Validate the request
        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'price' => 'required|numeric'
        ]);
    
        // Handle the image upload
        $imagePath = $menu->image;  // Retain the old image if no new one is uploaded
        if ($request->hasFile('image')) {
            // Delete the old image from storage
            if ($menu->image) {
                Storage::disk('public')->delete($menu->image);
            }
    
            // Store the new image in the 'menus' folder inside the 'public' disk
            $imagePath = $request->file('image')->store('menus', 'public');
        }
    
        // Update the menu
        $menu->update([
            'name' => $request->name,
            'description' => $request->description,
            'image' => $imagePath,  // Use the new image path, or keep the old one
            'price' => $request->price,
        ]);
    
        // Sync categories if provided
        if ($request->has('categories')) {
            $menu->categories()->sync($request->categories);
        }
    
        return to_route('admin.menus.index')->with('success', 'Menu updated successfully.');
    }
    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Menu $menu)
    {
        Storage::delete($menu->image);
        $menu->categories()->detach();
        $menu->delete();
        return to_route('admin.menus.index')->with('danger', 'Menu deleted successfully.');

    }
}
