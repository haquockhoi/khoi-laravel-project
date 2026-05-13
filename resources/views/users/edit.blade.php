@extends('layouts.skote')

@section('title', 'Edit User | Skote')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Edit User</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('users.index') }}">Users</a>
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
                    <h4 class="card-title mb-0">Update User Information</h4>

                    <a href="{{ route('users.index') }}" class="btn btn-secondary">
                        Back
                    </a>
                </div>

                <div id="loading-box" class="alert alert-info">
                    Đang tải dữ liệu user...
                </div>

                <form action="{{ route('users.update', $user->id) }}"
                      method="POST"
                      id="user-edit-form"
                      class="d-none">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text"
                               name="name"
                               id="name"
                               class="form-control"
                               required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email"
                               name="email"
                               id="email"
                               class="form-control"
                               required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <select name="role" id="role" class="form-control">
                            <option value="user">User</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">User Group</label>
                        <select name="user_group_id" id="user_group_id" class="form-control">
                            <option value="">No group</option>

                            @foreach($userGroups as $group)
                                <option value="{{ $group->id }}">
                                    {{ $group->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label d-block">Status</label>

                        <label class="custom-status-switch">
                            <input type="checkbox"
                                   name="status"
                                   value="1"
                                   id="status">

                            <span class="custom-status-slider"></span>
                            <span class="custom-status-text">Active</span>
                        </label>

                        <small class="text-muted d-block mt-2">
                            Bật Active để tài khoản được hoạt động.
                        </small>
                    </div>

                    <hr>

                    <p class="text-muted">
                        Nếu không muốn đổi mật khẩu thì để trống 2 ô dưới.
                    </p>

                    <div class="mb-3">
                        <label class="form-label">New Password</label>
                        <input type="password"
                               name="password"
                               class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Confirm New Password</label>
                        <input type="password"
                               name="password_confirmation"
                               class="form-control">
                    </div>

                    <button type="submit" class="btn btn-primary" id="submit-btn">
                        Update User
                    </button>

                    <a href="{{ route('users.index') }}" class="btn btn-light">
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
    const userId = '{{ $user->id }}';
    const loadUserUrl = '{{ route('users.showAjax', $user->id) }}';

    const form = document.getElementById('user-edit-form');
    const submitBtn = document.getElementById('submit-btn');
    const errorBox = document.getElementById('ajax-error-box');
    const errorList = document.getElementById('ajax-error-list');
    const loadingBox = document.getElementById('loading-box');

    const nameInput = document.getElementById('name');
    const emailInput = document.getElementById('email');
    const roleInput = document.getElementById('role');
    const userGroupInput = document.getElementById('user_group_id');
    const statusInput = document.getElementById('status');

    function loadUserData() {
        fetch(loadUserUrl, {
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
            const user = response.data;

            nameInput.value = user.name ?? '';
            emailInput.value = user.email ?? '';
            roleInput.value = user.role ?? 'user';
            userGroupInput.value = user.user_group_id ?? '';

            statusInput.checked = user.status == 1 || user.status === true;

            loadingBox.classList.add('d-none');
            form.classList.remove('d-none');
        })
        .catch(function () {
            loadingBox.classList.add('d-none');

            Swal.fire({
                title: 'Lỗi!',
                text: 'Không thể tải dữ liệu user.',
                icon: 'error',
                width: '360px'
            });
        });
    }

    loadUserData();

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
                    text: 'Không thể cập nhật người dùng. Vui lòng thử lại.',
                    icon: 'error',
                    width: '360px'
                });
            }
        })
        .finally(function () {
            submitBtn.disabled = false;
            submitBtn.innerText = 'Update User';
        });
    });
</script>
@endsection