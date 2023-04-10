<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
    public function terminateSubscription()
    {
        Auth::user()->subscription()->cancelNow();
        return to_route("home")->with(["message"=>"Subscription terminated"]);
    }
    public function cancelSubscription()
    {
        Auth::user()->subscription()->cancel();
        return back()->with(["message"=>"Subscription canceled"]);
    }
    public function resumeSubscription()
    {
        Auth::user()->subscription()->resume();
        return back()->with(["message"=>"Subscription resumed"]);
    }

    public function manageSubscriptionPage()
    {
        $currentPlanName = $this->getPlansIds($this->getPlans());
        foreach ($currentPlanName as $plan) {
            if ($plan["id"] === Auth::user()->subscription()->stripe_price) {
                $currentPlanName = $plan["name"];
                break;
            }
        }
        return view('managesubscription')->with([
            "planname" => $currentPlanName,
        ]);
    }
    public function home()
    {
        return view("home")->with(['plans' => $this->getPlansIds($this->getPlans())]);
    }
    public function getPlans()
    {
        $user = Auth::user();
        $key = config('services.stripe.secret');
        $stripe = new \Stripe\StripeClient($key);
        $plansraw = $stripe->plans->all();
        $plans = $plansraw->data;
        foreach ($plans as $plan) {
            $prod = $stripe->products->retrieve(
                $plan->product,
                []
            );
            $plan->product = $prod;
        }
        return $plans;
    }
    private function getPlansIds($plans)
    {
        $returnedArray = array_map(function ($p) {
            return [
                "name" => $p->product->name . " " . ucfirst($p->interval) . "ly",
                "id" => $p->id
            ];
        }, $plans);
        return $returnedArray;
    }
    public function getProductIds($plans)
    {
        $returnedArray = array_map(function ($p) {
            return [
                "name" => $p->product->name . " " . ucfirst($p->interval) . "ly",
                "id" => $p->product->id
            ];
        }, $plans);
        return $returnedArray;
    }
    private function getPlan($planid)
    {
        $plans = $this->getPlans();
        foreach ($plans as $plan) {
            if ($planid === $plan->id) {
                return $plan;
            }
        }
    }
    public function showPlans(Request $request)
    {
        $user = Auth::user();
        return view('plans', ['plans' => $this->getPlans($request), 'intent' => $user->createSetupIntent(),]);
    }
    public function trial(Request $request)
    {
        $user = Auth::user();
        if ($user->hasExpiredTrial("default") || $user->onTrial("default")) {
            return redirect()->route('home')->with(['message' => "You have already used your free trial period."]);
        }
        $user->createOrGetStripeCustomer();
        $plans = $this->getPlans();
        $paymentMethods = $user->paymentMethods();
        return view('trial')->with([
            "plans" => $plans,
            "methods" => $paymentMethods
        ]);
    }
    public function showSubscribe(Request $request)
    {
        $user = Auth::user();
        $user->createOrGetStripeCustomer();
        $plans = $this->getPlans();
        $paymentMethods = $user->paymentMethods();
        return view('subscribe')->with([
            "plans" => $plans,
            "methods" => $paymentMethods
        ]);
    }
    public function subscribe(Request $request)
    {
        $user = Auth::user();
        $user->createOrGetStripeCustomer();
        $plan = $this->getPlan($request->planid);
        $planName = $plan->product->name . " " . ucfirst($plan->interval) . "ly";
        try {
            $user->newSubscription("default", $request->planid)->create($request->methodid);
        } catch (\Exception $e) {
            return back()->withErrors(['message' => 'Error creating subscription. ' . $e->getMessage()]);
        }
        return to_route('home')->with(["subscribed" => "You have successfully subscribed to the {$planName}"]);
    }
    public function startTrial(Request $request)
    {
        $user = Auth::user();
        if ($user->hasExpiredTrial("default") || $user->onTrial("default")) {
            return redirect()->route('home')->with(['message' => "You have already used your free trial period."]);
        }
        $paymentMethods = $user->paymentMethods();
        if (!count($paymentMethods)) {
            return redirect()->route('billingMethods')->with(['message' => "In order to start your 7 days trial, you have to add at least one billing method."]);
        }
        $request->user()->newSubscription('default', $request->planid)
            ->trialDays(10)
            ->create($request->methodid);
        return redirect()->route('home')->with(["message" => "Trial activated successfully"]);
    }
}
