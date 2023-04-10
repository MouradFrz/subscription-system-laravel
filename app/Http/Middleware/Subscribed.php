<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

use function PHPUnit\Framework\isNull;

class Subscribed
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
    public function handle(Request $request, Closure $next, String $type = "basic"): Response
    {   if(!Auth::user()->subscribed()){
        return to_route("showSubscribe");
    }
        if (Auth::user()->subscription()===null) {
            return to_route("showSubscribe");
        }
        if ($type === "premium") {
            if (Auth::user()->subscription()->asStripeSubscription()->items->data[0]->plan->product!== $this->types["premium"]) {
                return to_route("showSubscribe");
            }
        }
        return $next($request);
    }
}
