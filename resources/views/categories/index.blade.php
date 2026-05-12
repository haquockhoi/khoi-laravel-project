@extends('layouts.skote')

@section('title', 'Categories | Skote')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">News Categories</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active">Categories</li>
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
            <h4 class="card-title mb-0">Category List</h4>

            <a href="{{ route('categories.create') }}" class="btn btn-success">
                + New Category
            </a>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead>
                    <tr>
                        <th style="width: 70px;">#</th>
                        <th>Name</th>
                        <th>Slug</th>
                        <th>Description</th>
                        <th>News Count</th>
                        <th>Status</th>
                        <th style="width: 220px;">Action</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($categories as $category)
                        <tr>
                            <td>{{ $category->id }}</td>

                            <td>
                                <strong>{{ $category->name }}</strong>
                            </td>

                            <td>{{ $category->slug }}</td>

                            <td>{{ $category->description ?? '-' }}</td>

                            <td>
                                <span class="badge bg-primary">
                                    {{ $category->news_count }} news
                                </span>
                            </td>

                            <td>
                                @if($category->status)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>

                            <td>
                                <a href="{{ route('categories.edit', $category) }}" class="btn btn-warning btn-sm">
                                    Edit
                                </a>

                                <form action="{{ route('categories.destroy', $category) }}"
                                      method="POST"
                                      class="d-inline delete-form"
                                      data-name="{{ $category->name }}">
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
                                Chưa có danh mục tin tức nào.
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

            const categoryName = form.getAttribute('data-name') || 'danh mục này';

            Swal.fire({
                title: 'Xác nhận xoá?',
                text: 'Bạn có chắc muốn xoá danh mục "' + categoryName + '" không?',
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