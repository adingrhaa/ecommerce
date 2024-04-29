<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage; 

class ProductController extends Controller
{

    public function __construct(){
        $this->middleware('auth:api')->except(['index']);
    }

    public function index()
    {
        $products = Product::all();
        return response()->json([
            'data' => $products
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_kategori' => 'required',
            'id_subkategori' => 'required',
            'nama_barang' => 'required',
            'harga' => 'required',
            'diskon' => 'required',
            'bahan' => 'required',
            'tags' => 'required',
            'sku' => 'required',
            'ukuran' => 'required',
            'warna' => 'required',
            'deskripsi' => 'required',
            'gambar' => 'required|image|mimes:jpg,png,webp'
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
            $nama_gambar = time() . rand(1,9) . '.' . $gambar->getClientOriginalExtension();
            $gambar->storeAs('public/uploads', $nama_gambar); // Store the image in 'public/uploads' directory
            $input['gambar'] = $nama_gambar;
        }

        $product = Product::create($input);

        return response()->json([
            'data' => $product
        ]);
    }

    public function show(Product $product)
    {
        // Generate public URL for image
        $product->gambar = asset('storage/uploads/' . $product->gambar);
        
        return response()->json([
            'data' => $product 
        ]);
    }

}
