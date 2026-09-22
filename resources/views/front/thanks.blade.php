@extends('front.layouts.app')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="text-center">
                    @include('front.message')

                    <div class="mb-4">
                        <i class="fa fa-check-circle text-success" style="font-size: 70px;"></i>
                    </div>
                    <h2>Thank You!</h2>

                    <p>Your order #{{ $id }} has been placed successfully.</p>

                    <p>
                        Thank you for shopping with us. We will process your order
                        and contact you shortly.
                    </p>

                    <a href="{{ route('front.shop') }}" class="btn btn-dark mt-3">
                        Continue Shopping
                    </a>

                </div>
            </div>
        </div>
    </div>
@endsection

