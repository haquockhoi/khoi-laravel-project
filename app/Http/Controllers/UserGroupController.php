<?php

namespace App\Http\Controllers;

use App\Models\UserGroup;
use App\Models\UserGroupPermission;
use Illuminate\Http\Request;

class UserGroupController extends Controller
{
    public function index()
    {
        $userGroups = UserGroup::withCount('permissions')->latest()->get();

        return view('user-groups.index', compact('userGroups'));
    }

    public function create()
    {
        return view('user-groups.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:user_groups,name',
            'description' => 'nullable|string',
            'is_fullaccess' => 'nullable|boolean',
        ]);

        UserGroup::create([
            'name' => $request->name,
            'description' => $request->description,
            'is_fullaccess' => $request->has('is_fullaccess'),
        ]);

        return redirect()
            ->route('user-groups.index')
            ->with('success', 'Tạo nhóm user thành công!');
    }

    public function edit(UserGroup $userGroup)
    {
        return view('user-groups.edit', compact('userGroup'));
    }

    public function update(Request $request, UserGroup $userGroup)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:user_groups,name,' . $userGroup->id,
            'description' => 'nullable|string',
            'is_fullaccess' => 'nullable|boolean',
        ]);

        $userGroup->update([
            'name' => $request->name,
            'description' => $request->description,
            'is_fullaccess' => $request->has('is_fullaccess'),
        ]);

        return redirect()
            ->route('user-groups.index')
            ->with('success', 'Cập nhật nhóm user thành công!');
    }

    public function destroy(UserGroup $userGroup)
    {
        $userGroup->delete();

        return redirect()
            ->route('user-groups.index')
            ->with('success', 'Xoá nhóm user thành công!');
    }

    public function permissions(UserGroup $userGroup)
    {
        $permissions = [
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

            'ProfileController' => [
                'edit' => 'Xem profile',
                'update' => 'Cập nhật profile',
                'destroy' => 'Xoá tài khoản',
            ],

            'PageController' => [
                'form_advanced' => 'Truy cập Form Advanced',
                'ecommerce_customers' => 'Truy cập Ecommerce Customers',
                'ecommerce_checkout' => 'Truy cập Ecommerce Checkout',
                'email_template_basic' => 'Truy cập Email Template Basic',
                'email_template_billing' => 'Truy cập Email Template Billing',
            ],
        ];

        $selectedPermissions = $userGroup->permissions()
            ->get()
            ->map(function ($permission) {
                return $permission->controller . '@' . $permission->action;
            })
            ->toArray();

        return view('user-groups.permissions', compact(
            'userGroup',
            'permissions',
            'selectedPermissions'
        ));
    }

    public function updatePermissions(Request $request, UserGroup $userGroup)
    {
        $request->validate([
            'permissions' => 'nullable|array',
        ]);

        $userGroup->permissions()->delete();

        foreach ($request->permissions ?? [] as $permission) {
            [$controller, $action] = explode('@', $permission);

            UserGroupPermission::create([
                'user_group_id' => $userGroup->id,
                'controller' => $controller,
                'action' => $action,
            ]);
        }

        return redirect()
            ->route('user-groups.permissions', $userGroup)
            ->with('success', 'Cập nhật phân quyền thành công!');
    }
}