<?php

namespace App\Http\Middleware;

use App\Http\Resources\GenericResponseResource;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;

class CheckIfUserExistsByEmail
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
        $user = User::where("email", $request->email)->first();
        if ($user) {
            return new GenericResponseResource(null, config('Constants.httpStatusCodes.FORBIDDEN'), "User with email '$request->email' already exists!");
        }
        return $next($request);
    }
}
