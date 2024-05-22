<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Brands;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BrandsController extends Controller
{
    #---- Index(Listing Page)
    public function index(Request $request) {

        $brands = Brands::latest('id');

        if (!empty($request->get('keyword'))) {
            $brands = $brands->where('name' ,'LIKE' ,'%'.$request->get('keyword').'%');
        }

        $brands = $brands->paginate(10);
        return view('admin.brands.list',compact('brands'));

    }

    public function create () {
        return view('admin.brands.create');
    }

    #------ Edit Brands
    public function edit($brand_id) {

        $brand = Brands::find($brand_id);

        if (empty($brand)) {
            
            session()->flash('error','Brand not found!');
            return redirect()->route('brands.index');
        }

        return view('admin.brands.edit', compact('brand'));
    }


    public function store(Request $request) {
        
        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'slug' => 'required|unique:brands',
        ]);

        if ($validator->passes()) {

            $brands = new Brands();

            $brands->name = $request->name;
            $brands->slug = $request->slug;
            $brands->status = $request->status;
            $brands->save();
            session()->flash('success','Brand added successfully');

            return response()->json([
                'status' => true,
                'message' => 'Brand added successfully',
            ]);

        }else{
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    public function update($brand_id, Request $request) {

        $brand = Brands::find($brand_id);

        if (empty($brand)) {
            
            session()->flash('error','Record not found!');
            return response()->json([
                'status' => false,
                'notFound' => true,
                'message' => 'Brand not found!',
            ]);

        }

        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'slug' => 'required|unique:brands,slug,'.$brand->id.',id',
        ]);

        if ($validator->passes()) {

            $brand->name = $request->name;
            $brand->slug = $request->slug;
            $brand->status = $request->status;
            $brand->save();
            session()->flash('success','Brand updated successfully');

            return response()->json([
                'status' => true,
                'message' => 'Brand updated successfully',
            ]);


        }else{
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
        
    }


    public function destroy ($id) {

        $brand = Brands::find($id);

        if (empty($brand)){
            session()->flash('error','Record not found!');
            return response([
                'status' => true,
                'message' => 'Brand not found!',
            ]);
        }

        $brand->delete();
        session()->flash('success','Brand deleted successfully');


        return response([
            'status' => true,
            'message' => 'Brand deleted successfully',
        ]);

    }
}
