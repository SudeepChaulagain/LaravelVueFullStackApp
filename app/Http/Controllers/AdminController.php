<?php

namespace App\Http\Controllers;

use App\Category;
use Illuminate\Http\Request;

use App\Tag;

class AdminController extends Controller
{
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
    public function deleteFileFromServer($fileName){
        $filePath = public_path().'/uploads/'.$fileName;
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


}
