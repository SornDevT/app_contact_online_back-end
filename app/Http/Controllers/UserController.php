<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use Image;

class UserController extends Controller
{
    //
    public function get(){
        $user = User::all();
        return $user;
    }

    public function get_all_user(){
        $user = User::all();
        // return $user;

        $response = [
            'user' => $user,
        ];
        return response()->json($response);
    }

    public function register(Request $request){

        try {

                $check_all_user = User::all();

                $check_tel = User::where('tel',$request->tel)->get();

                if($check_tel->count()){
                    $success = false;
                    $message = "ເບີໂທນີ້: ".$request->tel." ເຄີຍລົງທະບຽນແລ້ວ!";
                } else {

                    if($check_all_user->count()){
                            $user_type = 'user';
                    } else {
                        $user_type = 'admin';
                    }

                   
                    
                        if($request->file('image')){
                     
                                $upload_path = "img";
                                $generated_new_name = time().'.'.$request->image->getClientOriginalExtension();
                                $image = $request->file('image');
                                $img = Image::make($image->getRealpath());
                                $img->resize(800, null, function($constraint){
                                    $constraint->aspectRatio();
                                });

                                $img->save($upload_path.'/'.$generated_new_name);

                        $user = new User();
                        $user->name = $request->name;
                        $user->last_name = $request->last_name;
                        $user->gender = $request->gender;
                        $user->tel = $request->tel;
                        $user->image = $generated_new_name;
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
                        $user->user_type = $user_type;
                        $user->save();

                        } else {

                            $user = new User();
                            $user->name = $request->name;
                            $user->last_name = $request->last_name;
                            $user->gender = $request->gender;
                            $user->tel = $request->tel;
                            $user->image = '';
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
                            $user->user_type = $user_type;
                            $user->save();
                        }

                    

                    
            
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
        $response = [
            'user' => Auth()->User(),
        ];
        return response()->json($response);
   
    }

    public function get_user_one($id){

        try {

        $user = User::find($id);
        
            $success = true;
            $message = "ສຳເລັດ";


        } catch (\Illuminate\Database\QueryException $ex) {
            $success = false;
            $user = null;
            $message = $ex->getMessage();
        }

        $response = [
            'user' => $user,
            'success' => $success,
            'message' => $message,
        ];
        return response()->json($response);

    }


    public function update_user($id,Request $request){
        try {

                $user = User::find($id);
                
                // ບໍ່ມີລະຫັດຜ່ານສົ່ງມາ
                if($request->password == '' || $request->password == null){



                    if($request->file('image')){

                        // ມີຮູບສົ່ງມາ
                     
                        $upload_path = "img";

                        // ກວດຊອບ ລຶບຮູບເກົ່າອອກ
                        if($user->image!='' && $user->image!=null){
                            if(file_exists('img/'.$user->image)){
                                unlink('img/'.$user->image);
                            }
                        }


                        $generated_new_name = time().'.'.$request->image->getClientOriginalExtension();
                        $image = $request->file('image');
                        $img = Image::make($image->getRealpath());
                        $img->resize(800, null, function($constraint){
                            $constraint->aspectRatio();
                        });

                        $img->save($upload_path.'/'.$generated_new_name);

                        // ອັບເດດຂໍ້ມູນ
                        $user->update([
                            'name' => $request->name,
                            'last_name' => $request->last_name,
                            'gender' => $request->gender,
                            // 'password' => Hash::make($request->password),
                            'image' => $generated_new_name,
                            'tel' => $request->tel,
                            'birth_day' => $request->birth_day,
                            'add_village' => $request->add_village,
                            'add_city' => $request->add_city,
                            'add_province' => $request->add_province,
                            'add_detail' => $request->add_detail,
                            'email' => $request->email,
                            'web' => $request->web,
                            'job' => $request->job,
                            'job_type' => $request->job_type,
                        ]);
                        
                    } else {

                        // ບໍ່ມີຮູບສົ່ງມາ

                        // ອັບເດດຂໍ້ມູນ
                        // ອັບເດດຂໍ້ມູນ
                        $user->update([
                            'name' => $request->name,
                            'last_name' => $request->last_name,
                            'gender' => $request->gender,
                            // 'password' => Hash::make($request->password),
                            // 'image' => $generated_new_name,
                            'tel' => $request->tel,
                            'birth_day' => $request->birth_day,
                            'add_village' => $request->add_village,
                            'add_city' => $request->add_city,
                            'add_province' => $request->add_province,
                            'add_detail' => $request->add_detail,
                            'email' => $request->email,
                            'web' => $request->web,
                            'job' => $request->job,
                            'job_type' => $request->job_type,
                        ]);



                    }

                
               

            } else {

                /// ມີລະຫັດຜ່ານສົ່ງມາ

                if($request->file('image')){

                    // ມີຮູບສົ່ງມາ
                 
                    $upload_path = "img";

                    // ກວດຊອບ ລຶບຮູບເກົ່າອອກ
                    if($user->image!='' && $user->image!=null){
                        if(file_exists('img/'.$user->image)){
                            unlink('img/'.$user->image);
                        }
                    }


                    $generated_new_name = time().'.'.$request->image->getClientOriginalExtension();
                    $image = $request->file('image');
                    $img = Image::make($image->getRealpath());
                    $img->resize(800, null, function($constraint){
                        $constraint->aspectRatio();
                    });

                    $img->save($upload_path.'/'.$generated_new_name);

                    // ອັບເດດຂໍ້ມູນ
                    $user->update([
                        'name' => $request->name,
                        'last_name' => $request->last_name,
                        'gender' => $request->gender,
                        'password' => Hash::make($request->password),
                        'image' => $generated_new_name,
                        'tel' => $request->tel,
                        'birth_day' => $request->birth_day,
                        'add_village' => $request->add_village,
                        'add_city' => $request->add_city,
                        'add_province' => $request->add_province,
                        'add_detail' => $request->add_detail,
                        'email' => $request->email,
                        'web' => $request->web,
                        'job' => $request->job,
                        'job_type' => $request->job_type,
                    ]);
                    
                } else {

                    // ບໍ່ມີຮູບສົ່ງມາ
                    // ອັບເດດຂໍ້ມູນ
                    $user->update([
                        'name' => $request->name,
                        'last_name' => $request->last_name,
                        'gender' => $request->gender,
                        'password' => Hash::make($request->password),
                        // 'image' => $generated_new_name,
                        'tel' => $request->tel,
                        'birth_day' => $request->birth_day,
                        'add_village' => $request->add_village,
                        'add_city' => $request->add_city,
                        'add_province' => $request->add_province,
                        'add_detail' => $request->add_detail,
                        'email' => $request->email,
                        'web' => $request->web,
                        'job' => $request->job,
                        'job_type' => $request->job_type,
                    ]);

                }
            }


                $success = true;
                $message = "ສຳເລັດ";
    
    
            } catch (\Illuminate\Database\QueryException $ex) {
                $success = false;
                $user = null;
                $message = $ex->getMessage();
            }
    
            $response = [
                // 'user' => $user,
                'success' => $success,
                'message' => $message,
            ];
            return response()->json($response);
    }

    public function delete_user($id){
        try {

                $user = User::find($id);
                $user->delete();
            
                $success = true;
                $message = "ລຶບຂໍ້ມູນສຳເລັດ";
    
    
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

}
