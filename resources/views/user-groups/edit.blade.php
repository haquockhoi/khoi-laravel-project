@extends('layouts.skote')

@section('title', 'Edit User Group | Skote')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Edit User Group</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('user-groups.index') }}">User Groups</a>
                    </li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </div>
        </div>
    </div>
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

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('user-groups.update', $userGroup) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label">Group Name</label>
                        <input type="text"
                               name="name"
                               class="form-control"
                               value="{{ old('name', $userGroup->name) }}"
                               required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description"
                                  class="form-control"
                                  rows="4">{{ old('description', $userGroup->description) }}</textarea>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox"
                                   name="is_fullaccess"
                                   value="1"
                                   class="form-check-input"
                                   id="is_fullaccess"
                                   {{ old('is_fullaccess', $userGroup->is_fullaccess) ? 'checked' : '' }}>

                            <label class="form-check-label" for="is_fullaccess">
                                Full Access
                            </label>
                        </div>

                        <small class="text-muted">
                            Nếu bật, nhóm này sẽ bỏ qua kiểm tra phân quyền và được truy cập toàn bộ chức năng.
                        </small>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        Update Group
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