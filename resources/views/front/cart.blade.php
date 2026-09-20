@extends('front.layouts.app')

@section('content')
    <section class="section-5 pt-3 pb-3 mb-3 bg-white">
        <div class="container">
            <div class="light-font">
                <ol class="breadcrumb primary-color mb-0">
                    <li class="breadcrumb-item"><a class="white-text" href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a class="white-text" href="{{ route('front.shop') }}">Shop</a></li>
                    <li class="breadcrumb-item">Cart</li>
                </ol>
            </div>
        </div>
    </section>

    <section class=" section-9 pt-4">
        <div class="container">
            <div class="row">
                @if (Session::has('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ Session::get('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @if (Session::has('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ Session::get('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if (Cart::count() > 0)
                    <div class="col-md-8">
                        <div class="table-responsive">
                            <table class="table" id="cart">
                                <thead>
                                    <tr>
                                        <th>Item</th>
                                        <th>Price</th>
                                        <th>Quantity</th>
                                        <th>Total</th>
                                        <th>Remove</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    @forelse ($carts as $cart)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="{{ $cart->options->product_images->image_url }}"
                                                        width="" height=""
                                                        onerror="this.onerror=null; this.src='{{ asset('admin-assets/img/default-150x150.png') }}';">
                                                    <h2>{{ $cart->name }}</h2>
                                                </div>
                                            </td>
                                            <td>${{ $cart->price }}</td>
                                            <td>
                                                <div class="input-group quantity mx-auto" style="width: 100px;">
                                                    <div class="input-group-btn">
                                                        <button class="btn btn-sm btn-dark btn-minus p-2 pt-1 pb-1 sub"
                                                            data-id={{ $cart->rowId }}>
                                                            <i class="fa fa-minus"></i>
                                                        </button>
                                                    </div>
                                                    <input type="text"
                                                        class="form-control form-control-sm  border-0 text-center qty-input"
                                                        value="{{ $cart->qty }}" data-id="{{ $cart->rowId }}">
                                                    <div class="input-group-btn">
                                                        <button class="btn btn-sm btn-dark btn-plus p-2 pt-1 pb-1 add"
                                                            data-id={{ $cart->rowId }}>
                                                            <i class="fa fa-plus "></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                ${{ $cart->subtotal }}
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-danger"
                                                    onclick="deleteItem('{{ $cart->rowId }}')"><i
                                                        class="fa fa-times"></i></button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center">Your cart is empty.</td>
                                        </tr>
                                    @endforelse


                                </tbody>

                            </table>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card cart-summery">
                            <div class="sub-title">
                                <h2 class="bg-white">Cart Summery</h3>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-between pb-2">
                                    <div>Subtotal</div>
                                    <div>${{ Cart::subtotal() }}</div>
                                </div>
                                <div class="d-flex justify-content-between pb-2">
                                    <div>Shipping</div>
                                    <div>$0</div>
                                </div>
                                <div class="d-flex justify-content-between summery-end">
                                    <div>Total</div>
                                    <div>${{ Cart::subtotal() }}</div>
                                </div>
                                <div class="pt-5">
                                    <a href="{{ route('front.checkout') }}" class="btn-dark btn btn-block w-100">Proceed to Checkout</a>
                                </div>
                            </div>
                        </div>
                        {{-- <div class="input-group apply-coupan mt-4">
                        <input type="text" placeholder="Coupon Code" class="form-control">
                        <button class="btn btn-dark" type="button" id="button-addon2">Apply Coupon</button>
                    </div> --}}
                    </div>
                @else
                    <div class="col-md-12 text-center">
                        <h2>Your cart is empty.</h2>
                        <a href="{{ route('front.shop') }}" class="btn btn-dark mt-3">Continue Shopping</a>
                    </div>
                @endif
            </div>
        </div>
    </section>
@endsection

@section('customJs')
    <script>
        $(document).on('change', '.qty-input', function() {
            var qtyElement = $(this);
            var rowId = qtyElement.data('id'); // add this to the input in blade
            var qtyValue = parseInt(qtyElement.val());

            // Reject anything that isn't a valid positive integer
            if (isNaN(qtyValue) || qtyValue < 1) {
                qtyValue = 1;
            }
            if (qtyValue > 10) {
                qtyValue = 10; // or whatever your max cap is
            }

            qtyElement.val(qtyValue); // reflect the corrected value immediately
            updateCart(rowId, qtyValue);
        });

        // Optional: block non-numeric keystrokes entirely as they type
        $(document).on('input', '.qty-input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
        $('.add').click(function() {
            var qtyElement = $(this).parent().prev();
            var qtyValue = parseInt(qtyElement.val());
            if (qtyValue < 10) {
                qtyElement.val(qtyValue + 1);

                var rowId = $(this).data('id');
                var newQty = qtyElement.val();
                updateCart(rowId, newQty);
            }
        });

        $('.sub').click(function() {
            var qtyElement = $(this).parent().next();
            var qtyValue = parseInt(qtyElement.val());
            if (qtyValue > 1) {
                qtyElement.val(qtyValue - 1);

                var rowId = $(this).data('id');
                var newQty = qtyElement.val();
                updateCart(rowId, newQty);
            }
        });

        function updateCart(productId, qty) {
            $.post({
                url: '{{ route('front.updateCart') }}',
                data: {
                    rowId: productId,
                    qty: qty
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status) {
                        location.reload();
                    } else {
                        location.reload();
                    }
                },
            })
        }

        function deleteItem(rowId) {
            if (!confirm('Are you sure you want to remove this item from the cart?')) {
                return;
            }
            $.post({
                url: '{{ route('front.deleteItem.cart') }}',
                data: {
                    rowId: rowId
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status) {
                        location.reload();
                    }
                },
            })
        }
    </script>
@endsection
