@extends('layouts.skote')

@section('title', 'User Groups | Skote')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">User Groups</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active">User Groups</li>
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
            <h4 class="card-title mb-0">Group List</h4>

            <a href="{{ route('user-groups.create') }}" class="btn btn-success">
                + New Group
            </a>
        </div>

        <div class="table-responsive">
            <table id="user-groups-table" class="table table-bordered align-middle w-100">
                <thead>
                    <tr>
                        <th style="width: 70px;">#</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Full Access</th>
                        <th>Permissions</th>
                        <th style="width: 300px;">Action</th>
                    </tr>
                </thead>

                <tbody></tbody>
            </table>
        </div>

    </div>
</div>
@endsection

@section('script')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>

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
    let userGroupsTable = null;

    $(document).ready(function () {
        userGroupsTable = $('#user-groups-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route('user-groups.data') }}',
            pageLength: 10,
            order: [[0, 'desc']],
            columns: [
                {
                    data: 'id',
                    name: 'id'
                },
                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'description',
                    name: 'description'
                },
                {
                    data: 'is_fullaccess',
                    name: 'is_fullaccess',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'permissions_count',
                    name: 'permissions_count',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                }
            ],
            columnDefs: [
                {
                    targets: [1, 3, 4, 5],
                    render: function (data) {
                        return data;
                    }
                }
            ],
            language: {
                processing: 'Đang tải dữ liệu...',
                search: 'Tìm kiếm:',
                lengthMenu: 'Hiển thị _MENU_ dòng',
                info: 'Hiển thị _START_ đến _END_ của _TOTAL_ dòng',
                infoEmpty: 'Không có dữ liệu',
                infoFiltered: '(lọc từ _MAX_ dòng)',
                zeroRecords: 'Không tìm thấy dữ liệu phù hợp',
                paginate: {
                    first: 'Đầu',
                    last: 'Cuối',
                    next: 'Sau',
                    previous: 'Trước'
                }
            }
        });
    });

    $(document).on('submit', '.delete-user-group-form', function (event) {
        event.preventDefault();

        const form = this;
        const groupName = form.getAttribute('data-name') || 'nhóm user này';
        const url = form.getAttribute('action');
        const token = form.querySelector('input[name="_token"]').value;

        Swal.fire({
            title: 'Xác nhận xoá?',
            text: 'Bạn có chắc muốn xoá nhóm "' + groupName + '" không?',
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
                fetch(url, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': token,
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
                .then(function (data) {
                    if (data.success) {
                        userGroupsTable.ajax.reload(null, false);

                        Swal.fire({
                            title: 'Đã xoá!',
                            text: data.message,
                            icon: 'success',
                            width: '360px',
                            timer: 1200,
                            showConfirmButton: false
                        });
                    } else {
                        Swal.fire({
                            title: 'Lỗi!',
                            text: data.message || 'Không thể xoá nhóm user.',
                            icon: 'error',
                            width: '360px'
                        });
                    }
                })
                .catch(function (error) {
                    Swal.fire({
                        title: 'Lỗi!',
                        text: error.message || 'Không thể xoá nhóm user. Vui lòng thử lại.',
                        icon: 'error',
                        width: '360px'
                    });
                });
            }
        });
    });
</script>
@endsection