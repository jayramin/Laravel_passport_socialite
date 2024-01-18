<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Validator;

class PassportController extends Controller
{
    //
    public function register(Request $request)
    {
        // dd('a');
        // $validator = $this->validate($request, [
        //     'name' => 'required|min:3',
        //     'email' => 'required|email|unique:users',
        //     'password' => 'required|min:6',
        // ]);
        $validator = Validator::make($request->all(), [
            // 'email' => 'required|min:3|max:255',
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);

        $validationErorrs=[];
        if ($validator->fails()) {
            foreach ($validator->messages()->getMessages() as $field_name => $messages){
                $validationErorrs[$field_name]=$messages[0];
            // var_dump($messages); // messages are retrieved (publicly)
            }
            return response()->json(['validation_erorrs' => $validationErorrs], 200);
            return redirect('post/create')
                        ->withErrors($validator)
                        ->withInput();

        }
        // else{
        //     dd("else");
        // }
        // dd("test");
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);
 
        $token = $user->createToken('TutsForWeb')->accessToken;
        dd($token);
        return response()->json(['token' => $token], 200);
    }
 
    /**
     * Handles Login Request
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $credentials = [
            'email' => $request->email,
            'password' => $request->password
        ];
 
        if (auth()->attempt($credentials)) {
            $token = auth()->user()->createToken('TutsForWeb')->accessToken;
            return response()->json(['token' => $token], 200);
        } else {
            return response()->json(['error' => 'UnAuthorised'], 401);
        }
    }
 
    /**
     * Returns Authenticated User Details
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function details()
    {
        return response()->json(['user' => auth()->user()], 200);
    }
}
