@extends('layouts.skote')

@section('title', 'Create News | Skote')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Create News</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('news.index') }}">News</a>
                    </li>
                    <li class="breadcrumb-item active">Create</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div id="ajax-error-box" class="alert alert-danger d-none">
    <ul class="mb-0" id="ajax-error-list"></ul>
</div>

<div class="row justify-content-center">
    <div class="col-12 col-xl-10">
        <div class="card">
            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="card-title mb-0">News Information</h4>

                    <a href="{{ route('news.index') }}" class="btn btn-secondary">
                        Back
                    </a>
                </div>

                <form action="{{ route('news.store') }}" method="POST" id="news-create-form">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input type="text"
                               name="title"
                               class="form-control"
                               placeholder="Nhập tiêu đề bài viết"
                               required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Categories</label>

                        <select name="categories[]"
                                class="form-control select2 w-100"
                                multiple="multiple"
                                required>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>

                        <small class="text-muted">
                            Có thể chọn nhiều danh mục cho một bài viết.
                        </small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Image URL</label>
                        <input type="text"
                               name="thumbnail"
                               class="form-control"
                               placeholder="Dán link ảnh đại diện bài viết nếu có">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Summary</label>
                        <textarea name="summary"
                                  class="form-control"
                                  rows="3"
                                  placeholder="Tóm tắt ngắn bài viết"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Content</label>
                        <textarea name="content"
                                  id="content_editor"
                                  class="form-control"
                                  rows="10"
                                  placeholder="Nội dung bài viết"></textarea>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox"
                                   name="status"
                                   value="1"
                                   class="form-check-input"
                                   id="status"
                                   checked>

                            <label class="form-check-label" for="status">
                                Published
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary" id="submit-btn">
                        Save News
                    </button>

                    <a href="{{ route('news.index') }}" class="btn btn-light">
                        Cancel
                    </a>
                </form>

            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<link href="{{ asset('assets/libs/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />

<style>
    .select2-container {
        width: 100% !important;
    }

    .select2-selection--multiple {
        width: 100% !important;
        min-height: 38px;
        border: 1px solid #ced4da !important;
        border-radius: 0.25rem !important;
    }

    .select2-selection__rendered {
        width: 100% !important;
    }

    .ck-editor {
        width: 100% !important;
    }

    .ck-editor__editable {
        min-height: 180px;
    }

    @media (max-width: 768px) {
        .card-body {
            padding: 16px;
        }

        .page-content {
            padding-left: 12px;
            padding-right: 12px;
        }
    }
</style>

<script src="{{ asset('assets/libs/select2/js/select2.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>

<script>
    let newsEditor = null;

    $(document).ready(function () {
        $('.select2').select2({
            placeholder: 'Chọn danh mục',
            allowClear: true,
            width: '100%'
        });
    });

    ClassicEditor
        .create(document.querySelector('#content_editor'), {
            toolbar: [
                'heading',
                '|',
                'bold',
                'italic',
                'link',
                'bulletedList',
                'numberedList',
                '|',
                'outdent',
                'indent',
                '|',
                'blockQuote',
                'insertTable',
                'undo',
                'redo'
            ]
        })
        .then(function (editor) {
            newsEditor = editor;
        })
        .catch(function (error) {
            console.error(error);
        });

    const form = document.getElementById('news-create-form');
    const submitBtn = document.getElementById('submit-btn');
    const errorBox = document.getElementById('ajax-error-box');
    const errorList = document.getElementById('ajax-error-list');

    form.addEventListener('submit', function (event) {
        event.preventDefault();

        errorBox.classList.add('d-none');
        errorList.innerHTML = '';

        submitBtn.disabled = true;
        submitBtn.innerText = 'Saving...';

        const formData = new FormData(form);

        if (newsEditor) {
            formData.set('content', newsEditor.getData());
        }

        fetch(form.getAttribute('action'), {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: formData
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
                    text: 'Không thể tạo bài viết. Vui lòng thử lại.',
                    icon: 'error',
                    width: '360px'
                });
            }
        })
        .finally(function () {
            submitBtn.disabled = false;
            submitBtn.innerText = 'Save News';
        });
    });
</script>
@endsection