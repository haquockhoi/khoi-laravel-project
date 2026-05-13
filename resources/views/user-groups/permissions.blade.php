@extends('layouts.skote')

@section('title', 'Group Permissions | Skote')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18" id="page-title">
                Permissions
            </h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('user-groups.index') }}">User Groups</a>
                    </li>
                    <li class="breadcrumb-item active">Permissions</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div id="ajax-error-box" class="alert alert-danger d-none">
    <ul class="mb-0" id="ajax-error-list"></ul>
</div>

<form action="{{ route('user-groups.permissions.update', $userGroup) }}"
      method="POST"
      id="permissions-form">
    @csrf

    <div class="card">
        <div class="card-body">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="card-title mb-0">Permission List</h4>

                <a href="{{ route('user-groups.index') }}" class="btn btn-secondary">
                    Back
                </a>
            </div>

            <div id="loading-box" class="alert alert-info">
                Đang tải dữ liệu phân quyền...
            </div>

            <div id="full-access-alert" class="alert alert-info d-none">
                Nhóm này đang bật <strong>Full Access</strong>, nên hệ thống sẽ bỏ qua kiểm tra từng permission.
            </div>

            <div class="table-responsive d-none" id="permission-table-box">
                <table class="table table-bordered align-middle">
                    <thead>
                        <tr>
                            <th style="width: 260px;">Controller</th>
                            <th>Permissions</th>
                        </tr>
                    </thead>

                    <tbody id="permission-tbody"></tbody>
                </table>
            </div>

            <button type="submit" class="btn btn-primary d-none" id="submit-btn">
                Save Permissions
            </button>

            <a href="{{ route('user-groups.index') }}" class="btn btn-light d-none" id="cancel-btn">
                Cancel
            </a>

        </div>
    </div>
</form>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    const loadPermissionsUrl = '{{ route('user-groups.permissions.ajax', $userGroup) }}';

    const pageTitle = document.getElementById('page-title');
    const form = document.getElementById('permissions-form');
    const submitBtn = document.getElementById('submit-btn');
    const cancelBtn = document.getElementById('cancel-btn');
    const errorBox = document.getElementById('ajax-error-box');
    const errorList = document.getElementById('ajax-error-list');
    const loadingBox = document.getElementById('loading-box');
    const fullAccessAlert = document.getElementById('full-access-alert');
    const permissionTableBox = document.getElementById('permission-table-box');
    const permissionTbody = document.getElementById('permission-tbody');

    function makeId(value) {
        return 'permission_' + btoa(unescape(encodeURIComponent(value)))
            .replaceAll('=', '')
            .replaceAll('+', '_')
            .replaceAll('/', '_');
    }

    function escapeHtml(value) {
        return String(value ?? '')
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }

    function renderPermissions(permissions, selectedPermissions) {
        permissionTbody.innerHTML = '';

        Object.entries(permissions).forEach(function ([controller, actions]) {
            let actionsHtml = '<div class="row">';

            Object.entries(actions).forEach(function ([action, label]) {
                const value = controller + '@' + action;
                const id = makeId(value);
                const checked = selectedPermissions.includes(value) ? 'checked' : '';

                actionsHtml += `
                    <div class="col-md-4 mb-2">
                        <div class="form-check">
                            <input class="form-check-input"
                                   type="checkbox"
                                   name="permissions[]"
                                   value="${escapeHtml(value)}"
                                   id="${id}"
                                   ${checked}>

                            <label class="form-check-label" for="${id}">
                                ${escapeHtml(label)}
                            </label>
                        </div>
                    </div>
                `;
            });

            actionsHtml += '</div>';

            const row = `
                <tr>
                    <td>
                        <strong>${escapeHtml(controller)}</strong>
                    </td>
                    <td>
                        ${actionsHtml}
                    </td>
                </tr>
            `;

            permissionTbody.insertAdjacentHTML('beforeend', row);
        });
    }

    function loadPermissions() {
        fetch(loadPermissionsUrl, {
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
            const result = response.data;

            pageTitle.innerText = 'Permissions: ' + result.group.name;

            if (result.group.is_fullaccess) {
                fullAccessAlert.classList.remove('d-none');
            } else {
                fullAccessAlert.classList.add('d-none');
            }

            renderPermissions(result.permissions, result.selectedPermissions);

            loadingBox.classList.add('d-none');
            permissionTableBox.classList.remove('d-none');
            submitBtn.classList.remove('d-none');
            cancelBtn.classList.remove('d-none');
        })
        .catch(function () {
            loadingBox.classList.add('d-none');

            Swal.fire({
                title: 'Lỗi!',
                text: 'Không thể tải dữ liệu phân quyền.',
                icon: 'error',
                width: '360px'
            });
        });
    }

    loadPermissions();

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
                    text: 'Không thể cập nhật phân quyền. Vui lòng thử lại.',
                    icon: 'error',
                    width: '360px'
                });
            }
        })
        .finally(function () {
            submitBtn.disabled = false;
            submitBtn.innerText = 'Save Permissions';
        });
    });
</script>
@endsection