<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;
use \Laravel\Socialite\Facades\Socialite;
use Lcobucci\JWT\Exception;


class AuthController extends Controller
{
    /**
     * Manual login
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request){

        $data = [
            'email' => $request->email,
            'password' => $request->password,
            'active'=>true
        ];
        if (!Auth::attempt($data)) {
            return response()->json([
                'message' => 'Unauthorized'], 401);
        }
        $user = $request->user();
        $tokenResult = $user->createToken('Personal Access Token');

        $token = $tokenResult->token;
        if ($request->remember) {
            $token->expires_at = Carbon::now()->addWeeks(1);
        }
        $token->save();

        $data=[
            'user'=>$user,
            'access_token'=>[
                "token"=>$tokenResult->accessToken,
                "type"=>'Bearer',
                'expires_at' => Carbon::parse(
                    $tokenResult->token->expires_at)
                    ->toDateTimeString()
            ]
        ];
        $log="The user '".$user->id."' logged in using manual auth.";
        $this->log('info',$log,'web',$user);
        return $this->response('false',Response::HTTP_OK,'200 OK',$data);
    }

    /**
     * logout session
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        $log="The user '".$request->user()->id."' logged out.";
        $this->log('info',$log,'web',$request->user());
        return $this->response('false',Response::HTTP_OK,'200 OK',['message' =>
            'Successfully logged out']);
    }

    public function loginWithGoogle(Request $request){
        try {
            $driver = Socialite::driver('google');
            if(isset($request->accessToken)){
                $user = $driver->userFromToken($request->accessToken);
            }else{
                $user = $driver->user();
            }

            $data_user = User::where('email', '=', $user->getEmail())
                ->where('active', '=', true)
                ->first();
            if(!isset($data_user)){
                 $data=[
                    'name'=>$user->user['given_name'],
                    'lastname'=>$user->user['family_name'],
                    'email'=>$user->getEmail(),
                    'gender'=>null,
                    'password'=>null,
                    'photography'=>$user->getAvatar(),
                    'cod_rol'=>2
                ];
                $data_user = User::create($data);
            }
            $tokenResult = $data_user->createToken('Personal Access Token');
            $token = $tokenResult->token;
            $token->save();
            $redirect_auth = env('GOOGLE_APP_REDIRECT_WITH_AUTH');
            $log = "The user '" . $data_user->id . "' logged in using google.";
            $this->log('info', $log, 'web', $data_user);
            if(isset($request->accessToken)) {
                $data = [
                    'user' => $data_user,
                    'access_token' => [
                        "token" => $tokenResult->accessToken,
                        "type" => 'Bearer',
                        'expires_at' => Carbon::parse(
                            $tokenResult->token->expires_at)
                            ->toDateTimeString()
                    ]
                ];
                return $this->response('false', Response::HTTP_OK, '200 OK', $data);
            }
            return redirect("$redirect_auth?access_token=$tokenResult->accessToken");
        }catch(\Exception $e){
            return $e->getMessage();
            return response()->json([
                'message' => 'Not Found'], 404);
        }

    }

    public function googleRedirect(){
        return Socialite::driver('google')->redirect();
    }
}
