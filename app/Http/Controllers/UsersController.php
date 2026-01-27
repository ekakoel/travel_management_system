<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\UserLog;
use App\Models\Attention;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Mail\ApprovalUserMail;
use App\Mail\RegistrationUserMail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\StoreUsersRequest;
use App\Http\Requests\UpdateUsersRequest;

class UsersController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','verified']);
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'min:2','string', 'max:255'],
            'username' => ['required', 'string', 'max:25', 'unique:users'],
            'email' => ['required','email','max:255','unique:users'],
            'password' => ['required','string', 'min:8', 'confirmed'],
        ]);
    }

    public function index()
    {
        $adminusers=User::where('type', '=','admin')->paginate(8);
        $userusers=User::where('type', '=','user')->get();
        return view('admin.users', compact('adminusers'),[
            "userusers" => User::where('type', '=',"user"),
            "adminusers" => User::where('type', '=',"admin"),
            "adminusers" => $adminusers,
            "userusers" => $userusers,

        ]);
    }
    // VIEW PROFILE =============================================================================================================>
    public function userdetail($id){
        $duser = User::find($id);
        return view('admin.userdetail',[
                'dusers'=>$duser,
            ]);
        } 
    // VIEW PROFILE =============================================================================================================>
    public function new_register(){
        $now = Carbon::now();
        $user = User::where('id',1)->first();
        return view('emails.newUserRegister',[
                'user'=>$user,
                'now'=>$now,
            ]);
        } 
    // FUNCTION UPDATE PROFILE =============================================================================================================>
    public function func_update_profile(Request $request,$id)
    {
        $user=User::findOrFail($id);
        $now = Carbon::now();
        $user->update([
            "name" =>$request->name, 
            "phone"=>$request->phone,
            "address"=>$request->address,
            "country"=>$request->country,
            "office"=>$request->office,
        ]);
        Mail::to(config('app.reservation_mail'))
        ->send(new RegistrationUserMail($id,$now));
        return redirect("/profile");
    }
    // FUNCTION VERIFIED USER =============================================================================================================>
    public function func_verified_user(Request $request,$id)
    {
        $user=User::findOrFail($id);
        $ferivied = date('Y-m-d H.i',strtotime($request->verified));
        $user->update([
            "email_verified_at" =>$ferivied, 
            "status" =>"Active", 
        ]);
        return redirect("/user-manager")->with('success','User has been verified');
    }

    // FUNCTION UPDATE PROFILE IMAGE =============================================================================================================>
    public function func_update_profileimg(Request $request,$id)
    {
        $user=User::findOrFail($id);
        if($request->hasFile("profileimg")){
            if (File::exists("storage/user/profile/".$user->profileimg)) {
                File::delete("images/user/profile/".$user->profileimg);
            }
            $file=$request->file("profileimg");
            $user->profileimg=time()."_".$file->getClientOriginalName();
            $file->move("storage/user/profile/",$user->profileimg);
            $request['profileimg']=$user->profileimg;
        }
        $user->update([
            "profileimg"=>$user->profileimg,
        ]);
        return redirect("/profile");
    }

    // VIEW USER MANAGER =============================================================================================================>
    public function user_manager(){
        $notifications = auth()->user()->notifications;
        $now = Carbon::now();
        $users=User::all();
        $attentions = Attention::where('page','user-manager')->get();
        // $attentions = Attention::where('page','user-manager')->get();
        $uson = User::select("*")
            ->whereNotNull('session_id')
            ->orderBy('session_id', 'DESC')
            ->paginate(10);
        return view('admin.user-manager',[
            'attentions'=>$attentions,
            'users'=>$users,
            'uson'=>$uson,
            'now'=>$now,
            'notifications'=>$notifications,
        ]);
    }

    // FUNCTION EDIT USER =============================================================================================================>
    public function func_edit_user(Request $request,$id)
    {
        $users=User::findOrFail($id);
        $sts = $request->status;
        if ($sts == "Block") {
            $status = $sts;
            $is_approved = 0;
        }else{
            $status = $sts;
            $is_approved = 0;
        }
        $users->update([
            "type"=>$request->type,
            "code"=>strtoupper($request->code),
            "position"=>$request->position,
            "name"=>$request->name,
            "status"=>$status,
            "is_approved"=>$is_approved,
            "phone"=>$request->phone,
            "email"=>$request->email,
            "office"=>$request->office,
            "address"=>$request->address,
            "country"=>$request->country,
            "comment"=>$request->comment,
        ]);
        // dd($users);
        // USER LOG

        $action = "Update User";
        $service = "User";
        $subservice = "User Manager";
        $page = "user-manager";
        $note = "Update User: ".$id;
        $user_log =new UserLog([
            "action"=>$action,
            "service"=>$service,
            "subservice"=>$subservice,
            "subservice_id"=>$id,
            "page"=>$page,
            "user_id"=>$request->author,
            "user_ip"=>$request->getClientIp(),
            "note" =>$note, 
        ]);
        $user_log->save();
        return redirect("/user-manager")->with('success','User has been successfully updated!');
    }
    // FUNCTION APPROVE USER =============================================================================================================>
    public function func_approve_user(Request $request,$id)
    {
        $user=User::find($id);
        $now = Carbon::now();
        $is_approved=1;
        $approved_at= date('Y-m-d H.i',strtotime($now));
        $details = [
            'title' => 'Account Approval',
            'body' => 'Your account has been approved and can be used to access all services provided by PT. Bali Kami Tour. Thank you.'
        ];
        $user->update([
            "is_approved"=>$is_approved,
            "approved_at"=>$approved_at,
        ]);

        Mail::to($user->email)
        ->send(new ApprovalUserMail($id,$now));
        // USER LOG

        $action = "Approve User";
        $service = "User Manager";
        $subservice = "User Manager";
        $page = "user-manager";
        $note = "Approve User: ".$id;
        $author = Auth::user()->id;
        $user_log =new UserLog([
            "action"=>$action,
            "service"=>$service,
            "subservice"=>$subservice,
            "subservice_id"=>$id,
            "page"=>$page,
            "user_id"=>$author,
            "user_ip"=>$request->getClientIp(),
            "note" =>$note, 
        ]);
        $user_log->save();
        return redirect("/user-manager")->with('success','User has been approved!');
    }

// FUNCTION UPDATE PASSWORD =============================================================================================================>
    public function updatePassword(Request $request){
        # Validation
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|confirmed',
        ]);
        #Match The Old Password
        if(!Hash::check($request->old_password, auth()->user()->password)){
            return redirect("/profile")->with("error", "Old Password Doesn't match!");
        }
        #Update the new Password
        User::whereId(auth()->user()->id)->update([
            'password' => Hash::make($request->new_password)
        ]);
        return redirect("/profile")->with("status", "Password changed successfully!");
    }

// FUNCTION ADD USER =============================================================================================================>
    public function func_create_user(Request $request){
        $validated = $request->validate([
            'name' => 'required|min:2|string|max:255',
            'username' => 'required|string|max:25|unique:users',
            'email' => 'required|email|max:255|unique:users',
        ]);
        $now = Carbon::now();
        $password = Hash::make('1234567890');
        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => $password,
            'type' => 'user',
            'position' => $request->position,
            'status' => 'Active',
            'address' => $request->address,
            'office' => $request->office,
            'country' => $request->country,
            'email_verified_at' => $now,
            'code' => $request->code,
        ]);
        // dd($user);
        return redirect("/user-manager")->with("success", "New User has been added successfully!");
    }

   
}
