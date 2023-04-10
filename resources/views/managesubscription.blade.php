@extends('layouts.app')

@section('content')
    <div class="container">
        <p>You are currently subscribed to {{ $planname }} @if (Auth::user()->subscription()->onGracePeriod())
            (Subscription canceled, still valid till {{ date('dS M Y',Auth::user()->subscription()->asStripeSubscription()->current_period_end) }})
        @endif</p>
        <p>Subscription date : {{ date('dS M Y', Auth::user()->subscription()->created_at->timestamp) }}</p>
        <p>Current period start date
            :{{ date('dS M Y',Auth::user()->subscription()->asStripeSubscription()->current_period_start) }}</p>
        <p>Current period end date
            :{{ date('dS M Y',Auth::user()->subscription()->asStripeSubscription()->current_period_end) }}</p>
        <p>Subscription canceled at: @if (Auth::user()->subscription()->asStripeSubscription()->canceled_at !== null)
                {{ date('dS M Y',Auth::user()->subscription()->asStripeSubscription()->canceled_at) }}
            @else
                /
            @endif
        </p>
        @if (Auth::user()->subscription()->asStripeSubscription()->canceled_at !== null)
            <form action={{ route('resumeSubscription') }} method="POST">
                @csrf
                <button class="btn btn-primary">Resume subscription</button>
            </form>
        @else
            <form action={{ route('cancelSubscription') }} method="POST">
                @csrf
                <button class="btn-danger btn">Cancel subscription</button>
            </form>
        @endif
        <form action={{ route("terminateSubscription") }} method="POST">
            @csrf
        <button class="btn btn-danger">Terminate subscription</button></form>
    </div>
@endsection
