@extends('admin.layouts.app')

@section('content')
    				<!-- Content Header (Page header) -->
                    <section class="content-header">					
                        <div class="container-fluid my-2">
                            <div class="row mb-2">
                                <div class="col-sm-6">
                                    <h1>Create Product</h1>
                                </div>
                                <div class="col-sm-6 text-right">
                                    <a href="{{ route('products.index') }}" class="btn btn-primary">Back</a>
                                </div>
                            </div>
                        </div>
                        <!-- /.container-fluid -->
                    </section>
                    <!-- Main content -->
                    <section class="content">
                        <!-- Default box -->
                        <form action="" method="POST" name="productForm" id="productForm">
                            <div class="container-fluid">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="card mb-3">
                                            <div class="card-body">								
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="mb-3">
                                                            <label for="title">Title</label>
                                                            <input type="text" value="{{ $product->title }}" name="title" id="title" class="form-control" placeholder="Title">
                                                            <p class="error"></p>	
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="mb-3">
                                                            <label for="slug">Slug</label>
                                                            <input type="text" value="{{ $product->slug }}" name="slug" id="slug" class="form-control" placeholder="Slug" readonly>	
                                                            <p class="error"></p>	
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="mb-3">
                                                            <label for="description">Description</label>
                                                            <textarea name="description" id="description" cols="30" rows="10" class="summernote" placeholder="Description">{{ $product->description }}</textarea>
                                                        </div>
                                                    </div>                                            
                                                </div>
                                            </div>	                                                                      
                                        </div>
                                        <div class="card mb-3">
                                            <div class="card-body">
                                                <h2 class="h4 mb-3">Media</h2>								
                                                <div id="image" class="dropzone dz-clickable">
                                                    <div class="dz-message needsclick">    
                                                        <br>Drop files here or click to upload.<br><br>                                            
                                                    </div>
                                                </div>
                                            </div>	                                                                      
                                        </div>
                                        <div class="row" id="product-gallery">
                                            @if ($pro_imgs->isNotEmpty())
                                                @foreach ($pro_imgs as $img)
                                                <div class="col-md-3"> 
                                                    <div class="card" id='image-row-{{ $img->id }}'>
                                                        <input type="hidden" name="image_array[]" value="{{ $img->id }}">
                                                        <img src="{{ asset('uploads/products/small/'.$img->image) }}" class="card-img-top" alt="">
                                                        <div class="card-body">
                                                            <a href="javascript:void(0)" class="btn btn-danger" data-image-id={{ $img->id }}>Delete</a>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endforeach
                                            @endif
                                        </div>

                                        <div class="card mb-3">
                                            <div class="card-body">
                                                <h2 class="h4 mb-3">Pricing</h2>								
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="mb-3">
                                                            <label for="price">Price</label>
                                                            <input type="text" name="price" value="{{ $product->price }}" id="price" class="form-control" placeholder="Price">	
                                                            <p class="error"></p>	
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="mb-3">
                                                            <label for="compare_price">Compare at Price</label>
                                                            <input type="text" name="compare_price" value="{{ $product->compare_price }}" id="compare_price" class="form-control" placeholder="Compare Price">
                                                            <p class="text-muted mt-3">
                                                                To show a reduced price, move the product’s original price into Compare at price. Enter a lower value into Price.
                                                            </p>	
                                                        </div>
                                                    </div>                                            
                                                </div>
                                            </div>	                                                                      
                                        </div>
                                        <div class="card mb-3">
                                            <div class="card-body">
                                                <h2 class="h4 mb-3">Inventory</h2>								
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="mb-3">
                                                            <label for="sku">SKU (Stock Keeping Unit)</label>
                                                            <input type="text" name="sku" value="{{ $product->sku }}" id="sku" class="form-control" placeholder="sku">	
                                                            <p class="error"></p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="mb-3">
                                                            <label for="barcode">Barcode</label>
                                                            <input type="text" name="barcode" value="{{ $product->barcode }}" id="barcode" class="form-control" placeholder="Barcode">	
                                                        </div>
                                                    </div>   
                                                    <div class="col-md-12">
                                                        <div class="mb-3">
                                                            <div class="custom-control custom-checkbox">
                                                                <input type="hidden" name="track_qty" value="No">
                                                                <input class="custom-control-input" type="checkbox" id="track_qty" name="track_qty" {{ ($product->track_qty == 'Yes') ? 'checked' : '' }} value="Yes">
                                                                <label for="track_qty" class="custom-control-label">Track Quantity</label>
                                                                <p class="error"></p>	
                                                            </div>
                                                        </div>
                                                        <div class="mb-3">
                                                            <input type="number" value="{{ $product->qty }}" min="0" name="qty" id="qty" class="form-control" placeholder="Qty">	
                                                            <p class="error"></p>	
                                                        </div>
                                                    </div>                                         
                                                </div>
                                            </div>	                                                                      
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="card mb-3">
                                            <div class="card-body">	
                                                <h2 class="h4 mb-3">Product status</h2>
                                                <div class="mb-3">
                                                    <select name="status" id="status" class="form-control">
                                                        <option {{ ($product->status == 1) ? 'selected' : '' }} value="1">Active</option>
                                                        <option {{ ($product->status == 0) ? 'selected' : '' }} value="0">Block</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div> 
                                        <div class="card">
                                            <div class="card-body">	
                                                {{-- Categories --}}
                                                <h2 class="h4  mb-3">Product category</h2>

                                                <div class="mb-3">
                                                    <label for="category">Category</label>
                                                    <select name="category" id="category" class="form-control">
                                                        <option>--- Select a Category ---</option>
                                                        @if ($categories->isNotEmpty())
                                                            @foreach ($categories as $category)

                                                            <option {{ ($product->category_id == $category->id) ? 'selected' : '' }} value="{{ $category->id }}">{{ $category->name }}</option>

                                                            @endforeach
                                                        @endif
                                                    </select>
                                                    <p class="error"></p>	
                                                </div>
                                                <div class="mb-3">
                                                    <label for="category">Sub category</label>
                                                    <select name="sub_category" id="sub_category" class="form-control">
                                                        <option value="">--- Select a Category ---</option>
                                                        @if ($sub_category->isNotEmpty())
                                                        @foreach ($sub_category as $subCategory)

                                                        <option {{ ($product->sub_category_id == $subCategory->id) ? 'selected' : '' }} value="{{ $subCategory->id }}">{{ $subCategory->name }}</option>

                                                        @endforeach
                                                    @endif
                                                    </select>
                                                </div>
                                            </div>
                                        </div> 
                                        <div class="card mb-3">
                                            <div class="card-body">	
                                                <h2 class="h4 mb-3">Product brand</h2>
                                                <div class="mb-3">
                                                    <select name="brand" id="brand" class="form-control">
                                                        <option value="">--- Select a Brand ---</option>
                                                        @if ($brands->isNotEmpty())
                                                            @foreach ($brands as $brand)

                                                            <option {{ ($product->brand_id == $brand->id) ? 'selected' : '' }}
                                                             value="{{ $brand->id }}">{{ $brand->name }}</option>

                                                            @endforeach
                                                        @endif
                                                    </select>
                                                </div>
                                            </div>
                                        </div> 
                                        <div class="card mb-3">
                                            <div class="card-body">	
                                                <h2 class="h4 mb-3">Featured product</h2>
                                                <div class="mb-3">
                                                    <select name="is_featured" id="is_featured" class="form-control">
                                                        <option {{ ($product->is_featured == 'No') ? 'selected' : '' }} value="No">No</option>
                                                        <option {{ ($product->is_featured == 'Yes') ? 'selected' : '' }} value="Yes">Yes</option>                                                
                                                    </select>
                                                    <p class="error"></p>	
                                                </div>
                                            </div>
                                        </div>                                 
                                    </div>
                                </div>
                                
                                <div class="pb-5 pt-3">
                                    <button type="submit" class="btn btn-primary">Create</button>
                                    <a href="{{ route('products.index') }}" class="btn btn-outline-dark ml-3">Cancel</a>
                                </div>
                            </div>
                    </form>
                        <!-- /.card -->
                    </section>
                    <!-- /.content -->
@endsection

@section('customJs')

<script>

$('#productForm').submit(function(e){
    e.preventDefault();
    var element = $(this).serializeArray();
    $("button[type='submit']").prop('disabled', true);
        $.ajax({
            url : '{{ route("products.update",$product->id) }}',
            type : 'PUT',
            data : element,
            dataType : 'json',
            success: function(response){
                $("button[type='submit']").prop('disabled', false);

                if(response['status'] == true) {
                    $('.error').removeClass('invalid-feedback').html('');
                    $("input[type='text'], select, input[type='number']").removeClass('is-invalid');
                    window.location.href = "{{ route('products.index') }}";

                }else{
                    var errors = response['errors'];

                    $('.error').removeClass('invalid-feedback').html('');
                    $("input[type='text'], select, input[type='number']").removeClass('is-invalid');

                    $.each(errors, function(key,value) {
                        $(`#${key}`).addClass('is-invalid')
                        .siblings('p')
                        .addClass('invalid-feedback').html(value);
                    });
                }
            }
            ,error:function(jqXHR, exception) {
            console.error("Something went wrong");
        }
        });
});


$('#category').change(function(){
    let category_id = $(this).val();
    $.ajax({
            url : '{{ route("product-sub-category.index") }}',
            type : 'GET',
            data : { category_id: category_id},
            dataType : 'json',
            success: function(response){
                $('#sub_category').find('option').not(':first').remove();
                $.each(response['subCategories'],function(key, item){
                    $('#sub_category').append(`<option value="${item.id}">${item.name}</option>`);
                });
            }
            ,error:function(jqXHR, exception) {
            console.error("Something went wrong");
            }
    });
});

$("#title").change(function(){
    ele = $(this);
    $("button[type=submit]").prop('disabled', true);

$.ajax({
        url : '{{ route("getSlug")}}',
        type : 'get',
        data : {title : ele.val()},
        dataType : 'json',
        success: function(response){
            $("button[type=submit]").prop('disabled', false);

            if (response['status'] == true) {
                $('#slug').val(response['slug']);
            }
        }

    });
});

Dropzone.autoDiscover = false;    
const dropzone = $("#image").dropzone({ 
    url:  "{{ route('product-images.update') }}",
    maxFiles: 10,
    paramName: 'image',
    addRemoveLinks: true,
    params: { 'product_id' : '{{ $product->id }}' },
    acceptedFiles: "image/jpeg,image/png,image/gif",
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }, success: function (file, response) {

            var html = `<div class="col-md-3"> 
                            <div class="card" id='image-row-${response.image_id}'>
                                <input type="hidden" name="image_array[]" value="${response.image_id}">
                                <img src="${response.imagePath}" class="card-img-top" alt="">
                                <div class="card-body">
                                    <a href="javascript:void(0)" class="btn btn-danger" data-image-id=${response.image_id}>Delete</a>
                                </div>
                            </div>
                        </div>`;

                $('#product-gallery').append(html);
    },
        complete:function(file){
            this.removeFile(file);
        }
});

$('#product-gallery').on('click','a',(function(){

    var Image = $(this).data('image-id');

    if(confirm("Are you sure you want to delete image?")){
            $.ajax({
        url : "{{ route('product-images.destroy') }}" ,
        type : 'DELETE',
        data : {id : Image},
        success : function (response) {
            if(response.status == true) {
                $('#image-row-'+Image).slideUp('slow',function(){
                $(this).parent().remove();
                });
                alert(response.message);
            }else{
                alert(response.message);
            }
        }
    });
    }

    })
);

</script>
    
@endsection

