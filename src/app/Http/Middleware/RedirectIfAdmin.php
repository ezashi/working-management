<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->isAdmin()) {
            $id = $request->route('id');
            if ($id) {
                // 管理者用コントローラーを直接呼び出し
                $adminController = app(\App\Http\Controllers\AdminAttendanceController::class);

                if ($request->isMethod('GET')) {
                    return $adminController->show($id);
                } elseif ($request->isMethod('PUT') || $request->isMethod('PATCH')) {
                    return $adminController->update($request, $id);
                }
            }
            return redirect()->route('admin.attendance');
        }

        return $next($request);
    }
}