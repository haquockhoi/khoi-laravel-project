<?php

namespace App\Http\Middleware;

use App\Models\UserGroupPermission;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermissionMiddleware
{
    public function handle(Request $request, Closure $next, string $controller, string $action): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        if (!$user->user_group_id) {
            abort(403, 'Tài khoản chưa được gán nhóm quyền.');
        }

        $userGroup = $user->userGroup;

        if (!$userGroup) {
            abort(403, 'Nhóm quyền không tồn tại.');
        }

        if ($userGroup->is_fullaccess) {
            return $next($request);
        }

        $hasPermission = UserGroupPermission::where('user_group_id', $user->user_group_id)
            ->where('controller', $controller)
            ->where('action', $action)
            ->exists();

        if (!$hasPermission) {
            abort(403, 'Bạn không có quyền truy cập chức năng này.');
        }

        return $next($request);
    }
}