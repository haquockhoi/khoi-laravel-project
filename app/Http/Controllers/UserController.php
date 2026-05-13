<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        return view('users.index');
    }

    public function data(Request $request)
    {
        $columns = [
            0 => 'id',
            1 => 'name',
            2 => 'email',
            3 => 'role',
            4 => 'user_group_id',
            5 => 'created_at',
            6 => 'id',
        ];

        $totalData = User::count();

        $query = User::query();

        if (!empty($request->input('search.value'))) {
            $search = $request->input('search.value');

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('role', 'like', "%{$search}%");
            });
        }

        $totalFiltered = $query->count();

        $orderColumnIndex = $request->input('order.0.column', 0);
        $orderColumn = $columns[$orderColumnIndex] ?? 'id';
        $orderDirection = $request->input('order.0.dir', 'desc');

        if (in_array($orderColumn, ['id', 'name', 'email', 'role', 'user_group_id', 'created_at'])) {
            $query->orderBy($orderColumn, $orderDirection);
        } else {
            $query->latest();
        }

        $users = $query
            ->offset($request->input('start', 0))
            ->limit($request->input('length', 10))
            ->get();

        $data = [];

        foreach ($users as $user) {
            $role = ($user->role ?? 'user') === 'admin'
                ? '<span class="badge bg-success">Admin</span>'
                : '<span class="badge bg-secondary">User</span>';

            $userGroup = $user->user_group_id
                ? '<span class="badge bg-info">Group ID: ' . $user->user_group_id . '</span>'
                : '<span class="text-muted">No group</span>';

            $action = '
                <a href="' . route('users.edit', $user) . '" class="btn btn-warning btn-sm">
                    Edit
                </a>

                <form action="' . route('users.destroy', $user) . '"
                      method="POST"
                      class="d-inline delete-user-form"
                      data-id="' . $user->id . '"
                      data-name="' . e($user->name) . '">
                    <input type="hidden" name="_token" value="' . csrf_token() . '">
                    <input type="hidden" name="_method" value="DELETE">

                    <button type="submit" class="btn btn-danger btn-sm">
                        Delete
                    </button>
                </form>
            ';

            $data[] = [
                'id' => $user->id,
                'name' => '<strong>' . e($user->name) . '</strong>',
                'email' => e($user->email),
                'role' => $role,
                'user_group' => $userGroup,
                'created_at' => $user->created_at ? $user->created_at->format('d/m/Y H:i') : '-',
                'action' => $action,
            ];
        }

        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $totalData,
            'recordsFiltered' => $totalFiltered,
            'data' => $data,
        ]);
    }

    public function showAjax(User $user)
    {
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role ?? 'user',
                'user_group_id' => $user->user_group_id,
                'status' => $user->status ?? $user->is_active ?? true,
            ],
        ]);
    }

    public function create(): View
    {
        $userGroups = UserGroup::orderBy('name')->get();

        return view('users.create', compact('userGroups'));
    }

    public function store(Request $request)
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['nullable', 'string'],
            'user_group_id' => ['nullable', 'exists:user_groups,id'],
        ];

        $request->validate($rules);

        $data = [
            'name' => $request->name,
            'email' => strtolower($request->email),
            'password' => Hash::make($request->password),
            'role' => $request->role ?? 'user',
            'user_group_id' => $request->user_group_id,
        ];

        if (Schema::hasColumn('users', 'status')) {
            $data['status'] = $request->has('status');
        }

        if (Schema::hasColumn('users', 'is_active')) {
            $data['is_active'] = $request->has('is_active') || $request->has('status');
        }

        $user = User::create($data);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Thêm người dùng thành công!',
                'redirect' => route('users.index'),
                'data' => $user,
            ]);
        }

        return redirect()
            ->route('users.index')
            ->with('success', 'Thêm người dùng thành công!');
    }

    public function edit(User $user): View
    {
        $userGroups = UserGroup::orderBy('name')->get();

        return view('users.edit', compact('user', 'userGroups'));
    }

    public function update(Request $request, User $user)
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'role' => ['nullable', 'string'],
            'user_group_id' => ['nullable', 'exists:user_groups,id'],
        ];

        $request->validate($rules);

        $data = [
            'name' => $request->name,
            'email' => strtolower($request->email),
            'role' => $request->role ?? 'user',
            'user_group_id' => $request->user_group_id,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        if (Schema::hasColumn('users', 'status')) {
            $data['status'] = $request->has('status');
        }

        if (Schema::hasColumn('users', 'is_active')) {
            $data['is_active'] = $request->has('is_active') || $request->has('status');
        }

        $user->update($data);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Cập nhật người dùng thành công!',
                'redirect' => route('users.index'),
                'data' => $user,
            ]);
        }

        return redirect()
            ->route('users.index')
            ->with('success', 'Cập nhật người dùng thành công!');
    }

    public function destroy(User $user, Request $request)
    {
        if (auth()->id() === $user->id) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể xoá chính tài khoản đang đăng nhập!',
                ], 403);
            }

            return redirect()
                ->route('users.index')
                ->with('error', 'Không thể xoá chính tài khoản đang đăng nhập!');
        }

        $user->delete();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Xoá người dùng thành công!',
            ]);
        }

        return redirect()
            ->route('users.index')
            ->with('success', 'Xoá người dùng thành công!');
    }
}