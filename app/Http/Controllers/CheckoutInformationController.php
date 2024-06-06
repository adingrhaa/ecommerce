<?php

namespace App\Http\Controllers;

use App\Models\CheckoutInformation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Psy\CodeCleaner\ReturnTypePass;

class CheckoutInformationController extends Controller
{

    public function __construct(){
        $this->middleware('auth:sanctum')->except(['index', 'search',]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()

    {
        $checkoutinformations = CheckoutInformation::all();

        return response()->json([
            'data' => $checkoutinformations
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
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
            'ringkasan_belanja' => 'required',
            'biaya_pengiriman' => 'required',
            'biaya_admin' => 'required',
            'total_harga' => 'required',
        ]);

        if ($validator->fails()){
            return response()->json(
                $validator->errors(),
                422
            );
        };

        $input = $request->all();

        $checkoutinformation = CheckoutInformation::create($input);

        return response()->json([
            'data' => $checkoutinformation
        ]);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\CheckoutInformation  $checkoutinformation
     * @return \Illuminate\Http\Response
     */
    public function show(CheckoutInformation $checkoutinformation)
    {
        return response()->json([
            'data' => $checkoutinformation
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\CheckoutInformation  $checkoutinformation
     * @return \Illuminate\Http\Response
     */
    public function edit(CheckoutInformation $checkoutinformation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\CheckoutInformation  $checkoutinformation
     * @return \Illuminate\Http\Response
     */
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
            'ringkasan_belanja' => 'required',
            'biaya_pengiriman' => 'required',
            'biaya_admin' => 'required',
            'total_harga' => 'required',           
        ]);

        if ($validator->fails()){
            return response()->json(
                $validator->errors(),
                422
            );
        }

        $input = $request->all();

        // Memperbarui data checkout information
        $checkoutinformation->update($input);

        return response()->json([
            'message' => 'success',
            'data' => $checkoutinformation
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CheckoutInformation  $checkoutinformation
     * @return \Illuminate\Http\Response
     */
    public function destroy(CheckoutInformation $checkoutinformation)
{
    File::delete('uploads/' . $checkoutinformation->gambar);

    // Hapus data checkout information dari database
    $checkoutinformation->delete();

    return response()->json([
        'message' => 'success'
    ]);
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
