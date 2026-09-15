<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class FrontController extends Controller
{
    public function index () {

       $featured_products = Product::where('is_featured','Yes')->orderBy('id','DESC')->where('status','1')->get();
       $latest_products = Product::orderBy('id','DESC')->where('status','1')->take(8)->get();

        return view('front.home',compact('featured_products','latest_products'));
    }
}
