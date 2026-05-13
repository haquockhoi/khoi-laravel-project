<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        return view('categories.index');
    }

    public function data(Request $request)
    {
        $columns = [
            0 => 'id',
            1 => 'name',
            2 => 'slug',
            3 => 'description',
            4 => 'news_count',
            5 => 'status',
            6 => 'id',
        ];

        $totalData = Category::count();

        $query = Category::withCount('news');

        if (!empty($request->input('search.value'))) {
            $search = $request->input('search.value');

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $totalFiltered = $query->count();

        $orderColumnIndex = $request->input('order.0.column', 0);
        $orderColumn = $columns[$orderColumnIndex] ?? 'id';
        $orderDirection = $request->input('order.0.dir', 'desc');

        $categories = $query
            ->orderBy($orderColumn, $orderDirection)
            ->offset($request->input('start', 0))
            ->limit($request->input('length', 10))
            ->get();

        $data = [];

        foreach ($categories as $category) {
            $status = $category->status
                ? '<span class="badge bg-success">Active</span>'
                : '<span class="badge bg-secondary">Inactive</span>';

            $newsCount = '<span class="badge bg-primary">' . $category->news_count . ' news</span>';

            $action = '
                <a href="' . route('categories.edit', $category) . '" class="btn btn-warning btn-sm">
                    Edit
                </a>

                <form action="' . route('categories.destroy', $category) . '"
                      method="POST"
                      class="d-inline delete-category-form"
                      data-id="' . $category->id . '"
                      data-name="' . e($category->name) . '">
                    <input type="hidden" name="_token" value="' . csrf_token() . '">
                    <input type="hidden" name="_method" value="DELETE">

                    <button type="submit" class="btn btn-danger btn-sm">
                        Delete
                    </button>
                </form>
            ';

            $data[] = [
                'id' => $category->id,
                'name' => '<strong>' . e($category->name) . '</strong>',
                'slug' => e($category->slug),
                'description' => e($category->description ?? '-'),
                'news_count' => $newsCount,
                'status' => $status,
                'action' => $action,
            ];
        }

        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $totalData,
            'recordsFiltered' => $totalFiltered,
            'data' => $data,
        ]);
    }

    public function create()
    {
        return view('categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string',
            'status' => 'nullable|boolean',
        ]);

        $category = Category::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'status' => $request->has('status'),
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Tạo danh mục tin tức thành công!',
                'redirect' => route('categories.index'),
                'data' => $category,
            ]);
        }

        return redirect()
            ->route('categories.index')
            ->with('success', 'Tạo danh mục tin tức thành công!');
    }

    public function edit(Category $category)
    {
        return view('categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'description' => 'nullable|string',
            'status' => 'nullable|boolean',
        ]);

        $category->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'status' => $request->has('status'),
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Cập nhật danh mục tin tức thành công!',
                'redirect' => route('categories.index'),
                'data' => $category,
            ]);
        }

        return redirect()
            ->route('categories.index')
            ->with('success', 'Cập nhật danh mục tin tức thành công!');
    }

    public function destroy(Category $category, Request $request)
    {
        $category->delete();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Xoá danh mục tin tức thành công!',
            ]);
        }

        return redirect()
            ->route('categories.index')
            ->with('success', 'Xoá danh mục tin tức thành công!');
    }
}