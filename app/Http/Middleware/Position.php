<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Position
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, $position)
    {
        if ($request->user()->position == $position){
            return $next($request);
        }
        return redirect("/dashboard")->with('error','Anda Tidak diijinkan mengakses halaman tersebut');
    }
}
