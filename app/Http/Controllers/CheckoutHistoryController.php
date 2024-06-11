<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CheckoutHistory;
use Illuminate\Support\Facades\Validator;

class CheckoutHistoryController extends Controller
{
    public function index()
    {
        $checkoutHistories = CheckoutHistory::all();
        return response()->json(['data' => $checkoutHistories]);
    }

    public function show($id)
    {
        $checkoutHistory = CheckoutHistory::find($id);
        if (!$checkoutHistory) {
            return response()->json(['message' => 'Checkout history not found'], 404);
        }
        return response()->json(['data' => $checkoutHistory]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'member_id' => 'required|exists:members,id',
            'checkout_information_id' => 'required|exists:checkout_informations,id',
            'ringkasan_belanja' => 'required',
            'total_harga' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $checkoutHistory = CheckoutHistory::create($request->all());

        return response()->json(['data' => $checkoutHistory], 201);
    }

    public function update(Request $request, $id)
    {
        $checkoutHistory = CheckoutHistory::find($id);
        if (!$checkoutHistory) {
            return response()->json(['message' => 'Checkout history not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'member_id' => 'required|exists:members,id',
            'checkout_information_id' => 'required|exists:checkout_informations,id',
            'ringkasan_belanja' => 'required',
            'total_harga' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $checkoutHistory->update($request->all());

        return response()->json(['data' => $checkoutHistory]);
    }

    public function destroy($id)
    {
        $checkoutHistory = CheckoutHistory::find($id);
        if (!$checkoutHistory) {
            return response()->json(['message' => 'Checkout history not found'], 404);
        }

        $checkoutHistory->delete();

        return response()->json(['message' => 'Checkout history deleted successfully']);
    }
}

