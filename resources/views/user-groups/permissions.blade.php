@extends('layouts.skote')

@section('title', 'Group Permissions | Skote')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">
                Permissions: {{ $userGroup->name }}
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

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

<form action="{{ route('user-groups.permissions.update', $userGroup) }}" method="POST">
    @csrf

    <div class="card">
        <div class="card-body">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="card-title mb-0">Permission List</h4>

                <a href="{{ route('user-groups.index') }}" class="btn btn-secondary">
                    Back
                </a>
            </div>

            @if($userGroup->is_fullaccess)
                <div class="alert alert-info">
                    Nhóm này đang bật <strong>Full Access</strong>, nên hệ thống sẽ bỏ qua kiểm tra từng permission.
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead>
                        <tr>
                            <th style="width: 260px;">Controller</th>
                            <th>Permissions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($permissions as $controller => $actions)
                            <tr>
                                <td>
                                    <strong>{{ $controller }}</strong>
                                </td>

                                <td>
                                    <div class="row">
                                        @foreach($actions as $action => $label)
                                            @php
                                                $value = $controller . '@' . $action;
                                            @endphp

                                            <div class="col-md-4 mb-2">
                                                <div class="form-check">
                                                    <input class="form-check-input"
                                                           type="checkbox"
                                                           name="permissions[]"
                                                           value="{{ $value }}"
                                                           id="{{ md5($value) }}"
                                                           {{ in_array($value, $selectedPermissions) ? 'checked' : '' }}>

                                                    <label class="form-check-label" for="{{ md5($value) }}">
                                                        {{ $label }}
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <button type="submit" class="btn btn-primary">
                Save Permissions
            </button>

            <a href="{{ route('user-groups.index') }}" class="btn btn-light">
                Cancel
            </a>

        </div>
    </div>
</form>
@endsection