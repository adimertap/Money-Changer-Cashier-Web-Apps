<?php

namespace App\Http\Middleware;

use App\Services\MenuAccessService;
use Closure;
use Illuminate\Http\Request;

class MenuAccess
{
    public function handle(Request $request, Closure $next, ...$menus)
    {
        if (!app(MenuAccessService::class)->allows($menus)) {
            abort(403, 'Anda tidak memiliki hak akses ke menu ini.');
        }

        return $next($request);
    }
}
