@extends('layouts.app')
@section('styles')
<style>
    .StripeElement {
        background-color: white;
        padding: 8px 12px;
        border-radius: 4px;
        border: 1px solid transparent;
        box-shadow: 0 1px 3px 0 #e6ebf1;
        -webkit-transition: box-shadow 150ms ease;
        transition: box-shadow 150ms ease;
    }
    .StripeElement--focus {
        box-shadow: 0 1px 3px 0 #cfd7df;
    }
    .StripeElement--invalid {
        border-color: #fa755a;
    }
    .StripeElement--webkit-autofill {
        background-color: #fefde5 !important;
    }
</style>
@endsection
@section('content')
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action={{ route('removeBillingMethod') }} method="POST" id="delete-form">
                        @csrf
                        <input type="hidden" name="methodid" id="hidden-input">
                        <p class="m-0">Are you sure you want to delete this payment method?</p>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-danger"
                        onclick="document.querySelector('#delete-form').submit()">Confirm</button>
                </div>
            </div>
        </div>
    </div>
    <div class="container">
        @if (Session::has('success'))
            <div class="alert alert-success w-100 " role="alert">
                {{ Session::get('success') }}
            </div>
        @endif
        @if (Session::has('message'))
            <div class="alert alert-warning w-100 " role="alert">
                {{ Session::get('message') }}
            </div>
        @endif
        <h1>Registered billing methods</h1>
        <hr>
        <div>
            @if (count($methods) > 0)
                @foreach ($methods as $method)
                    <div class="card mb-3 px-3 py-2">
                        <p class="m-0"><span class="fw-bold">Name: </span>{{ $method->billing_details->name }}</p>
                        <p class="m-0"><span class="fw-bold">Card number: </span>**** **** ****
                            {{ $method->card->last4 }}
                        </p>
                        <p class="m-0"><span class="fw-bold">Brand: </span>{{ $method->card->brand }}</p>
                        <p class="m-0"><span class="fw-bold">Exipration date:
                            </span>{{ $method->card->exp_month }}/{{ $method->card->exp_year }}</p>
                        <button class="btn btn-danger w-25 mt-2 delete-trigger" data-methodid={{ $method->id }}
                            data-bs-toggle="modal" data-bs-target="#exampleModal">Delete this billing method</button>
                    </div>
                @endforeach
            @else
                <p>You have none</p>
            @endif
            <hr>
        </div>
        <div class="row justify-content-center">
            <div class="">
                <div class="card">
                    <div class="card-header">Add billing method</div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif
                            @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif
                        <form action={{ route('addBillingMethod') }} method="POST" id="subscribe-form">
                            <label for="card-holder-name">Card Holder Name</label><br>
                            <input id="card-holder-name" class="form-control" type="text">
                            @csrf
                            <div class="form-row">
                                <label for="card-element">Credit or debit card</label>
                                <div id="card-element" class="form-control">
                                </div>
                                <!-- Used to display form errors. -->
                                <div id="card-errors" role="alert"></div>
                            </div>
                            <div class="stripe-errors"></div>
                            @if (count($errors) > 0)
                            <div class="alert alert-danger">
                                @foreach ($errors->all() as $error)
                                {{ $error }}<br>
                                @endforeach
                            </div>
                            @endif
                            <div class="form-group">
                                <button  id="card-button" data-secret="{{ $intent->client_secret }}" class="btn btn-success mt-2 px-5">Add</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        const buttons = document.querySelectorAll(".delete-trigger");
        const input = document.querySelector(`#hidden-input`)
        buttons.forEach(e => {
            e.addEventListener('click', (ev) => {
                input.value = e.dataset.methodid
            })
        })
    </script>
    <script src="https://js.stripe.com/v3/"></script>
    <script>
        var stripe = Stripe('{{ env('STRIPE_KEY') }}');
        var elements = stripe.elements();
        var style = {
            base: {
                color: '#32325d',
                fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
                fontSmoothing: 'antialiased',
                fontSize: '16px',
                '::placeholder': {
                    color: '#aab7c4'
                }
            },
            invalid: {
                color: '#fa755a',
                iconColor: '#fa755a'
            }
        };
        var card = elements.create('card', {hidePostalCode: true,
            style: style});
        card.mount('#card-element');
        card.addEventListener('change', function(event) {
            var displayError = document.getElementById('card-errors');
            if (event.error) {
                displayError.textContent = event.error.message;
            } else {
                displayError.textContent = '';
            }
        });
        const cardHolderName = document.getElementById('card-holder-name');
        const cardButton = document.getElementById('card-button');
        const clientSecret = cardButton.dataset.secret;
        cardButton.addEventListener('click', async (e) => {
            e.preventDefault();
            const { setupIntent, error } = await stripe.confirmCardSetup(
                clientSecret, {
                    payment_method: {
                        card: card,
                        billing_details: { name: cardHolderName.value }
                    }
                }
                );
            if (error) {
                var errorElement = document.getElementById('card-errors');
                errorElement.textContent = error.message;
            } else {
                paymentMethodHandler(setupIntent.payment_method);
            }
        });
        function paymentMethodHandler(payment_method) {
            var form = document.getElementById('subscribe-form');
            var hiddenInput = document.createElement('input');
            hiddenInput.setAttribute('type', 'hidden');
            hiddenInput.setAttribute('name', 'payment_method');
            hiddenInput.setAttribute('value', payment_method);
            form.appendChild(hiddenInput);
            form.submit();
        }
    </script>
@endsection
