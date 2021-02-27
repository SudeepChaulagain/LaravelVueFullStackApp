<?php

namespace App\Http\Controllers;

use App\Category;
use App\Role;
use Illuminate\Http\Request;

use App\Tag;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;


class AdminController extends Controller
{
    public function index(Request $request){
        //need to check if we are logged in and admin user...
        if (!Auth::check() && $request->path() != 'login') {
            return redirect('/login');
        }

        if (!Auth::check() && $request->path() == 'login') {

            return view('welcome');
        }
        //when have already logged in we need to allow admin user to home and normal user to login again
        $user = Auth::user();
        if ($user->role_id == 'User') {
            return redirect('/login');
        }
        if ($request->path() == 'login') {
            return redirect('/');
        }
        return $this->checkForPermission($user, $request);


    }

    //check for permissiom
    public function checkForPermission($user, $request){
        $permission = json_decode($user->role->permission); //gives permission object {reousrces,....}
        $hasPermission = false;
        if (!$permission) {
            return view('welcome');
        }
        foreach($permission as $p){
            if ($p->name == $request->path()) {  //permission name->tags chai route tags sanga milcha vanae
               if ($p->read) { //read permission nai xaina vanae aaru access garna payen so read pahile check garne
                  $hasPermission = true;
               }
            }
        }
        if ($hasPermission) {
            return view('welcome');
        }
        return view('pagenotfound');

    }

    public function logout(){
        Auth::logout();
        return redirect('/login');
    }


    public function addTag(Request $request){
        //validate request
        $this->validate($request,[
            'tagName'=>'required'
        ]);
        return Tag::create([
            'tagName'=>$request->tagName
        ]);
    }
    public function getTags(){
        return Tag::orderBy('id', 'asc')->get();
    }

    public function editTag(Request $request){
        $this->validate($request,[
            'tagName'=>'required',
            'id' => 'required'
        ]);
        return Tag::where('id', $request->id)->update([
            'tagName' =>$request->tagName
        ]);
    }

    public function deleteTag(Request $request){
        $this->validate($request,[
            'tagName'=>'required',
            'id' => 'required'
        ]);
        return Tag::where('id', $request->id)->delete();

    }

    public function upload(Request $request){
        $this->validate($request, [
            'file'=>'required|mimes:jpeg,jpg,png'
        ]);
        $uploadedFile = time().'.'.$request->file->extension();
        $request->file->move(public_path('uploads'), $uploadedFile);
        return $uploadedFile;
    }
    public function deleteImage(Request $request){
       $fileName = $request->imageName;
       $this->deleteFileFromServer($fileName);
       return 'File Deleted Successfully!';

    }
    public function deleteFileFromServer($fileName, $hasFullPath = false){
        if (!$hasFullPath) {
            $filePath = public_path().'/uploads/'.$fileName;
        }
       if (file_exists($filePath)) {
           @unlink($filePath);
       }
       return;
    }

    public function addCategory(Request $request){
        //validate request
        $this->validate($request,[
            'categoryName'=>'required',
            'iconImage'=>'required'
        ]);
        return Category::create([
            'categoryName'=>$request->categoryName,
            'iconImage'=>$request->iconImage
        ]);
    }

    public function getCategory(){
        return Category::orderBy('id','asc')->get();
    }
    public function editCategory(Request $request){
        //validate request
        $this->validate($request,[
            'categoryName'=>'required',
            'iconImage'=>'required'
        ]);
        return Category::where('id', $request->id)->update([
            'categoryName' =>$request->categoryName,
            'iconImage' =>$request->iconImage,

        ]);
    }
    public function deleteCategory(Request $request){
        //first delete the orginal file from the server
        $this->deleteFileFromServer($request->iconImage);
        $this->validate($request,[
            'id' => 'required'
        ]);
        return Category::where('id', $request->id)->delete();

    }

    public function getUsers(){
        return User::with('role')->get();

    }

    public function createUser(Request $request){
        $this->validate($request,[
            'fullname' =>'required',
            'email' => 'bail|required|email|unique:users',
            'password' => 'bail|required|min:6',
            'role_id' => 'required'
        ]);
        $password = bcrypt($request->password);
        $user = User::create([
            'fullname' => $request->fullname,
            'email' =>$request->email,
            'password' => $password,
            'role_id' =>$request->role_id
        ]);
        return $user;
    }
    public function editUser(Request $request){
        $this->validate($request,[
            'fullname' =>'required',
            'email' => "bail|required|email|unique:users,email,$request->id", //this validation prevents having unique email when the user with same email tries to update other things
            'password' => 'min:6', //bail prevents from doing other validtaion if the first one fails
            'role_id' => 'required'
        ]);
        $data = [
            'fullname' => $request->fullname,
            'email' =>$request->email,
            'role_id' =>$request->role_id
        ];
        if($request->password){
            $password = bcrypt($request->password);
            $data['password'] = $password;
        }
        $user = User::where('id', $request->id)->update($data);
        return $user;
    }
    public function deleteUser(Request $request){
        $this->validate($request,[
            'fullname'=>'required',
            'email' => 'required',
            'role_id' => 'required',
            // 'password' => 'required',
        ]);
        return User::where('id', $request->id)->delete();
    }

    public function adminLogin(Request $request){
        $this->validate($request,[
            'email' => 'bail|required|email',
            'password' => 'bail|required|min:6',
        ]);
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            //laravel ley table plural cha vannae singular vayera access garcha
            //yeha user->role->isAdmin garna sakhcha kina vanae role_id foreign key huncha user table ma
            //role_id field user table ma rakhne bhetikaii roles table ko ho vanera chincha ani foregin key huncha
            if ($user->role->isAdmin == 0) {
               Auth::logout();
               return response()->json([
                'msg'=>'Incorrect login details!'
               ],401);
            }
            return response()->json([
                'msg'=>'You are logged in!',
                'user'=>$user
            ]);
        }
        else{
            return response()->json([
                'msg'=>'Incorrect login details!'
            ],401);
        }
    }

    public function getRoles()
    {
        return Role::all();
    }

    public function addRole(Request $request)
    {
        // validate request
        $this->validate($request, [
            'rolename' => 'required',
        ]);
        return Role::create([
            'rolename' => $request->rolename,
        ]);
    }
    public function editRole(Request $request)
    {
        // validate request
        $this->validate($request, [
            'rolename' => 'required',
        ]);
        return Role::where('id', $request->id)->update([
            'rolename' => $request->rolename,
        ]);
    }

    public function assignRole(Request $request){
        $this->validate($request,[
            'permission' => 'required',
            'id' => 'required'
        ]);
        return Role::where('id', $request->id)->update([
            'permission' => $request->permission
        ]);
    }




}
