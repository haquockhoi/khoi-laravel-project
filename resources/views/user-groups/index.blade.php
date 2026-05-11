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
            <table class="table table-bordered align-middle">
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

                <tbody>
                    @forelse($userGroups as $group)
                        <tr>
                            <td>{{ $group->id }}</td>

                            <td>
                                <strong>{{ $group->name }}</strong>
                            </td>

                            <td>{{ $group->description ?? '-' }}</td>

                            <td>
                                @if($group->is_fullaccess)
                                    <span class="badge bg-success">Yes</span>
                                @else
                                    <span class="badge bg-secondary">No</span>
                                @endif
                            </td>

                            <td>
                                <span class="badge bg-primary">
                                    {{ $group->permissions_count }} permissions
                                </span>
                            </td>

                            <td>
                                <a href="{{ route('user-groups.permissions', $group) }}"
                                   class="btn btn-info btn-sm">
                                    Permissions
                                </a>

                                <a href="{{ route('user-groups.edit', $group) }}"
                                   class="btn btn-warning btn-sm">
                                    Edit
                                </a>

                                <form action="{{ route('user-groups.destroy', $group) }}"
                                      method="POST"
                                      class="d-inline"
                                      onsubmit="return confirm('Bạn có chắc muốn xoá nhóm này không?')">
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
                            <td colspan="6" class="text-center">
                                Chưa có nhóm user nào.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</div>
@endsection