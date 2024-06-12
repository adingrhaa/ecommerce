<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CheckoutHistory;
use App\Models\CheckoutInformation;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class CheckoutInformationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['index', 'search']);
    }

    public function index()
    {
        $checkoutinformations = CheckoutInformation::all();
        return response()->json(['data' => $checkoutinformations]);
    }

    public function store(Request $request)
    {
    $validator = Validator::make($request->all(), [
        'fullname' => 'required',
        'email' => 'required',
        'no_hp' => 'required',
        'provinsi' => 'required',
        'kota_kabupaten' => 'required',
        'kecamatan' => 'required',
        'kode_pos' => 'required',
        'payment_method' => 'required|in:COD,E-Wallet,Bank',
        'delivery' => 'required|in:Reguler,Cargo,Economy',
        'ringkasan_belanja' => 'required|array',
        'ringkasan_belanja.*.id_produk' => 'required|exists:products,id',
        'ringkasan_belanja.*.nama_barang' => 'required',
        'ringkasan_belanja.*.jumlah' => 'required|integer|min:1',
        'biaya_pengiriman' => 'required',
        'biaya_admin' => 'required',
        'total_harga' => 'required',
        'id_member' => 'required|exists:members,id'
    ]);

    if ($validator->fails()) {
        return response()->json($validator->errors(), 422);
    }

    $requestData = $request->all();

    $validatedRingkasanBelanja = array_map(function ($item) {
        return [
            'id_produk' => $item['id_produk'],
            'nama_barang' => $item['nama_barang'],
            'jumlah' => $item['jumlah']
        ];
    }, $request->ringkasan_belanja);

    $requestData['ringkasan_belanja'] = json_encode($validatedRingkasanBelanja);

    $checkoutInformation = CheckoutInformation::create($requestData);

    $checkoutInformation->ringkasan_belanja = json_decode($checkoutInformation->ringkasan_belanja, true);

    CheckoutHistory::create([
        'id_member' => $checkoutInformation->id_member,
        'ringkasan_belanja' => json_encode($checkoutInformation->ringkasan_belanja), // Encode to JSON
        'total_harga' => $checkoutInformation->total_harga
    ]);

    return response()->json(['data' => $checkoutInformation]);
    }

    public function show($id)
    {
        $checkoutInformation = CheckoutInformation::find($id);

        if (!$checkoutInformation) {
            return response()->json(['message' => 'Checkout information not found'], 404);
        }

        // Decode ringkasan_belanja from JSON string to array
        $checkoutInformation->ringkasan_belanja = json_decode($checkoutInformation->ringkasan_belanja, true);

        return response()->json(['data' => $checkoutInformation]);
    }

    public function update(Request $request, CheckoutInformation $checkoutinformation)
    {
        $validator = Validator::make($request->all(), [
            'fullname' => 'required',
            'email' => 'required',
            'no_hp' => 'required',
            'provinsi' => 'required',
            'kota_kabupaten' => 'required',
            'kecamatan' => 'required',
            'kode_pos' => 'required',
            'payment_method' => 'required|in:COD,E-Wallet,Bank',
            'delivery' => 'required|in:Reguler,Cargo,Economy',
            'ringkasan_belanja' => 'required|array',
            'biaya_pengiriman' => 'required',
            'biaya_admin' => 'required',
            'total_harga' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $checkoutinformation->update($request->all());

        return response()->json(['message' => 'success', 'data' => $checkoutinformation]);
    }

    public function destroy(CheckoutInformation $checkoutinformation)
    {
        File::delete('uploads/' . $checkoutinformation->gambar);

        $checkoutinformation->delete();

        return response()->json(['message' => 'success']);
    }

    public function search(Request $request)
    {
        $query = CheckoutInformation::query();

        if ($request->has('fullname')) {
            $query->where('fullname', 'like', '%' . $request->fullname . '%');
        }
        if ($request->has('email')) {
            $query->where('email', 'like', '%' . $request->email . '%');
        }
        if ($request->has('no_hp')) {
            $query->where('no_hp', 'like', '%' . $request->no_hp . '%');
        }
        if ($request->has('provinsi')) {
            $query->where('provinsi', 'like', '%' . $request->provinsi . '%');
        }
        if ($request->has('kota_kabupaten')) {
            $query->where('kota_kabupaten', 'like', '%' . $request->kota_kabupaten . '%');
        }
        if ($request->has('kecamatan')) {
            $query->where('kecamatan', 'like', '%' . $request->kecamatan . '%');
        }
        if ($request->has('kode_pos')) {
            $query->where('kode_pos', 'like', '%' . $request->kode_pos . '%');
        }
        if ($request->has('payment_method')) {
            $query->where('payment_method', 'like', '%' . $request->payment_method . '%');
        }
        if ($request->has('delivery')) {
            $query->where('delivery', 'like', '%' . $request->delivery . '%');
        }
        if ($request->has('ringkasan_belanja')) {
            $query->where('ringkasan_belanja', 'like', '%' . $request->ringkasan_belanja . '%');
        }
        if ($request->has('total_harga')) {
            $query->where('total_harga', 'like', '%' . $request->total_harga . '%');
        }

        $results = $query->get();

        return response()->json($results);
   
    }
}