<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Brands;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\SubCategory;
use App\Models\TempImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Nette\Utils\Image;

class ProductController extends Controller
{

    public function index(Request $request) {
        $products = Product::latest('id')->with('product_images');

        if (!empty($request->get('keyword'))) {
            $products = $products->where('title' ,'LIKE' ,'%'.$request->get('keyword').'%');
        }

        $products = $products->paginate(10);
        return view('admin.products.list',compact('products'));
    }

    public function create () {
        $categories = Category::orderBy('name','ASC')->get();
        $brands = Brands::orderBy('name','ASC')->get();
        return view('admin.products.create',compact('categories','brands'));
    }

    public function productSubIndex (Request $req) {

        if (!empty($req->category_id)) {
            
            $sub_categories = SubCategory::where('category_id',$req->category_id)
            ->orderBy('name','ASC')
            ->get();
     
            return response()->json([
             'status' => true,
             'subCategories' => $sub_categories,
            ]);

        }else{
            return response()->json([
                'status' => true,
                'subCategories' => [],
            ]);
        }
    }

    public function store(Request $request) {
        
        $rules = [
            'title' => 'required',
            'slug' => 'required|unique:products',
            'price' => 'required|numeric',
            'sku' => 'required|unique:products',
            'track_qty' => 'required|in:Yes,No',
            'category' => 'required|numeric',
            'is_featured' => 'required|in:Yes,No',
        ];

        if (!empty($request->track_qty) && $request->track_qty == 'Yes') {
            $rules['qty'] = 'required|numeric';
        }

        $validator = Validator::make($request->all(),$rules);

        if ($validator->passes()) {

            $product = new Product();

            $product->title = $request->title;
            $product->slug = $request->slug;
            $product->description = $request->description;
            $product->price = $request->price;
            $product->compare_price = $request->compare_price;
            $product->sku = $request->sku;
            $product->barcode = $request->barcode;
            $product->track_qty = $request->track_qty;
            $product->qty = $request->qty;
            $product->status = $request->status;
            $product->category_id = $request->category;
            $product->sub_category_id = $request->sub_category;
            $product->brand_id = $request->brand;
            $product->is_featured = $request->is_featured;
            $product->save() ;
 


            // Save gallery Pics
            if (!empty($request->image_array)) {
                foreach ($request->image_array as $temp_image_id) {

                    $temp_image_info = TempImage::find($temp_image_id);
                    $ext_array = explode('.', $temp_image_info->name);
                    $ext = last($ext_array); //for extensions 


                    $product_image = new ProductImage();
                    $product_image->product_id = $product->id;
                    $product_image->image = 'NULL';
                    $product_image->save();

                    $image_name = $product->id.'-'.$product_image->id.'-'.time().'.'.$ext;
                    $product_image->image = $image_name;
                    $product_image->save();

 
                    // large Image
                    $source_path = public_path().'/temp/'.$temp_image_info->name;
                    $dest_path = public_path()."/uploads/products/large/".$image_name;
                    $image = Image::fromFile($source_path);
                    $image->resize(1400, null);
                    $image->save($dest_path);

                    // small Image
                    $dest_path = public_path()."/uploads/products/small/".$image_name;
                    $image = Image::fromFile($source_path);
                    $image->resize(300, 300, Image::Cover);
                    $image->save($dest_path);
                }
            }


            session()->flash('success','Product added successfully');

            return response()->json([
                'status' => true,
                'message' => 'Product added successfully',
            ]);

        }else{
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
        
    }

        #------ Edit Products
        public function edit($product_id, Request $request) {

            $product = Product::find($product_id);


            if (empty($product)) {
                return redirect()->route('products.index')->with('error','Product not found!');
            }

            // Product Images
            $pro_imgs = ProductImage::where('product_id',$product->id)->get();



            $sub_category = SubCategory::where('category_id',$product->category_id)->get();
    
  
            $categories = Category::orderBy('name','ASC')->get();
            $brands = Brands::orderBy('name','ASC')->get();
            return view('admin.products.edit',compact('categories','brands','product','sub_category','pro_imgs'));
    
 
        }

            #------ Update Product
    public function update ($product_id, Request $request) {

        $product = Product::find($product_id);

        $rules = [
            'title' => 'required',
            'slug' => 'required|unique:products,slug,'.$product->id.',id',
            'price' => 'required|numeric',
            'sku' => 'required|unique:products,sku,'.$product->id.',id',
            'track_qty' => 'required|in:Yes,No',
            'category' => 'required|numeric',
            'is_featured' => 'required|in:Yes,No',
        ];

        if (!empty($request->track_qty) && $request->track_qty == 'Yes') {
            $rules['qty'] = 'required|numeric';
        }

        $validator = Validator::make($request->all(),$rules);

        if ($validator->passes()) {

            $product->title = $request->title;
            $product->slug = $request->slug;
            $product->description = $request->description;
            $product->price = $request->price;
            $product->compare_price = $request->compare_price;
            $product->sku = $request->sku;
            $product->barcode = $request->barcode;
            $product->track_qty = $request->track_qty;
            $product->qty = $request->qty;
            $product->status = $request->status;
            $product->category_id = $request->category;
            $product->sub_category_id = $request->sub_category;
            $product->brand_id = $request->brand;
            $product->is_featured = $request->is_featured;
            $product->save() ;
          

            session()->flash('success','Product Updated successfully');

            return response()->json([
                'status' => true,
                'message' => 'Product updated successfully',
            ]);

        }else{
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }

    }

    public function destroy ($id, Request $request) {
    
            $product = Product::find($id);
    
            if (empty($product)){
                session()->flash('error','Product not found!');
                return response()->json([
                    'status' => false,
                    'notFound' => true,
                ]);
            }
            $pro_img = ProductImage::where('product_id',$id)->get();

            if (!empty($pro_img)){

                foreach ($pro_img as $img) {
                    File::delete(public_path().'/uploads/products/large/'.$img->image);
                    File::delete(public_path().'/uploads/products/small/'.$img->image);
                }

                ProductImage::where('product_id',$id)->delete();
      
            }
            $product->delete();

            session()->flash('success','Product deleted successfully');
    
            return response()->json([
                'status' => true,
                'notFound' => false,
            ]);
    }        
}
