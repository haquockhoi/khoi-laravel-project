@extends('layouts.skote')

@section('title', 'News | Skote')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">News Management</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active">News</li>
                </ol>
            </div>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

<div class="card">
    <div class="card-body">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="card-title mb-0">News List</h4>

            <a href="{{ route('news.create') }}" class="btn btn-success">
                + New News
            </a>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead>
                    <tr>
                        <th style="width: 70px;">#</th>
                        <th>Title</th>
                        <th>Categories</th>
                        <th>Author</th>
                        <th>Status</th>
                        <th>Created At</th>
                        <th style="width: 220px;">Action</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($newsList as $news)
                        <tr>
                            <td>{{ $news->id }}</td>

                            <td>
                                <strong>{{ $news->title }}</strong>
                                <br>
                                <small class="text-muted">{{ $news->slug }}</small>
                            </td>

                            <td>
                                @forelse($news->categories as $category)
                                    <span class="badge bg-info">
                                        {{ $category->name }}
                                    </span>
                                @empty
                                    <span class="text-muted">No category</span>
                                @endforelse
                            </td>

                            <td>
                                {{ $news->creator->name ?? '-' }}
                            </td>

                            <td>
                                @if($news->status)
                                    <span class="badge bg-success">Published</span>
                                @else
                                    <span class="badge bg-secondary">Draft</span>
                                @endif
                            </td>

                            <td>
                                {{ $news->created_at?->format('d/m/Y H:i') }}
                            </td>

                            <td>
                                <a href="{{ route('news.edit', $news) }}" class="btn btn-warning btn-sm">
                                    Edit
                                </a>

                                <form action="{{ route('news.destroy', $news) }}"
                                      method="POST"
                                      class="d-inline delete-form"
                                      data-name="{{ $news->title }}">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit" class="btn btn-danger btn-sm">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">
                                Chưa có bài viết tin tức nào.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</div>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    .swal2-popup.swal-small-popup {
        border-radius: 14px !important;
    }

    .swal-small-title {
        font-size: 24px !important;
        font-weight: 700 !important;
        margin-bottom: 8px !important;
    }

    .swal-small-text {
        font-size: 15px !important;
        line-height: 1.5 !important;
    }

    .swal-small-btn {
        font-size: 14px !important;
        padding: 8px 16px !important;
        border-radius: 8px !important;
        min-width: 90px;
    }

    .swal2-icon {
        width: 64px !important;
        height: 64px !important;
        margin: 18px auto 10px !important;
    }

    .swal2-icon-content {
        font-size: 42px !important;
    }
</style>

<script>
    document.querySelectorAll('.delete-form').forEach(function (form) {
        form.addEventListener('submit', function (event) {
            event.preventDefault();

            const newsTitle = form.getAttribute('data-name') || 'bài viết này';

            Swal.fire({
                title: 'Xác nhận xoá?',
                text: 'Bạn có chắc muốn xoá bài viết "' + newsTitle + '" không?',
                icon: 'warning',
                width: '400px',
                padding: '1rem',
                showCancelButton: true,
                confirmButtonText: 'Có, xoá ngay',
                cancelButtonText: 'Huỷ',
                buttonsStyling: false,
                customClass: {
                    popup: 'swal-small-popup',
                    title: 'swal-small-title',
                    htmlContainer: 'swal-small-text',
                    confirmButton: 'swal-small-btn btn btn-danger me-2',
                    cancelButton: 'swal-small-btn btn btn-secondary'
                }
            }).then(function (result) {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>
@endsection