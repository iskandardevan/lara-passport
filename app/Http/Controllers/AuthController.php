<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\User;
class AuthController extends Controller
{
    /**
     * Create user
     *
     * @param  [string] name
     * @param  [string] email
     * @param  [string] password
     * @param  [string] password_confirmation
     * @return [string] message
     */
    public function signup(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|confirmed'
        ]);
        $user = new User([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);
        $user->save();
        return response()->json([
            'message' => 'Successfully created user!'
        ], 201);
    }
    
    /**
     * Login user and create token
     *
     * @param  [string] email
     * @param  [string] password
     * @param  [boolean] remember_me
     * @return [string] access_token
     * @return [string] token_type
     * @return [string] expires_at
     */
    public function signin(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
            'remember_me' => 'boolean'
        ]);
        $credentials = request(['email', 'password']);
        if(!Auth::attempt($credentials))
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        $user = $request->user();
        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;
        if ($request->remember_me)
            $token->expires_at = Carbon::now()->addDays(1);
        $token->save();
        return response()->json([
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse(
                $tokenResult->token->expires_at
            )->toDateTimeString()
        ]);
    }
    
    /**
     * Logout user (Revoke the token)
     *
     * @return [string] message
     */
    public function signout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }
    
    /**
     * Get the authenticated User
     *
     * @return [json] user object
     */

    public function user(Request $request)
    { 
        return response()->json($request->user());
    } 

    public function update(Request $request)
    {
        error_log(json_encode($request->all()));
        try {
            $request->validate([
                'name' => 'required|string',
                'email' => 'required|string|email|unique:users,email,' . $request->user()->id,
                'password' => 'required|string|confirmed'
            ]);
        } catch (\Throwable $th) {
            error_log($th->getMessage());
        }
        
        $user = $request->user(); // get user from token
        
        $user->name = $request->name; 
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->save();
        
        return response()->json([
            'message' => 'Successfully updated user!',
            'user' => $user
        ]);
    }

    public function destroy(Request $request)
    {
        $request->user()->delete();
        return response()->json([
            'message' => 'Successfully deleted user!'
        ]);
    }

}