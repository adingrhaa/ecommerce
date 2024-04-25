<?php
 
namespace App\Http\Controllers;
 
use App\Models\Product; // a. Menggunakan model Product
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Psy\CodeCleaner\ReturnTypePass;
 
class ProductController extends Controller // a. Menggunakan ProductController
{
 
    public function __construct(){
        $this->middleware('auth:api')->except(['index']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = Product::all(); // a. Menggunakan Product
        return response()->json([
            'data' => $products // a. Menggunakan products
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
            $gambar->move('uploads', $nama_gambar);
            $input['gambar'] = $nama_gambar;
        } 
       
 
        $product = Product::create($input); // a. Menggunakan Product
 
        return response()->json([
            'data' => $product // a. Menggunakan product
        ]);
       
    }
 
    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product // c. Menggunakan Product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product) // c. Menggunakan Product
    {
        return response()->json([
            'data' => $product 
        ]);
    }
 
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Product  $product // c. Menggunakan Product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product) // c. Menggunakan Product
    {
        //
    }
 
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product // c. Menggunakan Product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product) // c. Menggunakan Product
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
        }
   
        $input = $request->all();
   
        if ($request->has('gambar')) {
            File::delete('uploads/' . $product->gambar);
            // Mengunggah gambar yang baru
            $gambar = $request->file('gambar');
            $nama_gambar = time() . rand(1,9) . '.' . $gambar->getClientOriginalExtension();
            $gambar->move('uploads', $nama_gambar);
            $input['gambar'] = $nama_gambar;
        } else {
            // Jika tidak ada gambar baru, hapus informasi gambar dari input
            unset($input['gambar']);
        }
   
        // Memperbarui data product
        $product->update($input); // c. Menggunakan Product
   
        return response()->json([
            'message' => 'success',
            'data' => $product // c. Menggunakan product
        ]);
    }
   
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product // c. Menggunakan Product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product) // c. Menggunakan Product
{
    File::delete('uploads/' . $product->gambar);
 
    // Hapus data product dari database
    $product->delete(); // c. Menggunakan Product
 
    return response()->json([
        'message' => 'success'
    ]);
}
 
}
