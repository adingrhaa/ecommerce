<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{

    public function __construct(){
        $this->middleware('auth:sanctum')->except(['index']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()

    {
        $carts = Cart::all();

        return response()->json([
            'data' => $carts
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
            'id_produk' => 'required',
            'id_member' => 'required',
            'nama_barang' => 'required',
            'harga' => 'required'
        ]);

        if ($validator->fails()){
            return response()->json(
                $validator->errors(),
                422
            );
        };

        $input = $request->all();

        if ($request->has('gambar')){
            $gambar = $request->file('gambar');
            $nama_gambar = time() . rand(1,9) . '.' . optional($gambar)->getClientOriginalExtension();
            $path = $gambar->storeAs('public/images', $nama_gambar);
            $input['gambar'] = $nama_gambar;
   
            $url_gambar = asset('storage/images/' . $nama_gambar);
            $input['url_gambar'] = $url_gambar;
        }
       
        $cart = Cart::create($input);

        return response()->json([
            'data' => $cart
        ]);
       
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Cart  $cart
     * @return \Illuminate\Http\Response
     */
    public function show(Cart $cart)
    {
        return response()->json([
            'data' => $cart 
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Cart  $cart
     * @return \Illuminate\Http\Response
     */
    public function edit(Cart $cart)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Cart  $cart
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Cart $cart)
    {
        $validator = Validator::make($request->all(), [
            'id_produk' => 'required',
            'id_member' => 'required',
            'nama_barang' => 'required',
            'harga' => 'required'
                       
        ]);
   
        if ($validator->fails()){
            return response()->json(
                $validator->errors(),
                422
            );
        }
   
        $input = $request->all();

        if ($request->hasFile('gambar')) {
            // Menghapus gambar yang sudah ada
            if ($cart->gambar) {
                File::delete(public_path('storage/images/' . $cart->gambar));
            }
   
            // Mengunggah gambar yang baru
            $gambar = $request->file('gambar');
            $nama_gambar = time() . rand(1,9) . '.' . $gambar->getClientOriginalExtension();
            $path = $gambar->storeAs('public/images', $nama_gambar);
            $input['gambar'] = $nama_gambar;
        } else {
            // Jika tidak ada gambar baru, hapus informasi gambar dari input
            unset($input['gambar']);
        }
   
        // Memperbarui data keranjang
        $cart->update($input);
   
        return response()->json([
            'message' => 'success',
            'data' => $cart
        ]);
    }
   
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Cart  $cart
     * @return \Illuminate\Http\Response
     */
    public function destroy(Cart $cart)
    {
        if ($cart->gambar) {
            // Hapus gambar terkait jika ada
            File::delete(public_path('storage/images/' . $cart->gambar));
        }

        File::delete('uploads/' . $cart->gambar);
 
        // Hapus data keranjang dari database
        $cart->delete();
 
        return response()->json([
            'message' => 'success'
        ]);
    }

}

