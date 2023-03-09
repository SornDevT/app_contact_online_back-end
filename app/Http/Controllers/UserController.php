<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    //
    public function get(){
        $user = User::all();
        return $user;
    }

    public function get_all_user(){
        $user = User::all();
        return $user;
    }

    public function register(Request $request){

        try {

                $check_tel = User::where('tel',$request->tel)->get();

                if($check_tel->count()){
                    $success = false;
                    $message = "ເບີໂທນີ້: ".$request->tel." ເຄີຍລົງທະບຽນແລ້ວ!";
                } else {

                    $user = new User();
                    $user->name = $request->name;
                    $user->last_name = $request->last_name;
                    $user->gender = $request->gender;
                    $user->tel = $request->tel;
                    // $user->image = $generated_new_name;
                    $user->password = Hash::make($request->password);
                    $user->birth_day = $request->birth_day;
                    $user->add_village = $request->add_village;
                    $user->add_city = $request->add_city;
                    $user->add_province = $request->add_province;
                    $user->add_detail = $request->add_detail;
                    $user->email = $request->email;
                    $user->web = $request->web;
                    $user->job = $request->job;
                    $user->job_type = $request->job_type;
                    $user->user_type = 'user';
                    $user->save();
            
            $success = true;
            $message = "ລົງທະບຽນສຳເລັດ";
        }

        } catch (\Illuminate\Database\QueryException $ex) {
            $success = false;
            $message = $ex->getMessage();
        }

        $response = [
            'success' => $success,
            'message' => $message,
        ];
        return response()->json($response);
    }

    public function mobile_login(Request $request){

        $request->validate([
            'tel' => 'required',
            'password' => 'required',
            'device_name' => 'required',
        ]);
     
        $user = User::where('tel', $request->tel)->first();

        // return $user;
     
        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'message' => 'ລະຫັດຜ່ານຂອງທ່ານບໍ່ຖຶກຕ້ອງ! ກະລຸນາກວດຄືນ.....',
            ]);
        }
     
        return $user->createToken($request->device_name)->plainTextToken;
    }

    public function mobile_logout(Request $request){

        $user = $request->user();
        $user->tokens()->delete();

    }

    public function check_user(){
        // $user = User::all();
        return Auth()->User();
    }

}
