<?php

namespace App\Http\Controllers;

use App\Models\Slider; // (a) Menggunakan model Slider
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Psy\CodeCleaner\ReturnTypePass;

class SliderController extends Controller // (a) Menggunakan SliderController
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
        $sliders = Slider::all(); // (a) Menggunakan Slider
        return response()->json([
            'data' => $sliders // (a) Menggunakan sliders
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
            'nama_slider' => 'required',
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


        $slider = Slider::create($input); // (a) Menggunakan Slider

        return response()->json([
            'data' => $slider // (a) Menggunakan slider
        ]);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Slider  $slider // (a) Menggunakan Slider
     * @return \Illuminate\Http\Response
     */
    public function show(Slider $slider) // (a) Menggunakan Slider
    {
        return response()->json([
            'data' => $slider
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Slider  $slider // (a) Menggunakan Slider
     * @return \Illuminate\Http\Response
     */
    public function edit(Slider $slider) // (a) Menggunakan Slider
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Slider  $slider // (a) Menggunakan Slider
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Slider $slider) // (a) Menggunakan Slider
    {
        $validator = Validator::make($request->all(), [
            'nama_slider' => 'required',
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
            File::delete('uploads/' . $slider->gambar);
            // Mengunggah gambar yang baru
            $gambar = $request->file('gambar');
            $nama_gambar = time() . rand(1,9) . '.' . $gambar->getClientOriginalExtension();
            $gambar->move('uploads', $nama_gambar);
            $input['gambar'] = $nama_gambar;
        } else {
            // Jika tidak ada gambar baru, hapus informasi gambar dari input
            unset($input['gambar']);
        }

        // Memperbarui data slider
        $slider->update($input);

        return response()->json([
            'message' => 'success',
            'data' => $slider
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Slider  $slider // (a) Menggunakan Slider
     * @return \Illuminate\Http\Response
     */
    public function destroy(Slider $slider) // (a) Menggunakan Slider
    {
        File::delete('uploads/' . $slider->gambar);

        // Hapus data slider dari database
        $slider->delete();

        return response()->json([
            'message' => 'success'
        ]);
    }

}
