<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CheckoutHistory;
use Illuminate\Support\Facades\Validator;

class CheckoutHistoryController extends Controller
{
    public function index(Request $request)
{
    // Mengambil 'id_member' dan 'status' dari parameter query
    $id_member = $request->query('id_member');
    $status = $request->query('status');

    // Deklarasi variabel untuk menyimpan hasil query
    $checkoutHistories = null;

    // Jika kedua parameter ada, cari berdasarkan keduanya
    if ($id_member && $status) {
        $checkoutHistories = CheckoutHistory::where('id_member', $id_member)
                                            ->where('status', $status)
                                            ->get();
    } elseif ($id_member) {
        // Jika hanya 'id_member' disediakan, cari berdasarkan id_member
        $checkoutHistories = CheckoutHistory::where('id_member', $id_member)->get();
    } else {
        // Jika tidak ada parameter query, tampilkan semua data
        $checkoutHistories = CheckoutHistory::all();
    }

    // Kembalikan hasil sebagai JSON
    return response()->json([
        'data' => $checkoutHistories
    ]);
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
