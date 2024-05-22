<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\TempImage;
use Illuminate\Http\Request;
use Nette\Utils\Image;
use Illuminate\Support\Str;

class TempImagesController extends Controller
{
    public function create(Request $request){
        
        if ($request->image) {
            $image = $request->image;
            $ext = $image->extension();
            $new_name = Str::random(1,1000). time().".".$ext;

            $temp_image = new TempImage();
            $temp_image->name = $new_name;
            $temp_image->save();
            $image->move(public_path(). '/temp/' , $new_name);

            $sourcePath = public_path()."/temp/".$new_name;
            $destPath = public_path().'/temp/thumb/'.$new_name;

            // $image_thumb = Image::fromFile($sourcePath);
            // $image_thumb->resize(600, 600);
            // $image_thumb->save($destPath);
            

            return response()->json([
                'status' => true,
                'image_id' => $temp_image->id,
                // 'imagePath' => asset('/temp/thumb/'. $new_name),
                'message' => "Image uploaded successfully",
            ]);

        }
    }
}
