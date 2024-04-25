<?php
 
namespace App\Http\Controllers;
 
use App\Models\Subcategory; // (a) Mengubah 'Category' menjadi 'Subcategory'
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Psy\CodeCleaner\ReturnTypePass;
 
class SubcategoryController extends Controller // (a) Mengubah 'CategoryController' menjadi 'SubcategoryController'
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
        $subcategories = Subcategory::all(); // (a) Mengubah 'Category' menjadi 'Subcategory'
 
        return response()->json([
            'data' => $subcategories // (a) Mengubah 'categories' menjadi 'subcategories'
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
            'nama_subkategori' => 'required',
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
       
 
        $subcategory = Subcategory::create($input); // (a) Mengubah 'Category' menjadi 'Subcategory'
 
        return response()->json([
            'data' => $subcategory // (a) Mengubah 'category' menjadi 'subcategory'
        ]);
       
    }
 
    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Category  $subcategory // (a) Mengubah 'Category' menjadi 'Subcategory'
     * @return \Illuminate\Http\Response
     */
    public function show(Subcategory $subcategory) // (a) Mengubah 'Category' menjadi 'Subcategory'
    {
        return response()->json([
            'data' => $subcategory 
        ]);
    }
 
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Category  $subcategory // (a) Mengubah 'Category' menjadi 'Subcategory'
     * @return \Illuminate\Http\Response
     */
    public function edit(Subcategory $subcategory) // (a) Mengubah 'Category' menjadi 'Subcategory'
    {
        //
    }
 
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Category  $subcategory // (a) Mengubah 'Category' menjadi 'Subcategory'
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Subcategory $subcategory) // (a) Mengubah 'Category' menjadi 'Subcategory'
    {
        $validator = Validator::make($request->all(), [
            'id_kategori' => 'required',
            'nama_subkategori' => 'required',
            'deskripsi' => 'required',
           
        ]);
   
        if ($validator->fails()){
            return response()->json(
                $validator->errors(),
                422
            );
        }
   
        $input = $request->all();
   
        if ($request->has('gambar')) {
            File::delete('uploads/' . $subcategory->gambar); // (a) Mengubah 'Category' menjadi 'Subcategory'
            // Mengunggah gambar yang baru
            $gambar = $request->file('gambar');
            $nama_gambar = time() . rand(1,9) . '.' . $gambar->getClientOriginalExtension();
            $gambar->move('uploads', $nama_gambar);
            $input['gambar'] = $nama_gambar;
        } else {
            // Jika tidak ada gambar baru, hapus informasi gambar dari input
            unset($input['gambar']);
        }
   
        // Memperbarui data kategori
        $subcategory->update($input); // (a) Mengubah 'Category' menjadi 'Subcategory'
   
        return response()->json([
            'message' => 'success',
            'data' => $subcategory // (a) Mengubah 'category' menjadi 'subcategory'
        ]);
    }
   
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Category  $subcategory // (a) Mengubah 'Category' menjadi 'Subcategory'
     * @return \Illuminate\Http\Response
     */
    public function destroy(Subcategory $subcategory) // (a) Mengubah 'Category' menjadi 'Subcategory'
{
    File::delete('uploads/' . $subcategory->gambar); // (a) Mengubah 'Category' menjadi 'Subcategory'
 
    // Hapus data kategori dari database
    $subcategory->delete(); // (a) Mengubah 'Category' menjadi 'Subcategory'
 
    return response()->json([
        'message' => 'success'
    ]);
}
 
}
