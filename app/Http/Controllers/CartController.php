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
        session()->flash('success', $product->title . ' added to cart successfully');
        return response()->json(['status' => true, 'message' => $product->title . ' added to cart successfully']);
    }

    public function cart()
    {
        $carts = Cart::content();

        return view('front.cart', compact('carts'));
    }

    public function updateCart(Request $request)
    {
        $request->validate([
            'rowId' => 'required|string',
            'qty'   => 'required|integer|min:1',
        ]);

        $rowId = $request->rowId;
        $qty = (int) $request->qty;

        $itemInfo = Cart::get($rowId);
        if (!$itemInfo) {
            return response()->json(['status' => false, 'message' => 'Item not found in cart'], 404);
        }

        $product = Product::find($itemInfo->id);
        if (!$product) {
            return response()->json(['status' => false, 'message' => 'Product no longer exists'], 404);
        }

        $message = 'Cart updated successfully';

        if ($product->track_qty == 'Yes' && $qty > $product->qty) {
            Cart::update($rowId, $product->qty);
            $message = 'Only ' . $product->qty . ' item(s) available in stock';
            session()->flash('error', $message);
        } else {
            Cart::update($rowId, $qty);
            session()->flash('success', $message);
        }

        return response()->json(['status' => true, 'message' => $message]);
    }

    public function deleteItem(Request $request)
    {
        $rowId = $request->rowId;

        $itemInfo = Cart::get($rowId);
        if (!$itemInfo) {
            session()->flash('error', 'Item not found in cart');
            return response()->json(['status' => false, 'message' => 'Item not found in cart'], 404);
        }

        Cart::remove($rowId);
        session()->flash('success', 'Item removed from cart successfully');

        return response()->json(['status' => true, 'message' => 'Item removed from cart successfully']);
    }
}
