@extends('admin.layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid my-2">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Shipping Management</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="{{ route('categories.index') }}" class="btn btn-primary">Back</a>
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
                                                <option value="{{ $country->id }}">{{ $country->name }}</option>
                                            @endforeach
                                        @endif
                                        <option value="rest_of_the_world">Rest of the World</option>
                                    </select>
                                    <p></p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <input type="number" name="amount" id="amount" class="form-control"
                                        placeholder="Amount" min="0">
                                    <p></p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-primary">Create</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <table class="table table-hover text-nowrap">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Amount</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($shippingCharges as $key => $shipping)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $shipping->country_id == 'rest_of_the_world' ? 'Rest of the World' : $shipping->name }}
                                            </td>
                                            <td>${{ $shipping->amount }}</td>
                                            <td>
                                                <a href="{{ route('shipping.edit', $shipping->id) }}" class="btn btn-primary">
                                                    Edit
                                                </a>
                                                <a href="javascript:void(0)" onclick="deleteCharge({{ $shipping->id }})" class="btn btn-danger">
                                                    Delete
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5">Records not found</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
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
                url: '{{ route('shipping.store') }}',
                type: 'POST',
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

        function deleteCharge(id) {
            let url = '{{ route("shipping.delete", "ID") }}';
            let newUrl = url.replace('ID', id);

            if (confirm("Are you sure want to delete?")) {
                $.ajax({
                    url : newUrl,
                    type: 'DELETE',
                    dataType: 'json',

                    success: function(response) {
                        location.reload();
                    }
                })
            }
        }
    </script>
@endsection
