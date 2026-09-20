<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function loginPage()
    {
        return view('front.account.login');
    }

    public function registerPage()
    {
        return view('front.account.register');
    }

    public function register(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required|min:3',
                'email' => 'required|email|unique:users',
                'password' => 'required|min:5|confirmed'
            ]
        );


        if ($validator->passes()) {

            $user = new User;
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->password = Hash::make($request->password);
            $user->save();

            session()->flash('success', 'Registration successful! You can now log in.');

            return response()->json(['status' => true]);
        } else {
            return response()->json(['status' => false, 'errors' => $validator->errors()]);
        }
    }

    public function login(Request $request) {
        $validator = Validator::make($request->all(),
        [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->passes()) {
            if (Auth::attempt(['email' => $request->email, 'password' => $request->password], $request->get('remember', false))) {
                return redirect()->intended(route('account.profile'));
            } else {
                return redirect()->route('account.login.show')->withInput($request->only('email'))->with('error', 'Either email/password is incorrect');
            }
        } else {
            return redirect()->route('account.login.show')->withErrors($validator)->withInput($request->only('email'));
        }
    }

    public function profile(){
        $user = Auth::user();
        return view('front.account.profile', compact('user'));
    }

    public function logout(){
        Auth::logout();
        session()->flash('success', "You have Successfully logged out!");
        return redirect()->route('account.login.show');
    }
}
