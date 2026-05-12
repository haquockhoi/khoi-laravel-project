<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class NewsController extends Controller
{
    public function index()
    {
        $newsList = News::with(['categories', 'creator'])
            ->latest()
            ->get();

        return view('news.index', compact('newsList'));
    }

    public function create()
    {
        $categories = Category::where('status', true)
            ->orderBy('name')
            ->get();

        return view('news.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255|unique:news,title',
            'summary' => 'nullable|string',
            'content' => 'nullable|string',
            'thumbnail' => 'nullable|string|max:255',
            'status' => 'nullable|boolean',
            'categories' => 'required|array',
            'categories.*' => 'exists:categories,id',
        ]);

        $news = News::create([
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'summary' => $request->summary,
            'content' => $request->content,
            'thumbnail' => $request->thumbnail,
            'status' => $request->has('status'),
            'created_by' => Auth::id(),
        ]);

        $news->categories()->sync($request->categories);

        return redirect()
            ->route('news.index')
            ->with('success', 'Tạo bài viết tin tức thành công!');
    }

    public function edit(News $news)
    {
        $categories = Category::where('status', true)
            ->orderBy('name')
            ->get();

        $selectedCategories = $news->categories()
            ->pluck('categories.id')
            ->toArray();

        return view('news.edit', compact(
            'news',
            'categories',
            'selectedCategories'
        ));
    }

    public function update(Request $request, News $news)
    {
        $request->validate([
            'title' => 'required|string|max:255|unique:news,title,' . $news->id,
            'summary' => 'nullable|string',
            'content' => 'nullable|string',
            'thumbnail' => 'nullable|string|max:255',
            'status' => 'nullable|boolean',
            'categories' => 'required|array',
            'categories.*' => 'exists:categories,id',
        ]);

        $news->update([
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'summary' => $request->summary,
            'content' => $request->content,
            'thumbnail' => $request->thumbnail,
            'status' => $request->has('status'),
        ]);

        $news->categories()->sync($request->categories);

        return redirect()
            ->route('news.index')
            ->with('success', 'Cập nhật bài viết tin tức thành công!');
    }

    public function destroy(News $news)
    {
        $news->delete();

        return redirect()
            ->route('news.index')
            ->with('success', 'Xoá bài viết tin tức thành công!');
    }
}