@extends('layouts.skote')

@section('title', 'Create User Group | Skote')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Create User Group</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('user-groups.index') }}">User Groups</a>
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
    <div class="col-lg-7">
        <div class="card">
            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="card-title mb-0">Group Information</h4>

                    <a href="{{ route('user-groups.index') }}" class="btn btn-secondary">
                        Back
                    </a>
                </div>

                <form action="{{ route('user-groups.store') }}" method="POST" id="user-group-create-form">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Group Name</label>
                        <input type="text"
                               name="name"
                               class="form-control"
                               placeholder="Ví dụ: Admin, User, Staff"
                               required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description"
                                  class="form-control"
                                  rows="4"
                                  placeholder="Mô tả nhóm user"></textarea>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox"
                                   name="is_fullaccess"
                                   value="1"
                                   class="form-check-input"
                                   id="is_fullaccess">

                            <label class="form-check-label" for="is_fullaccess">
                                Full Access
                            </label>
                        </div>

                        <small class="text-muted">
                            Nếu bật, nhóm này sẽ bỏ qua kiểm tra phân quyền và được truy cập toàn bộ chức năng.
                        </small>
                    </div>

                    <button type="submit" class="btn btn-primary" id="submit-btn">
                        Save Group
                    </button>

                    <a href="{{ route('user-groups.index') }}" class="btn btn-light">
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

<script>
    const form = document.getElementById('user-group-create-form');
    const submitBtn = document.getElementById('submit-btn');
    const errorBox = document.getElementById('ajax-error-box');
    const errorList = document.getElementById('ajax-error-list');

    form.addEventListener('submit', function (event) {
        event.preventDefault();

        errorBox.classList.add('d-none');
        errorList.innerHTML = '';

        submitBtn.disabled = true;
        submitBtn.innerText = 'Saving...';

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
                    text: 'Không thể tạo nhóm user. Vui lòng thử lại.',
                    icon: 'error',
                    width: '360px'
                });
            }
        })
        .finally(function () {
            submitBtn.disabled = false;
            submitBtn.innerText = 'Save Group';
        });
    });
</script>
@endsection