<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SubCategoryController extends Controller
{
    public function index(Request $request) {
        $sub_categories = SubCategory::select('sub_categories.*', 'categories.name as categoryName')
                                    ->latest('sub_categories.id')
                                    ->leftJoin('categories', 'categories.id' , 'sub_categories.category_id');

        if (!empty($request->get('keyword'))) {
            $sub_categories = $sub_categories->where('sub_categories.name' ,'LIKE' ,'%'.$request->get('keyword').'%')
            ->orwhere('categories.name' ,'LIKE' ,'%'.$request->get('keyword').'%');;
        }

        $sub_categories = $sub_categories->paginate(10);
        return view('admin.sub_category.list',compact('sub_categories'));
    }

    public function create() {
        $categories = Category::orderBy('name','ASC')->get();
        return view('admin.sub_category.create',compact('categories'));
    }
    
    # ----------Insert Data
    public function store (Request $request) {
        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'slug' => 'required|unique:sub_categories',
            'category' => 'required',
            'status' => 'required',
        ]);

        if ($validator->passes()) {
            $subCategory = new SubCategory();

            $subCategory->name = $request->name;
            $subCategory->slug = $request->slug;
            $subCategory->status = $request->status;
            $subCategory->category_id = $request->category;
            $subCategory->save();

            session()->flash('success','Sub Category created successfully');

            return response()->json([
                'status' => true,
                'message' => 'Sub Category created successfully'
            ]);


        }else{
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }


    public function edit($id, Request $request) {

        $subCategory = SubCategory::find($id);

        if (empty($subCategory)) {
            session()->flash('error','Record not found!');
            return redirect()->route('sub-categories.index');
        }

        $categories = Category::orderBy('name','ASC')->get();
        return view('admin.sub_category.edit',compact('categories','subCategory'));
    }

    public function update ($id, Request $request) {

        $subCategory = SubCategory::find($id);

        if (empty($subCategory)) {
            session()->flash('error','Record not found!');

            return response([
                'status' => false,
                'notFound' => true,
            ]);
        }


        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'slug' => 'required|unique:sub_categories,slug,'.$subCategory->id.',id',
        ]);

        if ($validator->passes()) {

            $subCategory->name = $request->name;
            $subCategory->slug = $request->slug;
            $subCategory->status = $request->status;
            $subCategory->category_id = $request->category;
            $subCategory->save();

            session()->flash('success','Sub Category updated successfully');

            return response([
                'status' => true,
                'message' => 'Sub Category updated successfully'
            ]);

        } else{
            return response([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }



    }

    public function destroy($id, Request $request) {
        $subCategory = SubCategory::find($id);

        if (empty($subCategory)){
            session()->flash('error','Record not found!');
            return response([
                'status' => true,
                'message' => 'Sub Category not found!',
            ]);
        }

        $subCategory->delete();
        session()->flash('success','Sub Category deleted successfully');


        return response([
            'status' => true,
            'message' => 'Sub Category deleted successfully',
        ]);

    }
    
}
