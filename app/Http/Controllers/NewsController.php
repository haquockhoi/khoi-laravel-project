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
        return view('news.index');
    }

    public function data(Request $request)
    {
        $columns = [
            0 => 'id',
            1 => 'title',
            2 => 'id',
            3 => 'created_by',
            4 => 'status',
            5 => 'created_at',
            6 => 'id',
        ];

        $totalData = News::count();

        $query = News::with(['categories', 'creator']);

        if (!empty($request->input('search.value'))) {
            $search = $request->input('search.value');

            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%")
                    ->orWhere('summary', 'like', "%{$search}%")
                    ->orWhereHas('categories', function ($categoryQuery) use ($search) {
                        $categoryQuery->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('creator', function ($creatorQuery) use ($search) {
                        $creatorQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        $totalFiltered = $query->count();

        $orderColumnIndex = $request->input('order.0.column', 0);
        $orderColumn = $columns[$orderColumnIndex] ?? 'id';
        $orderDirection = $request->input('order.0.dir', 'desc');

        if (in_array($orderColumn, ['id', 'title', 'status', 'created_at'])) {
            $query->orderBy($orderColumn, $orderDirection);
        } else {
            $query->latest();
        }

        $newsList = $query
            ->offset($request->input('start', 0))
            ->limit($request->input('length', 10))
            ->get();

        $data = [];

        foreach ($newsList as $news) {
            $categoriesHtml = '';

            if ($news->categories->count() > 0) {
                foreach ($news->categories as $category) {
                    $categoriesHtml .= '<span class="badge bg-info me-1">' . e($category->name) . '</span>';
                }
            } else {
                $categoriesHtml = '<span class="text-muted">No category</span>';
            }

            $status = $news->status
                ? '<span class="badge bg-success">Published</span>'
                : '<span class="badge bg-secondary">Draft</span>';

            $title = '
                <strong>' . e($news->title) . '</strong>
                <br>
                <small class="text-muted">' . e($news->slug) . '</small>
            ';

            $action = '
                <a href="' . route('news.edit', $news) . '" class="btn btn-warning btn-sm">
                    Edit
                </a>

                <form action="' . route('news.destroy', $news) . '"
                      method="POST"
                      class="d-inline delete-news-form"
                      data-id="' . $news->id . '"
                      data-name="' . e($news->title) . '">
                    <input type="hidden" name="_token" value="' . csrf_token() . '">
                    <input type="hidden" name="_method" value="DELETE">

                    <button type="submit" class="btn btn-danger btn-sm">
                        Delete
                    </button>
                </form>
            ';

            $data[] = [
                'id' => $news->id,
                'title' => $title,
                'categories' => $categoriesHtml,
                'author' => e($news->creator->name ?? '-'),
                'status' => $status,
                'created_at' => $news->created_at ? $news->created_at->format('d/m/Y H:i') : '-',
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

    public function showAjax(News $news)
    {
        $news->load('categories');

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $news->id,
                'title' => $news->title,
                'summary' => $news->summary,
                'content' => $news->content,
                'thumbnail' => $news->thumbnail,
                'status' => $news->status,
                'categories' => $news->categories->pluck('id')->toArray(),
            ],
        ]);
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

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Tạo bài viết tin tức thành công!',
                'redirect' => route('news.index'),
                'data' => $news,
            ]);
        }

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

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Cập nhật bài viết tin tức thành công!',
                'redirect' => route('news.index'),
                'data' => $news,
            ]);
        }

        return redirect()
            ->route('news.index')
            ->with('success', 'Cập nhật bài viết tin tức thành công!');
    }

    public function destroy(News $news, Request $request)
    {
        $news->categories()->detach();

        $news->delete();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Xoá bài viết thành công!',
            ]);
        }

        return redirect()
            ->route('news.index')
            ->with('success', 'Xoá bài viết thành công!');
    }
}