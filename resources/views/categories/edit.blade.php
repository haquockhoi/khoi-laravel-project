@extends('layouts.skote')

@section('title', 'Edit Category | Skote')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Edit Category</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('categories.index') }}">Categories</a>
                    </li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div id="ajax-error-box" class="alert alert-danger d-none">
    <ul class="mb-0" id="ajax-error-list"></ul>
</div>

<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="card-title mb-0">Category Information</h4>

                    <a href="{{ route('categories.index') }}" class="btn btn-secondary">
                        Back
                    </a>
                </div>

                <form action="{{ route('categories.update', $category) }}" method="POST" id="category-edit-form">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label">Category Name</label>
                        <input type="text"
                               name="name"
                               class="form-control"
                               value="{{ old('name', $category->name) }}"
                               required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description"
                                  class="form-control"
                                  rows="4">{{ old('description', $category->description) }}</textarea>
                    </div>

                    <div class="mb-4">
    <label class="form-label d-block">Status</label>

    <label class="custom-status-switch">
        <input type="checkbox"
               name="status"
               value="1"
               id="status"
               {{ old('status', $category->status) ? 'checked' : '' }}>

        <span class="custom-status-slider"></span>
        <span class="custom-status-text">Active</span>
    </label>

    <small class="text-muted d-block mt-2">
        Bật Active để danh mục được hiển thị khi tạo bài viết tin tức.
    </small>
</div>

                    <button type="submit" class="btn btn-primary" id="submit-btn">
                        Update Category
                    </button>

                    <a href="{{ route('categories.index') }}" class="btn btn-light">
                        Cancel
                    </a>
                </form>

            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
    .custom-status-switch {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        cursor: pointer;
        user-select: none;
    }

    .custom-status-switch input {
        display: none;
    }

    .custom-status-slider {
        width: 52px;
        height: 28px;
        background: #adb5bd;
        border-radius: 999px;
        position: relative;
        transition: 0.2s ease-in-out;
        display: inline-block;
    }

    .custom-status-slider::before {
        content: "";
        width: 22px;
        height: 22px;
        background: #ffffff;
        border-radius: 50%;
        position: absolute;
        top: 3px;
        left: 3px;
        transition: 0.2s ease-in-out;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
    }

    .custom-status-switch input:checked + .custom-status-slider {
        background: #34c38f;
    }

    .custom-status-switch input:checked + .custom-status-slider::before {
        transform: translateX(24px);
    }

    .custom-status-text {
        font-weight: 600;
        color: #495057;
    }
</style>

<script>
    const form = document.getElementById('category-edit-form');
    const submitBtn = document.getElementById('submit-btn');
    const errorBox = document.getElementById('ajax-error-box');
    const errorList = document.getElementById('ajax-error-list');

    form.addEventListener('submit', function (event) {
        event.preventDefault();

        errorBox.classList.add('d-none');
        errorList.innerHTML = '';

        submitBtn.disabled = true;
        submitBtn.innerText = 'Updating...';

        fetch(form.getAttribute('action'), {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: new FormData(form)
        })
        .then(async function (response) {
            const data = await response.json();

            if (!response.ok) {
                throw data;
            }

            return data;
        })
        .then(function (data) {
            Swal.fire({
                title: 'Thành công!',
                text: data.message,
                icon: 'success',
                width: '360px',
                timer: 1200,
                showConfirmButton: false
            });

            setTimeout(function () {
                window.location.href = data.redirect;
            }, 1200);
        })
        .catch(function (error) {
            if (error.errors) {
                Object.values(error.errors).forEach(function (messages) {
                    messages.forEach(function (message) {
                        const li = document.createElement('li');
                        li.innerText = message;
                        errorList.appendChild(li);
                    });
                });

                errorBox.classList.remove('d-none');
                window.scrollTo({ top: 0, behavior: 'smooth' });
            } else {
                Swal.fire({
                    title: 'Lỗi!',
                    text: 'Không thể cập nhật danh mục. Vui lòng thử lại.',
                    icon: 'error',
                    width: '360px'
                });
            }
        })
        .finally(function () {
            submitBtn.disabled = false;
            submitBtn.innerText = 'Update Category';
        });
    });
</script>
@endsection