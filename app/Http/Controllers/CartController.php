<?php

namespace App\Http\Controllers;

use App\Models\CustomerAddress;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

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

    public function checkout()
    {

        if (Cart::count() == 0) {
            return redirect()->route('front.cart');
        }

        if (!Auth::check()) {
            return redirect()->route('account.login.show')->with('error', "You need to Log In first!");
        }

        $customerAddress = CustomerAddress::where('user_id', Auth::id())->first();
        $countries = DB::table('countries')->orderBy('name')->get();

        return view('front.checkout', compact('countries', 'customerAddress'));
    }

    public function processCheckout(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|min:5',
            'last_name' => 'required',
            'email' => 'required|email',
            'country' => 'required',
            'address' => 'required|min:30',
            'city' => 'required',
            'state' => 'required',
            'zip' => 'required',
            'mobile' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => 'Please fix the errors', 'errors' => $validator->errors()]);
        }

            $userId = Auth::id();
            CustomerAddress::updateOrCreate(
                ['user_id' => $userId],
                [
                    'user_id' => $userId,
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'email' => $request->email,
                    'mobile' => $request->mobile,
                    'country_id' => $request->country,
                    'address' => $request->address,
                    'apartment' => $request->apartment,
                    'city' => $request->city,
                    'state' => $request->state,
                    'zip' => $request->zip,
                ]
            );

        if ($request->payment_method == 'cod') {

            $shipping = 0;
            $dicount = 0;
            $subTotal = Cart::subtotal(2, '.', '');
            $grandTotal = $subTotal + $shipping;

            $order = new Order;
            $order->subtotal = $subTotal;
            $order->shipping = $shipping;
            $order->grand_total = $grandTotal;

            // User Details
            $order->user_id = $userId;
            $order->first_name = $request->first_name;
            $order->last_name = $request->last_name;
            $order->email = $request->email;
            $order->mobile = $request->mobile;
            $order->country_id = $request->country;
            $order->address = $request->address;
            $order->apartment = $request->apartment;
            $order->city = $request->city;
            $order->state = $request->state;
            $order->zip = $request->zip;
            $order->notes = $request->order_notes;
            $order->save();

            // store order items
            foreach (Cart::content() as $item) {
                $orderItem = new OrderItem;

                $orderItem->order_id = $order->id;
                $orderItem->product_id = $order->id;
                $orderItem->name = $item->name;
                $orderItem->qty = $item->qty;
                $orderItem->price = $item->price;
                $orderItem->total = $item->subtotal;

                $orderItem->save();
            }
            session()->flash('success', 'Orders Saved Successfully');
            Cart::destroy();
            return response()->json(['status' => true, 'message' => 'Orders Saved Successfully', 'orderId' => $order->id]);
        } else {
        }
    }

    public function thankYou($id){
        return view('front.thanks', compact('id'));
    }
}
