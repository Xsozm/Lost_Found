<?php

namespace App\Http\Controllers;
use App\Verify_Token;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Mail;
use Illuminate\Http\Request;
use App\User;
use App\Mail\Verification_Token;
use Validator;
class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login','register','activate']]);
        $this->middleware('verified',['except' => ['login','register','activate']]);
    }

    public function activate($token){
        $ver = Verify_Token::where('token','=',$token)->first();
        if(!isset($ver)){
            return response()->json("Token Not Valid",401);
        }
        $id = $ver->user_id;
        $user= User::find($id);
        $user->verified=true;
        $user->save();
        $ver->delete();
        return response()->json("Activated Successfully",200);
    }

    public function register(Request $request){
        $rules = [
           'username' => 'required|alpha_num|min:3|max:20|unique:users,username',
           'email' => 'bail|required|unique:users,email|min:2|max:60|email',
           'password' => 'bail|required|min:6|max:30',
           'mob' => 'bail|required|digits:11',
           'whatsapp' => 'digit:11'
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()){
            return response()->json(array(
                'success' => false,
                'errors' => $validator->getMessageBag()->toArray()

            ), 400); // 400 being the HTTP code for an invalid request.
        }

        $user = new User();
        $user->username=$request->input('username');
        $user->email=$request->input('email');
        $user->password=bcrypt($request->input('password'));
        $user->mob=$request->input('mob');

        if(isset($request->whatsapp))
            $user->whatsapp=$request->input('whatsapp');
        if( isset($request->facebook))
            $user->facebook=$request->input('facebook');
        $user->role="student";
        $user->save();
        $token = auth()->login($user);
        $veri = new Verify_Token();
        $veri->user_id=$user->id;
        $veri->token=$token;
        $veri->save();

        Mail::to($user->email)->send(new Verification_Token($user,$token));

       return response()->json("Registration Done Successfully , PLease Activate your account through sent link",200);




    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        $rules = ['email' => 'bail|email|required|min:2|max:60',
           'password' => 'bail|required|min:6|max:30'];
        $validator = Validator::make(request(['email', 'password']),$rules);
        if ($validator->fails()){
            return response()->json(array(
                'success' => false,
                'errors' => $validator->getMessageBag()->toArray()

            ), 400); // 400 being the HTTP code for an invalid request.
        }
        $credentials = request(['email', 'password']);

        if (! $token =auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized ,You need to register first'], 401);
        }

        $user =User::where('email','=',$credentials['email'])->first();
        if (!$user->verified){
            $v = $user->verify_token;
            $v->token=$token;
            $v->save();
            Mail::to($user->email)->send(new Verification_Token($user,$token));
            return response()->json("Verification Mail sent to Activate Your Account ",400);

        }

        return $this->respondWithToken($token);
    }



    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();//invalidate the token

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 180
        ]);
    }
}