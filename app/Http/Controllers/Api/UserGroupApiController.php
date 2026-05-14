<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserGroup;
use App\Models\UserGroupPermission;
use Illuminate\Http\Request;

class UserGroupApiController extends Controller
{
    public function index(Request $request)
    {
        $query = UserGroup::withCount('permissions')
            ->latest();

        if ($request->filled('keyword')) {
            $keyword = $request->keyword;

            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                    ->orWhere('description', 'like', "%{$keyword}%");
            });
        }

        if ($request->filled('is_fullaccess')) {
            $query->where('is_fullaccess', $request->boolean('is_fullaccess'));
        }

        $groups = $query->paginate($request->get('per_page', 10));

        return response()->json([
            'success' => true,
            'message' => 'Lấy danh sách nhóm user thành công.',
            'data' => $groups,
        ], 200, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    public function show(UserGroup $userGroup)
    {
        $userGroup->load('permissions');

        return response()->json([
            'success' => true,
            'message' => 'Lấy chi tiết nhóm user thành công.',
            'data' => $userGroup,
        ], 200, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:user_groups,name'],
            'description' => ['nullable', 'string'],
            'is_fullaccess' => ['nullable', 'boolean'],
        ]);

        $userGroup = UserGroup::create([
            'name' => $request->name,
            'description' => $request->description,
            'is_fullaccess' => $request->boolean('is_fullaccess'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Tạo nhóm user thành công.',
            'data' => $userGroup,
        ], 201, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    public function update(Request $request, UserGroup $userGroup)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:user_groups,name,' . $userGroup->id],
            'description' => ['nullable', 'string'],
            'is_fullaccess' => ['nullable', 'boolean'],
        ]);

        $userGroup->update([
            'name' => $request->name,
            'description' => $request->description,
            'is_fullaccess' => $request->boolean('is_fullaccess'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật nhóm user thành công.',
            'data' => $userGroup,
        ], 200, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    public function destroy(UserGroup $userGroup)
    {
        $userGroup->permissions()->delete();
        $userGroup->delete();

        return response()->json([
            'success' => true,
            'message' => 'Xoá nhóm user thành công.',
        ], 200, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    public function permissions(UserGroup $userGroup)
    {
        $userGroup->load('permissions');

        return response()->json([
            'success' => true,
            'message' => 'Lấy danh sách quyền của nhóm thành công.',
            'data' => [
                'group' => $userGroup,
                'permissions' => $this->getPermissionList(),
                'selected_permissions' => $userGroup->permissions->map(function ($permission) {
                    return $permission->controller . '@' . $permission->action;
                })->values(),
            ],
        ], 200, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    public function updatePermissions(Request $request, UserGroup $userGroup)
    {
        $request->validate([
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string'],
        ]);

        $userGroup->permissions()->delete();

        foreach ($request->permissions ?? [] as $permission) {
            if (!str_contains($permission, '@')) {
                continue;
            }

            [$controller, $action] = explode('@', $permission);

            UserGroupPermission::create([
                'user_group_id' => $userGroup->id,
                'controller' => $controller,
                'action' => $action,
            ]);
        }

        $userGroup->load('permissions');

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật phân quyền nhóm thành công.',
            'data' => $userGroup,
        ], 200, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    private function getPermissionList()
    {
        return [
            'DashboardController' => [
                'index' => 'Xem dashboard',
            ],

            'UserController' => [
                'index' => 'Xem danh sách user',
                'create' => 'Mở form thêm user',
                'store' => 'Lưu user mới',
                'edit' => 'Mở form sửa user',
                'update' => 'Cập nhật user',
                'destroy' => 'Xoá user',
            ],

            'UserGroupController' => [
                'index' => 'Xem danh sách nhóm user',
                'create' => 'Mở form thêm nhóm user',
                'store' => 'Lưu nhóm user mới',
                'edit' => 'Mở form sửa nhóm user',
                'update' => 'Cập nhật nhóm user',
                'destroy' => 'Xoá nhóm user',
                'permissions' => 'Xem phân quyền nhóm',
                'updatePermissions' => 'Cập nhật phân quyền nhóm',
            ],

            'CategoryController' => [
                'index' => 'Xem danh sách danh mục tin tức',
                'create' => 'Mở form thêm danh mục tin tức',
                'store' => 'Lưu danh mục tin tức mới',
                'edit' => 'Mở form sửa danh mục tin tức',
                'update' => 'Cập nhật danh mục tin tức',
                'destroy' => 'Xoá danh mục tin tức',
            ],

            'NewsController' => [
                'index' => 'Xem danh sách tin tức',
                'create' => 'Mở form thêm tin tức',
                'store' => 'Lưu tin tức mới',
                'edit' => 'Mở form sửa tin tức',
                'update' => 'Cập nhật tin tức',
                'destroy' => 'Xoá tin tức',
            ],

            'ProfileController' => [
                'edit' => 'Xem profile',
                'update' => 'Cập nhật profile',
                'destroy' => 'Xoá tài khoản',
            ],
        ];
    }
}