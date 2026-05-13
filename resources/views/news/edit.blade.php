@extends('layouts.skote')

@section('title', 'Edit News | Skote')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Edit News</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('news.index') }}">News</a>
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
    <div class="col-12 col-xl-10">
        <div class="card">
            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="card-title mb-0">News Information</h4>

                    <a href="{{ route('news.index') }}" class="btn btn-secondary">
                        Back
                    </a>
                </div>

                <div id="loading-box" class="alert alert-info">
                    Đang tải dữ liệu bài viết...
                </div>

                <form action="{{ route('news.update', $news) }}"
                      method="POST"
                      id="news-edit-form"
                      class="d-none">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input type="text"
                               name="title"
                               id="title"
                               class="form-control"
                               required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Categories</label>

                        <select name="categories[]"
                                id="categories"
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
                               id="thumbnail"
                               class="form-control"
                               placeholder="Dán link ảnh đại diện bài viết nếu có">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Summary</label>
                        <textarea name="summary"
                                  id="summary"
                                  class="form-control"
                                  rows="3"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Content</label>
                        <textarea name="content"
                                  id="content_editor"
                                  class="form-control"
                                  rows="10"></textarea>
                    </div>

                    <div class="mb-4">
                        <label class="form-label d-block">Status</label>

                        <label class="custom-status-switch">
                            <input type="checkbox"
                                   name="status"
                                   value="1"
                                   id="status">

                            <span class="custom-status-slider"></span>
                            <span class="custom-status-text">Published</span>
                        </label>

                        <small class="text-muted d-block mt-2">
                            Bật Published để bài viết được hiển thị.
                        </small>
                    </div>

                    <button type="submit" class="btn btn-primary" id="submit-btn">
                        Update News
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

    const loadNewsUrl = '{{ route('news.showAjax', $news->id) }}';

    const form = document.getElementById('news-edit-form');
    const submitBtn = document.getElementById('submit-btn');
    const errorBox = document.getElementById('ajax-error-box');
    const errorList = document.getElementById('ajax-error-list');
    const loadingBox = document.getElementById('loading-box');

    const titleInput = document.getElementById('title');
    const categoriesInput = $('#categories');
    const thumbnailInput = document.getElementById('thumbnail');
    const summaryInput = document.getElementById('summary');
    const statusInput = document.getElementById('status');

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
            loadNewsData();
        })
        .catch(function (error) {
            console.error(error);
        });

    function loadNewsData() {
        fetch(loadNewsUrl, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(async function (response) {
            const data = await response.json();

            if (!response.ok) {
                throw data;
            }

            return data;
        })
        .then(function (response) {
            const news = response.data;

            titleInput.value = news.title ?? '';
            thumbnailInput.value = news.thumbnail ?? '';
            summaryInput.value = news.summary ?? '';
            statusInput.checked = news.status == 1 || news.status === true;

            categoriesInput.val(news.categories ?? []).trigger('change');

            if (newsEditor) {
                newsEditor.setData(news.content ?? '');
            }

            loadingBox.classList.add('d-none');
            form.classList.remove('d-none');
        })
        .catch(function () {
            loadingBox.classList.add('d-none');

            Swal.fire({
                title: 'Lỗi!',
                text: 'Không thể tải dữ liệu bài viết.',
                icon: 'error',
                width: '360px'
            });
        });
    }

    form.addEventListener('submit', function (event) {
        event.preventDefault();

        errorBox.classList.add('d-none');
        errorList.innerHTML = '';

        submitBtn.disabled = true;
        submitBtn.innerText = 'Updating...';

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
                    text: 'Không thể cập nhật bài viết. Vui lòng thử lại.',
                    icon: 'error',
                    width: '360px'
                });
            }
        })
        .finally(function () {
            submitBtn.disabled = false;
            submitBtn.innerText = 'Update News';
        });
    });
</script>
@endsection