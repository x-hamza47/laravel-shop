@extends('admin.layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid my-2">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Edit Shipping Management</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="{{ route('shipping.create') }}" class="btn btn-primary">Back</a>
                </div>
            </div>
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- Main content -->
    <section class="content">
        <!-- Default box -->
        <div class="container-fluid">
            @include('admin.message')
            <form method="POST" id="shippingForm" name="shippingForm">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <select name="country" id="country" class="form-control">
                                        <option value="">Select a Country</option>
                                        @if ($countries->isNotEmpty())
                                            @foreach ($countries as $country)
                                                <option @selected($shippingCharge->country_id == $country->id) value="{{ $country->id }}">
                                                    {{ $country->name }}
                                                </option>
                                            @endforeach
                                        @endif

                                        <option @selected($shippingCharge?->country_id == 'rest_of_the_world') value="rest_of_the_world">
                                            Rest of the World
                                        </option>
                                    </select>
                                    <p></p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <input type="number" name="amount" id="amount" class="form-control"
                                        placeholder="Amount" min="0" value="{{ $shippingCharge?->amount }}">
                                    <p></p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-primary">Update</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <!-- /.card -->
    </section>
    <!-- /.content -->
@endsection

@section('customJs')
    <script>
        $('#shippingForm').submit(function(event) {
            event.preventDefault();
            var element = $(this);
            $("button[type=submit]").prop('disabled', true);
            $.ajax({
                url: '{{ route('shipping.update', $shippingCharge->id) }}',
                type: 'PUT',
                data: element.serializeArray(),
                dataType: 'json',
                success: function(response) {
                    $("button[type=submit]").prop('disabled', false);
                    $('.form-control').removeClass('is-invalid').siblings('p').removeClass(
                        'invalid-feedback').html('');

                    if (response.status) {
                        window.location.href = "{{ route('shipping.create') }}";
                    } else {
                        $.each(response.errors, function(field, message) {
                            $(`#${field}`).addClass('is-invalid').siblings('p').addClass(
                                'invalid-feedback').html(message[0]);
                        });
                    }

                },
                error: function(jqXHR, exception) {
                    console.log("Something went wrong");
                }
            });

        });
    </script>
@endsection
