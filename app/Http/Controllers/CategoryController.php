<?php
 
namespace App\Http\Controllers;
 
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Psy\CodeCleaner\ReturnTypePass;
 
class CategoryController extends Controller
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
        $categories = Category::all();
 
        return response()->json([
            'data' => $categories
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
            'nama_kategori' => 'required',
        ]);
 
        if ($validator->fails()){
            return response()->json(
                $validator->errors(),
                422
            );
        };
 
        $input = $request->all();
       
        $category = Category::create($input);
 
        return response()->json([
            'data' => $category
        ]);
       
    }
 
    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category)
    {
        return response()->json([
            'data' => $category 
        ]);
    }
 
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function edit(Category $category)
    {
        //
    }
 
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Category $category)
    {
        $validator = Validator::make($request->all(), [
            'nama_kategori' => 'required',           
        ]);
   
        if ($validator->fails()){
            return response()->json(
                $validator->errors(),
                422
            );
        }
   
        $input = $request->all();
   
        // Memperbarui data kategori
        $category->update($input);
   
        return response()->json([
            'message' => 'success',
            'data' => $category
        ]);
    }
   
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category)
{
    File::delete('uploads/' . $category->gambar);
 
    // Hapus data kategori dari database
    $category->delete();
 
    return response()->json([
        'message' => 'success'
    ]);
}
 
}