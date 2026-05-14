<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class NewsApiController extends Controller
{
    public function index(Request $request)
    {
        $query = News::with(['categories', 'creator'])
            ->latest();

        if ($request->filled('keyword')) {
            $keyword = $request->keyword;

            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                    ->orWhere('summary', 'like', "%{$keyword}%")
                    ->orWhere('content', 'like', "%{$keyword}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('categories.id', $request->category_id);
            });
        }

        $newsList = $query->paginate($request->get('per_page', 10));

        return response()->json([
            'success' => true,
            'message' => 'Lấy danh sách bài viết thành công.',
            'data' => $newsList,
        ], 200, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    public function show(News $news)
    {
        $news->load(['categories', 'creator']);

        return response()->json([
            'success' => true,
            'message' => 'Lấy chi tiết bài viết thành công.',
            'data' => $news,
        ], 200, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => ['required', 'string', 'max:255', 'unique:news,title'],
            'summary' => ['nullable', 'string'],
            'content' => ['nullable', 'string'],
            'thumbnail' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'boolean'],
            'categories' => ['required', 'array'],
            'categories.*' => ['exists:categories,id'],
        ]);

        $news = News::create([
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'summary' => $request->summary,
            'content' => $request->content,
            'thumbnail' => $request->thumbnail,
            'status' => $request->boolean('status'),
            'created_by' => Auth::id(),
        ]);

        $news->categories()->sync($request->categories);

        $news->load(['categories', 'creator']);

        return response()->json([
            'success' => true,
            'message' => 'Tạo bài viết thành công.',
            'data' => $news,
        ], 201, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    public function update(Request $request, News $news)
    {
        $request->validate([
            'title' => ['required', 'string', 'max:255', 'unique:news,title,' . $news->id],
            'summary' => ['nullable', 'string'],
            'content' => ['nullable', 'string'],
            'thumbnail' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'boolean'],
            'categories' => ['required', 'array'],
            'categories.*' => ['exists:categories,id'],
        ]);

        $news->update([
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'summary' => $request->summary,
            'content' => $request->content,
            'thumbnail' => $request->thumbnail,
            'status' => $request->boolean('status'),
        ]);

        $news->categories()->sync($request->categories);

        $news->load(['categories', 'creator']);

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật bài viết thành công.',
            'data' => $news,
        ], 200, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    public function destroy(News $news)
    {
        $news->categories()->detach();
        $news->delete();

        return response()->json([
            'success' => true,
            'message' => 'Xoá bài viết thành công.',
        ], 200, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
}