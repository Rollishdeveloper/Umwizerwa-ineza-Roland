<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('courses')->latest()->paginate(15);
        return view('admin.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $category = Category::create($validated);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'activity' => "Created category {$category->category_name}"
        ]);

        return back()->with('success', 'Category created successfully.');
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'category_name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $category->update($validated);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'activity' => "Updated category {$category->category_name}"
        ]);

        return back()->with('success', 'Category updated successfully.');
    }

    public function destroy(Category $category)
    {
        if ($category->courses()->count() > 0) {
            return back()->with('error', 'Cannot delete category with existing courses.');
        }
        $category->delete();
        return back()->with('success', 'Category deleted successfully.');
    }
}
