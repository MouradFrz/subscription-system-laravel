@extends('layouts.app')
@section('content')
    <div class="container">
        <form action={{ route("subscribe") }} method="POST">
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
