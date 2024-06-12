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

    $checkoutHistory->ringkasan_belanja = json_encode($checkoutHistory->ringkasan_belanja, true);

    return response()->json(['data' => $checkoutHistory]);
}

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_member' => 'required|exists:members,id',
            'ringkasan_belanja' => 'required|array', // Ensure ringkasan_belanja is an array
            'total_harga' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Ensure ringkasan_belanja is stored as JSON
        $requestData = $request->all();
        $requestData['ringkasan_belanja'] = json_encode($request->input('ringkasan_belanja'));

        $checkoutHistory = CheckoutHistory::create($requestData);

        return response()->json(['data' => $checkoutHistory], 201);
    }

    public function update(Request $request, $id)
    {
        $checkoutHistory = CheckoutHistory::find($id);
        if (!$checkoutHistory) {
            return response()->json(['message' => 'Checkout history not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'id_member' => 'required|exists:members,id',
            'ringkasan_belanja' => 'required|array',
            'total_harga' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Ensure ringkasan_belanja is stored as JSON
        $requestData = $request->all();
        $requestData['ringkasan_belanja'] = json_encode($request->input('ringkasan_belanja'));

        $checkoutHistory->update($requestData);

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
