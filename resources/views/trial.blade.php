@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="alert alert-warning w-100 " role="alert">
            <p class="mb-0"><span class="fw-bold">DISCLAIMER: </span>By activating this free trial, you will be subscribed
                to the plan you chose below, and unless you explicitly unsubscribe, the credit card you choose will be
                charged at the end of your trial in order to renew your subscription.</p>
        </div>
        <form action={{ route("startTrial") }} method="POST">
            @csrf
            @foreach ($plans as $plan)
                <input type="radio" class="form-check-input" name="planid" value={{ $plan->id }}>
                <label for="">{{ ucfirst($plan->interval) . 'ly' }} {{ $plan->product->name }} </label> <br>
            @endforeach
            <label for="">Payment Method:</label> <br>
            <select name="methodid" id="" class="form-select">
                @foreach ($methods as $method)
                    <option value={{ $method->id }}>{{ $method->billing_details->name }}</option>
                @endforeach
            </select>
            <button class="btn btn-primary mt-2">Start trial</button>
        </form>
    </div>
@endsection
