<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class Free
{
    private $types = [
        "basic" => "prod_Nfh77HD4f3yxfj",
        "premium" => "prod_NfhvxAamiMOcCg"
    ];
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(Auth::user()->onTrial()){
            return $next($request);
        }
        if (Auth::user()->subscribedToProduct($this->types["basic"], 'default') || Auth::user()->subscribedToProduct($this->types["premium"], 'default')) {
            return to_route("home");
        }
        return $next($request);
    }
}
