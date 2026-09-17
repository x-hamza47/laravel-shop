<?php

namespace App\Http\Controllers;

use App\Models\Brands;
use App\Models\Category;
use App\Models\Product;
use App\Models\SubCategory;
use Illuminate\Http\Request;

class shopController extends Controller
{
    public function index(Request $request, $category_slug = null, $subCategory_slug = null)
    {
        $category_selected = "";
        $sub_category_selected = "";
        $brands_array = [];

        $categories = Category::orderBy('name', 'ASC')->with('sub_category')->where('status', '1')->get();
        $brands = Brands::orderBy('name', 'ASC')->where('status', '1')->get();

        $products = Product::where('status', '1');
        // $products = Product::orderBy('id','DESC')->where('status','1')->get();

        // Apply Filters Here
        if (!empty($category_slug)) {
            $category = Category::where('slug', $category_slug)->first();
            $products = $products->where('category_id', $category->id);
            $category_selected = $category->id;
        }
        if (!empty($subCategory_slug)) {
            $sub_category = SubCategory::where('slug', $subCategory_slug)->first();
            $products = $products->where('sub_category_id', $sub_category->id);
            $sub_category_selected = $sub_category->id;
        }

        if (!empty($request->get('brand'))) {
            $brands_array = explode(',', $request->get('brand'));
            $products = $products->whereIn('brand_id', $brands_array);
        }

        if ($request->get('price_max') != "" && $request->get('price_min') != "") {
            if ($request->get('price_max') == 1000) {
                $products = $products->whereBetween('price', [intval($request->get('price_min')), 1000000]);
            } else {
                $products = $products->whereBetween('price', [intval($request->get('price_min')), intval($request->get('price_max'))]);
            }
        }

        if ($request->get('sort') != "") {
            if ($request->get('sort') == 'price_asc') {
                $products = $products->orderBy('price', 'ASC');
            } elseif ($request->get('sort') == 'price_desc') {
                $products = $products->orderBy('price', 'DESC');
            } else {

                $products = $products->orderBy('id', 'DESC');
            }
        } else {

            $products = $products->orderBy('id', 'DESC');
        }
        $products = $products->paginate(6)->withQueryString();

        $price_min = intval($request->get('price_min'));
        $price_max = (intval($request->get('price_max')) == 0 ? 1000 : $request->get('price_max'));
        $sort_selected = $request->get('sort') != "" ? $request->get('sort') : 'latest';

        return view('front.shop', compact(
            'categories',
            'brands',
            'products',
            'category_selected',
            'sub_category_selected',
            'brands_array',
            'price_min',
            'price_max',
            'sort_selected'
        ));
    }

    public function product(String $slug)
    {
        $product = Product::where('slug', $slug)->with('product_images')->firstOrFail();

        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('status', 1)
            ->with('product_images')
            ->limit(12)
            ->get();

        return view('front.product', compact('product', 'relatedProducts'));
    }
}
