@extends('layouts.app')
@section('content')
    <div class="container">
        @if (Session::has('message'))
            <div class="alert alert-warning w-100 " role="alert">
                <p class="mb-0">{{ Session::get('message') }}</p>
            </div>
        @endif
        @auth
            <p>
                @if (Auth::user()->onTrial('default'))
                    <p>You are currently on trial.</p>
                @elseif (Auth::user()->subscribed('default'))
                    @foreach ($plans as $sub)
                        @if (Auth::user()->subscribedToPrice($sub['id'], 'default'))
                            You are subscribed to {{ $sub['name'] }} @if (Auth::user()->subscription()->onGracePeriod())
                                (Subscription canceled, still valid till
                                {{ date('dS M Y',Auth::user()->subscription()->asStripeSubscription()->current_period_end) }})
                            @endif
                        @endif
                    @endforeach
                @elseif (Auth::user()->hasExpiredTrial('default'))
                    Your trial has expired
                @else
                    You are not subscribed and your account is limited
                @endif
            </p>
            <p>{{ Auth::user()->subscription()===null ? "null" : "not nul" }}</p>
        @endauth
    </div>
@endsection
