<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CheckoutHistory;
use App\Models\CheckoutInformation;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class CheckoutInformationController extends Controller
{
    public function __construct(){
        $this->middleware('auth:sanctum')->except(['index', 'search',]);
    }

    public function index()
    {
        $checkoutinformations = CheckoutInformation::all();
        return response()->json(['data' => $checkoutinformations]);
    }

    public function store(Request $request)
    {
        // Validasi input
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
            'id_member' => 'required|exists:members,id'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Simpan data checkout information
        $checkoutInformation = CheckoutInformation::create($request->all());

        // Simpan riwayat checkout ke dalam tabel checkout_histories
        CheckoutHistory::create([
            'id_member' => $checkoutInformation->id_member,
            'ringkasan_belanja' => json_encode($checkoutInformation->ringkasan_belanja), // Encode to JSON
            'total_harga' => $checkoutInformation->total_harga
        ]);

        return response()->json(['data' => $checkoutInformation]);
    }

    public function show(CheckoutInformation $checkoutinformation)
    {
        return response()->json(['data' => $checkoutinformation]);
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