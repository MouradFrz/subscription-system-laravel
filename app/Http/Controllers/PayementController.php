<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PayementController extends Controller
{
    public function addBillingMethod(Request $request)
    {
        $paymentMethod = $request->payment_method;
        $user = Auth::user();
        $user->createOrGetStripeCustomer();
        $paymentMethod = $user->addPaymentMethod($paymentMethod);
        return back()->with(["success"=>"Added billing method successfully"]);
    }
    public function getBillingMethods(Request $request){
        $user = Auth::user();
        $user->createOrGetStripeCustomer();
        return view('billingmethods',[
            'methods'=>$user->paymentMethods(),
            'intent' => $user->createSetupIntent(),
        ]);
    }
    public function removeBillingMethod(Request $request){
        $user = Auth::user();
        $paymentMethods = $user->paymentMethods();
        foreach($paymentMethods as $method){
            echo $method->id.'<br>';
            if($method->id===$request->methodid){
                $method->delete();
            }
        }
        return back()->with(['success'=>"Removed successfully"]);
    }
}
