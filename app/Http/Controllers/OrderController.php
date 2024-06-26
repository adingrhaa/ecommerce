<?php
 
namespace App\Http\Controllers;
 
use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Psy\CodeCleaner\ReturnTypePass;
 
class OrderController extends Controller
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
        $orders = Order::all();
 
        return response()->json([
            'data' => $orders
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
            'id_member' => 'required'
        ]);
 
        if ($validator->fails()){
            return response()->json(
                $validator->errors(),
                422
            );
        };
 
        $input = $request->all();
        $order = Order::create($input);

        for ($i=0; $i < count($input['id_produk']); $i++) { 
            OrderDetail::create([
                'id_order' => $order['id'],
                'id_produk' => $input['id_produk'][$i],
                'jumlah' => $input['jumlah'][$i],
                'size' => $input['size'][$i],
                'color' => $input['color'][$i],
                'total' => $input['total'][$i],
            ]);
        } 

        return response()->json([
            'data' => $order
        ]);
       
    }
 
    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function show(Order $order)
    {
        return response()->json([
            'data' => $order
        ]);
    }
 
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function edit(Order $order)
    {
        //
    }
 
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Order $order)
    {
        $validator = Validator::make($request->all(), [
            'id_member' => 'required',
           
        ]);
   
        if ($validator->fails()){
            return response()->json(
                $validator->errors(),
                422
            );
        }
   
        $input = $request->all();
        $order->update($input);

        OrderDetail::where('id_order', $order['id'])-> delete();
   
        for ($i=0; $i < count($input['id_produk']); $i++) { 
            OrderDetail::create([
                'id_order' => $order['id'],
                'id_produk' => $input['id_produk'][$i],
                'jumlah' => $input['jumlah'][$i],
                'size' => $input['size'][$i],
                'color' => $input['color'][$i],
                'total' => $input['total'][$i],
            ]);
        }
   
        // Memperbarui data order
        $order->update($input);
   
        return response()->json([
            'message' => 'success',
            'data' => $order
        ]);
    }

    public function ubah_status(Request $request, Order $order){
        $order->update([
            'status' => $request->status
        ]);

        return response()->json([
            'message' => 'success',
            'data' => $order
        ]);
    }

    public function dikonfirmasi(){
        $orders = Order::where('status', 'Dikonfirmasi')->get();
 
        return response()->json([
            'data' => $orders
        ]);
    }

    public function dikemas(){
        $orders = Order::where('status', 'Dikemas')->get();
 
        return response()->json([
            'data' => $orders
        ]);
    }

    public function dikirim(){
        $orders = Order::where('status', 'Dikirim')->get();
 
        return response()->json([
            'data' => $orders
        ]);
    }

    public function diterima(){
        $orders = Order::where('status', 'Diterima')->get();
 
        return response()->json([
            'data' => $orders
        ]);
    }

    public function selesai(){
        $orders = Order::where('status', 'Selesai')->get();
 
        return response()->json([
            'data' => $orders
        ]);
    }
   
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function destroy(Order $order)
{
 
    // Hapus data order dari database
    $order->delete();
 
    return response()->json([
        'message' => 'success'
    ]);
}
 
}
