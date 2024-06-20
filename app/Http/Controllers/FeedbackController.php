<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;


class FeedbackController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->middleware('auth:sanctum')->except(['index']);
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
            'id_member' => 'required|exists:members,id',
            'id_product' => 'required|array',  // Menggunakan array karena id_product adalah array
            'id_product.*' => 'exists:products,id',  // Memvalidasi setiap elemen dalam array
            'rating' => 'required|integer|min:0|max:100',  // Validasi rating 0-100
            'comment' => 'nullable|string',  // Opsional untuk komentar
            'gambar' => 'nullable|file',    // Opsional untuk gambar
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
        }

        $feedback = Feedback::create($input);

        return response()->json(['data' => $feedback]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Feedback  $feedback
     * @return \Illuminate\Http\Response
     */
    public function show(Feedback $feedback)
    {
        $feedback = Feedback::all();

        return response()->json([
            'data' => $feedback 
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Feedback  $feedback
     * @return \Illuminate\Http\Response
     */
    public function edit(Feedback $feedback)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Feedback  $feedback
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Feedback $feedback)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Feedback  $feedback
     * @return \Illuminate\Http\Response
     */
    public function destroy(Feedback $feedback)
    {
        //
    }

    public function getByProductId(Request $request, $productId)
    {
        // Convert productId to string
        $productId = (string) $productId;
        
        Log::info("Searching for productId: '{$productId}'");

        $feedbacks = Feedback::whereRaw("JSON_CONTAINS(id_product, '[\"{$productId}\"]')")->get();
        
        Log::info("Found feedbacks: ", $feedbacks->toArray());

        return response()->json([
            'data' => $feedbacks 
        ]);
    }
}
