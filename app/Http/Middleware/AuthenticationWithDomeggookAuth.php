<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Constants\HttpConstant;
use Illuminate\Support\Facades\Session;
use App\Services\Api\Domeggook\UserManagementService;

class AuthenticationWithDomeggookAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $userManagementService = new UserManagementService($request);
        $result = $userManagementService->setLoginChk();

        if ($result["result"] == HttpConstant::RETURN_FAILURE || (isset($result["list"]["errors"]) && count($result["list"]["errors"]))) {
            Session::flush();
            return redirect()->route('login');
        }
        return $next($request);
    }
}
