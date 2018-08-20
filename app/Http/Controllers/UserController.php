<?php

namespace App\Http\Controllers;

use App\Mail\Verification_Token;
use Illuminate\Foundation\Auth\User;
use Illuminate\Http\Request;
use Mail;

class UserController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware('verified');

    }

    public function ban($user_id){
        $user = auth()->user();
        if ($user->role!='admin')
            return response()->json('Unauthorized Action',401);
        $banneduser = User::find($user_id);
        if($banneduser==null){
            return response()->json('User Not Found',404);

        }
        $banneduser->isBanned=true;
        $banneduser->save();
        return response()->json("User Banned Successfully",200);


    }

    public function unban($user_id){
        $user = auth()->user();
        if ($user->role!='admin')
            return response()->json('Unauthorized Action',401);
        $banneduser = User::find($user_id);
        if($banneduser==null){
            return response()->json('User Not Found',404);

        }
        $banneduser->isBanned=false;
        $banneduser->save();
        return response()->json("User Banned Successfully",200);


    }

    public function delete($user_id){
        $u = User::find($user_id);
        if($u==null){
            return response()->json('User Not Found',404);

        }
        $user = auth()->user();
        if ($user->role!='admin' || $u->role=='admin')
            return response()->json('Unauthorized Action',401);

        $u->delete();
        return response()->json("User Deleted Successfully",200);

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    //show all users
    public function index()
    {
        $user = auth()->user();
        if ($user->role!='admin')
            return response()->json('Unauthorized Action',401);
        $users = User::all();
        return response()->json($user,200);
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
