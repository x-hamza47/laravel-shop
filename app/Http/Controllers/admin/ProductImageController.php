<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
// use Illuminate\Support\Str;
use Nette\Utils\Image;

class ProductImageController extends Controller
{
  public function update (Request $request) {

    $image = $request->image;
    $ext = $image->extension();
    $source_path = $image->getPathName();

    $product_image = new ProductImage();
    $product_image->product_id = $request->product_id;
    $product_image->image = 'NULL';
    $product_image->save();

    $image_name = $request->product_id.'-'.$product_image->id.'-'.time().'.'.$ext;
    $product_image->image = $image_name;
    $product_image->save();


        // large Image
        $dest_path = public_path().'/uploads/products/large/'.$image_name;
        $image = Image::fromFile($source_path);
        $image->resize(1400, null);
        $image->save($dest_path);

        // small Image
        $dest_path = public_path().'/uploads/products/small/'.$image_name;
        $image = Image::fromFile($source_path);
        $image->resize(300, 300, Image::Cover);
        $image->save($dest_path);

        return response()->json([
            'status' => true,
            'image_id' => $product_image->id,
            'imagePath' => asset('/uploads/products/small/'.$product_image->image),
            'message' => 'Image saved Successfully',
        ]);
  }


  public function destroy (Request $request) {
        $pro_img = ProductImage::find($request->id);

        if(empty($pro_img)) {
            return response()->json([
                'status' => false,
                'message' => 'Image not found!',
            ]);
        }

        // delete images from folder

        File::delete(public_path('/uploads/products/large/'.$pro_img->image));
        File::delete(public_path('/uploads/products/small/'.$pro_img->image));

        $pro_img->delete();

        return response()->json([
            'status' => true,
            'message' => 'Record Image deleted Successfully',
        ]);
  }

}
