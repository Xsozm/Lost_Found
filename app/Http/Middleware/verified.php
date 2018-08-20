<?php

namespace App\Http\Middleware;

use Closure;

class verified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user =auth()->user();
        if (!$user->verified)
            return response()->json("Please Verify Your Account",401);
        if($user->isBanned)
            return response()->json("Your Account Has been Banned From Our Service for a while , Please Contact the Admin",401);
        return $next($request);

    }
}
