<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;
use \Laravel\Socialite\Facades\Socialite;


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

         $a= Socialite::driver('google')->user() ;
         return $a->getEmail();
      return   Socialite::driver('google')->user();
       /* try {
            return $user = Socialite::driver('google')->user();
            $data_user = User::where('type_auth', '=', 'oidc')
                ->where('email_inst', '=', $user->getEmail())
                ->where('deleted', '=', false)
                ->first();
            if ($data_user != null) {
                $tokenResult = $data_user->createToken('Personal Access Token');
                $token = $tokenResult->token;
                $token->save();
                $redirect_auth = env('KEYCLOAK_REDIRECT_AUTH');
                $msj['change_success'] = 'Se actualizo el password correctamente';
                $log = "The user '" . $data_user->id . "' logged in using keycloak.";
                $this->log('info', $log, 'web', $data_user);
                return redirect("$redirect_auth?access_token=$tokenResult->accessToken");
            } else {
                return response()->json([
                    'message' => 'Unauthorized'], 401);
            }
        }catch(\Exception $e){
            //return $e->getMessage();
            return response()->json([
                'message' => 'Not Found'], 404);
        }*/

    }

    public function googleRedirect(){
        return Socialite::driver('google')->redirect();
    }
}
