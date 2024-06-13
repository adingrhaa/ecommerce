<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function __construct(){
        $this->middleware('auth:api')->except(['index', 'search', 'show']);
    }

    /**
     * Display a listing of the resource with pagination.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
{
    // Check if 'per_page' parameter is provided
    if ($request->has('per_page')) {
        // If 'per_page' parameter is provided, paginate the results
        $perPage = $request->input('per_page');
        $products = Product::paginate($perPage);
    } else {
        // If 'per_page' parameter is not provided, get all products
        $products = Product::all();
    }

    return response()->json(['data' => $products]);
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
            'id_kategori' => 'required',
            'nama_barang' => 'required',
            'gambar' => 'required|image|mimes:jpg,png,webp',
            'deskripsi' => 'required',
            'harga' => 'required|numeric|min:0',
            'bahan' => 'required',
            'tags' => 'required',
        ]);

        if ($validator->fails()){
            return response()->json($validator->errors(), 422);
        }

        $input = $request->all();

        if ($request->has('gambar')){
            $gambar = $request->file('gambar');
            $nama_gambar = time() . rand(1,9) . '.' . $gambar->getClientOriginalExtension();
            $path = $gambar->storeAs('public/images', $nama_gambar);
            $input['gambar'] = $nama_gambar;

            $url_gambar = asset('storage/images/' . $nama_gambar);
            $input['url_gambar'] = $url_gambar;
        }

        $product = Product::create($input);

        return response()->json(['data' => $product]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        return response()->json(['data' => $product]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        $validator = Validator::make($request->all(), [
            'id_kategori' => 'required',
            'nama_barang' => 'required',
            'deskripsi' => 'required',
            'harga' => 'required|numeric|min:0',
            'bahan' => 'required',
            'tags' => 'required',
        ]);

        if ($validator->fails()){
            return response()->json($validator->errors(), 422);
        }

        $input = $request->all();

        if ($request->hasFile('gambar')) {
            // Menghapus gambar yang sudah ada
            if ($product->gambar) {
                File::delete(public_path('storage/images/' . $product->gambar));
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

        // Memperbarui data kategori
        $product->update($input);

        return response()->json(['message' => 'success', 'data' => $product]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        if ($product->gambar) {
            // Hapus gambar terkait jika ada
            File::delete(public_path('storage/images/' . $product->gambar));
        }

        // Hapus data kategori dari database
        $product->delete();

        return response()->json(['message' => 'success']);
    }

    public function search(Request $request)
    {
        $query = Product::query();

        if ($request->has('id_kategori')) {
            $query->where('id_kategori', $request->id_kategori);
        }

        if ($request->has('nama_barang')) {
            $query->where('nama_barang', 'like', '%' . $request->nama_barang . '%');
        }

        $results = $query->get(); 

        return response()->json($results);
    }
}
