<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Request;

class CBHook extends Controller {

	/*
	| --------------------------------------
	| Please note that you should re-login to see the session work
	| --------------------------------------
	|
	*/
	public function afterLogin() {
		$users = User::where("email", request('email'))->first();

        if (Hash::check(request('password'), $users->password)) {
            if($users->status == 'INACTIVE'){
                Session::flush();
                return redirect()->route('getLogin')->with('message', 'The user does not exist!');
            }
        }
	}
}
