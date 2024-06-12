<?php
 
namespace App\Http\Controllers;
 
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\CheckoutInformation;
use Psy\CodeCleaner\ReturnTypePass;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
 
class MemberController extends Controller
{
 
    public function __construct(){
        $this->middleware('auth:api')->except(['index','show','update']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
 
    {
        $members = Member::all();
 
        return response()->json([
            'data' => $members
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
            'fullname' => 'required',
            'country' => 'required',
            'city' => 'required',
            'gender' => 'required|in:male,female',
            'detail_alamat' => 'required',
            'no_hp' => 'required',
            'email' => 'required|email',
            'password' => 'required|same:konfirmasi_password',
            'konfirmasi_password' => 'required|same:password',
        ]);
    
        if ($validator->fails()){
            return response()->json(
                $validator->errors(),
                422
            );
        };
    
        $input = $request->all();
        $input['password'] = bcrypt($request->password);
        unset($input['konfirmasi_password']);
        $member = Member::create($input);
    
        return response()->json([
            'data' => $member
        ]);
       
    }
 
    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Member  $member
     * @return \Illuminate\Http\Response
     */
    public function show(Member $member)
    {
        return response()->json([
            'data' => $member 
        ]);
    }
 
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Member  $member
     * @return \Illuminate\Http\Response
     */
    public function edit(Member $member)
    {
        //
    }
 
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Member  $member
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Member $member)
    {
        $validator = Validator::make($request->all(), [
            'fullname' => 'required',
            'country' => 'required',
            'city' => 'required',
            'gender' => 'required|in:male,female',
            'detail_alamat' => 'required',
            'no_hp' => 'required',
            'email' => 'email',
            'password' => 'same:konfirmasi_password',
            'konfirmasi_password' => 'same:password',
        ]);
   
        if ($validator->fails()){
            return response()->json(
                $validator->errors(),
                422
            );
        }
   
        $input = $request->all();
        $input['password'] = bcrypt($request->password);
        unset($input['konfirmasi_password']);
   
        // Memperbarui data member
        $member->update($input);
   
        return response()->json([
            'message' => 'success',
            'data' => $member
        ]);
    }
   
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Member  $member
     * @return \Illuminate\Http\Response
     */
    public function destroy(Member $member)
{
 
    // Hapus data member dari database
    $member->delete();
 
    return response()->json([
        'message' => 'success'
    ]);
}

    public function blockMember($id)
    {
        $member = Member::find($id);
        if (!$member) {
            return response()->json(['message' => 'Member not found.'], 404);
        }

        $member->blocked_until = Carbon::now()->addSeconds(30); // Contoh: diblokir selama 7 hari
        $member->save();

        return response()->json(['message' => 'Member has been blocked successfully.']);
    }

    public function unblockMember($id)
    {
        $member = Member::find($id);
        if (!$member) {
            return response()->json(['message' => 'Member not found.'], 404);
        }

        $member->blocked_until = null; // Hapus status blokir
        $member->save();

        return response()->json(['message' => 'Member has been unblocked successfully.']);
    }

}