<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function addToCart(Request $request)
    {
        $product = Product::with('product_images')->find($request->id);

        if ($product == null) {
            return response()->json(['status' => false, 'message' => 'Product not found']);
        }

        $productAlreadyExists = Cart::content()->where('id', $product->id)->isNotEmpty();

        if ($productAlreadyExists) {
            return response()->json(['status' => false, 'message' => $product->title . ' already exists in the cart']);
        }

        Cart::add($product->id, $product->title, 1, $product->price, ['product_images' => $product->product_images->first() ?? '']);

        return response()->json(['status' => true, 'message' => $product->title . ' added to cart successfully']);

    }

    public function cart()
    {
        $carts = Cart::content();

        return view('front.cart', compact('carts'));
    }
}
