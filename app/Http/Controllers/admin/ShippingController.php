<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Shipping;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ShippingController extends Controller
{
    public function create()
    {
        $countries = DB::table('countries')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))->from('shipping_charges')->whereColumn('shipping_charges.country_id', 'countries.id');
            })
            ->orderBy('name')->get();

        $shippingCharges = Shipping::leftJoin('countries', 'countries.id', 'shipping_charges.country_id')->select('shipping_charges.*', 'countries.name')->get();

        return view('admin.shipping.create', compact('countries', 'shippingCharges'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'country' => [
                'required',
                Rule::unique('shipping_charges', 'country_id'),
                Rule::when(
                    $request->country !== 'rest_of_the_world',
                    Rule::exists('countries', 'id')
                ),
            ],
            'amount' => 'required|numeric|min:0'
        ]);

        if ($validator->passes()) {

            $shipping = new Shipping;
            $shipping->country_id = $request->country;
            $shipping->amount = $request->amount;
            $shipping->save();

            session()->flash('success', 'Shipping added Successfully');

            return response()->json(['status' => true]);
        } else {
            return response()->json(['status' => false, 'errors' => $validator->errors()]);
        }
    }

    public function edit(int $id)
    {
        $shippingCharge = Shipping::find($id);
        $countries = DB::table('countries')
            ->whereNotExists(function ($query) use ($id) {
                $query->select(DB::raw(1))
                    ->from('shipping_charges')
                    ->whereColumn('shipping_charges.country_id', 'countries.id')
                    ->where('shipping_charges.id', '!=', $id);
            })
            ->orderBy('name')->get();

        return view('admin.shipping.edit', compact('countries', 'shippingCharge'));
    }

    public function update(int $id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'country' => [
                'required',
                Rule::unique('shipping_charges', 'country_id'),
                Rule::when(
                    $request->country !== 'rest_of_the_world',
                    Rule::exists('countries', 'id')
                ),
            ],
            'amount' => 'required|numeric|min:0'
        ]);

        if ($validator->passes()) {

            $shipping = Shipping::find($id);
            $shipping->country_id = $request->country;
            $shipping->amount = $request->amount;
            $shipping->save();

            session()->flash('success', 'Shipping updated Successfully');

            return response()->json(['status' => true]);
        } else {
            return response()->json(['status' => false, 'errors' => $validator->errors()]);
        }
    }

    public function destroy(int $id)
    {
        $shippingCharge = Shipping::find($id);

        if (!$shippingCharge) {
            session()->flash('success', 'Shipping charge not found.');
            return response()->json([
                'status' => false,
                'message' => 'Shipping charge not found.'
            ]);
        }

        $shippingCharge->delete();

        session()->flash('success', 'Shipping charge deleted successfully.');

        return response()->json([
            'status' => true,
            'message' => 'Shipping charge deleted successfully.'
        ]);
    }
}
