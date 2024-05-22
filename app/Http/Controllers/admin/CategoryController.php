<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\TempImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Nette\Utils\Image;


class CategoryController extends Controller
{
    public function index(Request $request) {
        $categories = Category::latest('id');

        if (!empty($request->get('keyword'))) {
            $categories = $categories->where('name' ,'LIKE' ,'%'.$request->get('keyword').'%');
        }

        $categories = $categories->paginate(10);
        return view('admin.category.list',compact('categories'));
    }

    public function create() {
        return view('admin.category.create');
    }


    #---- Input categories 
    public function store(Request $request) {
        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'slug' => 'required|unique:categories',
        ]);

        if ($validator->passes()) {

            $category = new Category();

            $category->name = $request->name;
            $category->slug = $request->slug;
            $category->status = $request->status;
            $category->save();

            // Save Image

            if (!empty($request->image_id)) {
                    $temp_image = TempImage::find($request->image_id);
                    $extArray = explode("." , $temp_image->name);
                    $ext = last($extArray);

                    $new_image_name = $category->id.".".$ext;
                    $sPath = public_path(). '/temp/'. $temp_image->name;
                    $dPath = public_path(). '/uploads/category/'. $new_image_name;
                    File::copy($sPath, $dPath);

                    // Generate thumbnail
                    $desPath = public_path(). '/uploads/category/thumb/'. $new_image_name;
                    $image_thumb = Image::fromFile($sPath);
                    $image_thumb->resize(450,600, Image::Cover);
                    $image_thumb->save($desPath);
                    
                    $category->image = $new_image_name;
                    $category->save();
            }


           session()->flash('success','Category added successfully');

            return response()->json([
                'status' => true,
                'message' => 'Category added successfully'
            ]);

        } else{
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }

    }

    #------ Edit Categories
    public function edit($category_id, Request $request) {

        $category = Category::find($category_id);

        if (empty($category)) {
            return redirect()->route('categories.index');
        }

        return view('admin.category.edit', compact('category'));
    }

    #------ Update Categories
    public function update ($category_id, Request $request) {

        $category = Category::find($category_id);

        if (empty($category)) {

            session()->flash('error','Category not found!');

            return response()->json([
                'status' => false,
                'notFound' => true,
                'message' => 'Category not found!',
            ]);
        }


        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'slug' => 'required|unique:categories,slug,'.$category->id.',id',
        ]);

        if ($validator->passes()) {

            $category->name = $request->name;
            $category->slug = $request->slug;
            $category->status = $request->status;
            $category->save();

            $old_image = $category->image;

            // Save Image

            if (!empty($request->image_id)) {
                    $temp_image = TempImage::find($request->image_id);
                    $extArray = explode("." , $temp_image->name);
                    $ext = last($extArray);

                    $new_image_name = $category->id.'-'.time().".".$ext;
                    $sPath = public_path(). '/temp/'. $temp_image->name;
                    $dPath = public_path(). '/uploads/category/'. $new_image_name;
                    File::copy($sPath, $dPath);

                    // Generate thumbnail
                    $desPath = public_path(). '/uploads/category/thumb/'. $new_image_name;
                    $image_thumb = Image::fromFile($sPath);
                    $image_thumb->resize(450,600, Image::Cover);
                    $image_thumb->save($desPath);
                    
                    $category->image = $new_image_name;
                    $category->save();

                    // Delete Old Images Here
                    File::delete(public_path().'/uploads/category/'.$old_image);
                    File::delete(public_path().'/uploads/category/thumb/'.$old_image);
            }


            session()->flash('success','Category updated successfully');

            return response()->json([
                'status' => true,
                'message' => 'Category updated successfully'
            ]);

        } else{
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }



    }

    public function destroy($category_id, Request $request) {
        $category = Category::find($category_id);

        if (empty($category)){
            session()->flash('error','Category not found!');
            return response()->json([
                'status' => true,
                'message' => 'Category not found!',
            ]);
        }
        File::delete(public_path().'/uploads/category/thumb/'.$category->image);
        File::delete(public_path().'/uploads/category/'.$category->image);

        $category->delete();
        session()->flash('success','Category deleted successfully');


        return response()->json([
            'status' => true,
            'message' => 'Category deleted successfully',
        ]);

    }
}
