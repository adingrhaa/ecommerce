<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{

    public function __construct(){
        $this->middleware('auth:sanctum')->except(['index','show','destroy']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)

    {
        $id_member = $request->query('id_member');
        $id_produk = $request->query('id_produk');

        if ($id_member && $id_produk) {
            // Jika kedua parameter ada, cari berdasarkan keduanya
            $carts = Cart::where('id_member', $id_member)
                        ->where('id_produk', $id_produk)
                        ->get();
        } elseif ($id_member) {
            // Jika hanya id_member ada, cari berdasarkan id_member
            $carts = Cart::where('id_member', $id_member)->get();
        } elseif (!$id_member && !$id_produk) {
            // Jika tidak ada parameter, tampilkan semua data
            $carts = Cart::all();
        } else {
            // Jika hanya salah satu parameter yang ada, kembalikan error
            return response()->json(['error' => 'Both id_member and id_produk are required'], 400);
        }

        return response()->json($carts);

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

        if ($request->hasFile('gambar')){
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
    public function destroy(Request $request, $id = null)
{
    if ($id) {
        // Hapus berdasarkan id
        $cart = Cart::find($id);

        if ($cart) {
            if ($cart->gambar) {
                File::delete(public_path('storage/images/' . $cart->gambar));
            }

            $cart->delete();

            return response()->json(['message' => 'Cart deleted successfully'], 200);
        } else {
            return response()->json(['error' => 'Cart not found'], 404);
        }
    } else {
        $id_member = $request->query('id_member');
        $id_produk = $request->query('id_produk');

        if ($id_member && $id_produk) {
            // Cari cart berdasarkan id_member dan id_produk
            $cart = Cart::where('id_member', $id_member)
                        ->where('id_produk', $id_produk)
                        ->first();

            if ($cart) {
                if ($cart->gambar) {
                    File::delete(public_path('storage/images/' . $cart->gambar));
                }

                $cart->delete();
                return response()->json(['message' => 'Cart deleted successfully'], 200);
            } else {
                return response()->json(['error' => 'Cart not found'], 404);
            }
        } else {
            // Jika tidak ada parameter atau parameter tidak lengkap, kembalikan pesan error
            return response()->json(['error' => 'Either id, or both id_member and id_produk are required'], 400);
        }
    }
}


}

