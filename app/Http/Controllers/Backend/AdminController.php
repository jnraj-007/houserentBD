<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\Admin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class AdminController extends Controller
{
    public function adminList(){
       $adminList= Admin::where('id','!=',\auth('admin')->user()->id)->get();
       $title="Admin List";
       return view('backend.layouts.admin.adminList',compact('title','adminList'));

    }
    public function adminForm(){
        $title="Add Admin";
        return view('backend.layouts.admin.adminAdd',compact('title'));
    }
    public function adminAdd(Request $request){

         $request->validate([
            'name'=>'required',
            'password'=>'required|min:6',
             'email'=>'required|email|unique:admins',
             'contact'=>'required|unique:admins',
             'status'=>'required',
             'role'=>'required',
             'photo'=>'required'
         ]);
//
             $image="";

        if ($request->hasFile('photo'))
        {
            $file=$request->file('photo');
            if ($file->isValid()){

                $image=date('Ymdhms').'.'.$file->getClientOriginalExtension();
                $file->storeAs('admins',$image);


            }
        }
      Admin::create([
            'name'=>$request->name,
            'email'=>$request->email,
            'password'=>bcrypt($request->password),
            'address'=>$request->address,
            'contact'=>$request->contact,
            'status'=>$request->status,
            'role'=>$request->role,
          'image'=>$image
        ]);
        return redirect()->route('admin.list');
    }

    public function deleteAdmin($id)
    {
        $del= Admin::find($id);
        $del->delete();
        return redirect()->route('admin.list');

    }

    public function loginForm()
    {
        return view('backend.layouts.admin.adminlogin');
    }

    public function login(Request $request)
    {
//        dd($request->all());


        //validation
        $request->validate([
            'email'=>'email|required',
            'password'=>'required|min:6'
        ]);
        $admin_auth=$request->only( 'email','password');
//        dd($admin_auth);
        if(Auth::guard('admin')->attempt($admin_auth)){
          $request->session()->regenerate();
          return redirect()->route('home');


        }
        return back()->withErrors([
            'email'=>'Invalid credentials'
        ]);


    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('admin.loginForm');
    }

    public function adminProfile($id)
    {
        $title="Admin Profile";
        $admin=Admin::find($id);
        return view('backend.layouts.adminprofile.adminprofile',compact('admin','title'));
    }

    public function updateAdminProfile( Request $request,$id)
    {

//dd($request->all());
        if(auth('admin')->user()->role=='superAdmin'){
            $admin= Admin::find($id);

            $admin->update([
                'address' => $request->address,
                'name'=>$request->name
            ]);
            return redirect()->route('admin.profile',$id)->with('success', 'Profile updated Successfully');

        }else{
            $request->validate([
                'email'=>'required|email',
                'password'=>'required'
            ]);
            $admin_auth=$request->only('email','password');
            if (Auth::guard('admin')->attempt($admin_auth)){

                $request->validate([
                    'name' => 'required',
                    'address' => 'required',

                ]);
                $admin= Admin::find($id);

                $admin->update([
                    'address' => $request->address,

                ]);


                return redirect()->route('admin.profile',$id)->with('success', 'Profile updated Successfully');
            }

            else{
                return redirect()->route('admin.profile',$id)->with('danger', 'Wrong Password');

            }
        }

    }

    public function adminPasswordUpdate(Request $request,$id)
    {
        if(auth('admin')->user()->role=='superAdmin'){

            $request->validate([
                'email'=>'required|email',
                'newPassword1'=>'required|min:6',
                'newPassword2'=>'required|min:6',
                'password'=>'required',
            ]);
            $superAdmin_auth=$request->only('email','password');
            if (Auth::guard('admin')->attempt($superAdmin_auth)){
                if ($request->newPassword1==$request->newPassword2){
                    $password=Admin::find($id);
                    $password->update([
                        'password'=>bcrypt($request->newPassword2)
                    ]);
                    return redirect()->route('admin.profile',$id)->with('success','Password Changed Successfully');
                }else{
                    return redirect()->route('admin.profile',$id)->with('danger',' New Password Does not Match!!!');
                }
            }else{

                return redirect()->route('admin.profile',$id)->with('danger',' Super admin Password Does not Matched!!!');
            }




        }
        else{
            $request->validate([
                'email'=>'required|email',
                'password'=>'required',
                'newPassword1'=>'required|min:6',
                'newPassword2'=>'required|min:6',
            ]);
            $admin_auth=$request->only('email','password');
            if (Auth::guard('admin')->attempt($admin_auth)){
                if ($request->newPassword1==$request->newPassword2){

                    $password=Admin::find($id);
                    $password->update([
                        'password'=>bcrypt($request->newPassword2)
                    ]);
                    return redirect()->route('admin.profile',$id)->with('success','Password Changed Successfully');
                }else{
                    return redirect()->route('admin.profile',$id)->with('success',' New Password Does not Match!!!');
                }

            }else{
                return redirect()->route('admin.profile',$id)->with('danger',' you entered wrong password. Please contact with SuperAdmin!!!');
            }
        }



    }

    public function updateAdminPhoto( Request $request,$id)
    {
        $request->validate([
           'image'=>'required|image',
            'photo'=>'required'

        ]);

        if ($request->photo!='userImage.jpg'){
            File::delete('image/admins/'.$request->photo);
        }
        $image="";
        if ($request->hasFile('image'))
        {
            $file=$request->file('image');
            if ($file->isValid()){

                $image=date('Ymdhms').'.'.$file->getClientOriginalExtension();
                $file->storeAs('admins',$image);


            }
        }
        $updatePhoto=Admin::find($id);
        $updatePhoto->update([
            'image'=>$image
        ]);
        return redirect()->route('admin.profile',$id)->with('success','photo update successfully!!!');
    }



    public function superAdminProfile($id)
    {
        $title=" Super Admin Profile";
        $admin=Admin::find($id);
        return view('backend.layouts.superAdminProfile.superAdminProfile',compact('admin','title'));
    }


}
