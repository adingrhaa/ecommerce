<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CheckoutHistory;
use App\Models\CheckoutInformation;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class CheckoutInformationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['index', 'search','update']);
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
            'biaya_pengiriman' => 'required',
            'biaya_admin' => 'required',
            'total_harga' => 'required',
            'status' => 'in:dibuat,dikonfirmasi,dikirim,diterima,selesai'
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
    
        // Simpan data ke CheckoutHistory
        CheckoutHistory::create([
            'id_member' => $checkoutInformation->id_member,
            'ringkasan_belanja' => $validatedRingkasanBelanja, // Simpan langsung sebagai array
            'total_harga' => $checkoutInformation->total_harga,
            'status' => $checkoutInformation->status
        ]);
    
        // Decode ringkasan_belanja dari string JSON ke array
        $ringkasanBelanjaArray = json_decode($checkoutInformation->ringkasan_belanja, true);
    
        if (is_null($ringkasanBelanjaArray)) {
            return response()->json(['message' => 'Error decoding ringkasan_belanja field'], 500);
        }
    
        // Update properti ringkasan_belanja dengan array
        $checkoutInformation->ringkasan_belanja = $ringkasanBelanjaArray;
    
        // Respon
        return response()->json(['data' => $checkoutInformation]);
    }
    
    public function show($id)
{
    $checkoutinformation = CheckoutInformation::find($id);
    
    if (!$checkoutinformation) {
        return response()->json(['message' => 'Checkout history not found'], 404);
    }

    $checkoutinformation->ringkasan_belanja = json_encode($checkoutinformation->ringkasan_belanja, true);

    return response()->json(['data' => $checkoutinformation]);
}

public function update(Request $request, $id)
{
    // Temukan data CheckoutInformation yang akan diperbarui
    $checkoutInformation = CheckoutInformation::findOrFail($id);

    // Validasi data yang diterima
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
        'total_harga' => 'required',
        'status' => 'in:dibuat,dikonfirmasi,dikirim,diterima,selesai'
    ]);

    // Jika validasi gagal, kembalikan respon kesalahan
    if ($validator->fails()) {
        return response()->json($validator->errors(), 422);
    }

    // Ambil data dari request
    $requestData = $request->all();

    // Validasi dan ubah ringkasan belanja ke format yang benar
    $validatedRingkasanBelanja = array_map(function ($item) {
        return [
            'id_produk' => $item['id_produk'],
            'nama_barang' => $item['nama_barang'],
            'jumlah' => $item['jumlah']
        ];
    }, $request->ringkasan_belanja);

    // Ubah ringkasan belanja menjadi format JSON
    $requestData['ringkasan_belanja'] = json_encode($validatedRingkasanBelanja);

    // Update data CheckoutInformation
    $checkoutInformation->update($requestData);

    // Update entri CheckoutHistory yang terkait
    $checkoutHistory = CheckoutHistory::where('id_member', $checkoutInformation->id_member)
                                      ->orderBy('created_at', 'desc')
                                      ->first();
    if ($checkoutHistory) {
        $checkoutHistory->update([
            'ringkasan_belanja' => $validatedRingkasanBelanja,
            'total_harga' => $checkoutInformation->total_harga,
            'status' => $checkoutInformation->status
        ]);
    }

    // Decode ringkasan_belanja dari string JSON ke array
    $ringkasanBelanjaArray = json_decode($checkoutInformation->ringkasan_belanja, true);

    // Jika terjadi kesalahan pada decoding, kembalikan pesan kesalahan
    if (is_null($ringkasanBelanjaArray)) {
        return response()->json(['message' => 'Error decoding ringkasan_belanja field'], 500);
    }

    // Update properti ringkasan_belanja dengan array
    $checkoutInformation->ringkasan_belanja = $ringkasanBelanjaArray;

    // Kembalikan respon
    return response()->json(['data' => $checkoutInformation]);
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