<?php

namespace App\Http\Controllers;

use Auth;
use DB;
use App\Models\User;
use App\Models\Category;
use App\Models\Inspiration;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Validator;

class ApiController extends Controller
{
  //================ Login =================//
  public function login(Request $request)
  {
    $rules = [
      'email' => 'required',
      'password' => 'required',

    ];

    $input     = $request->all();
    $validator = Validator::make($input, $rules);
    if ($validator->fails()) {
      $result['id'] = '';
      $result['email'] = '';
      return response()->json(['success' => 'false', 'data' => $result, 'message' => "Please enter all fields"]);
      die();
    }

    if (!auth()->attempt($request->all())) {
      $result['id'] = '';
      $result['email'] = '';
      return response(['status' => 'false', 'data' => $result, 'message' => 'Invalid Credentials']);
      die();
    }

    $logindata = User::Select('id', 'email', 'name')->where('email', $request->email)->orwhere('password', $request->password)->first();
    $id = $logindata->id;
    $dataa['id'] = "$id";
    $dataa['email'] = $logindata->email;
    $dataa['username'] = $logindata->name;
    echo json_encode(array('status' => 'true', 'data' => $dataa, 'message' => 'User Login Successfully'));
    die();
  }

  //================ sIGN UP====================//
  public function signup(Request $request)
  {
    $rules = [
      'name' => 'required',
      'email'    => 'unique:users|required',
      'password' => 'unique:users|required',

    ];

    $data = $request->all();

    $validator = Validator::make($data, $rules);

    $dataa['name'] = '';

    $dataa['email'] = '';

    $dataa['password'] = '';

    $error_msg = '';

    $error_msg = $validator->errors()->first();

    if ($validator->fails()) {

      return response()->json(['status' => 'false', 'data' => $dataa, 'message' => $error_msg]);

      die();
    }

    $data_user = array('name' => $data['name'], 'email' => $data['email'], 'password' => bcrypt($data['password']));

    $user = User::create($data_user);

    if ($user) {

      return response()->json(array('status' => 'true', 'data' => $data, 'message' => 'User Register Successfully'));

      die();
    } else {

      return response()->json(array('status' => 'false', 'data' => $dataa, 'message' => 'Somthing went wrong'));

      die();
    }
  }

  //================ inspiration get data====================//
  public function inspiration_get_data(Request $request)
  {
    $data = Inspiration::orderBy('id', 'desc')->get();
    if ($data) {
      return response()->json(array('status' => 'true', 'data' => $data, 'message' => 'successfully get data'));
      die();
    } else {
      return response()->json(array('status' => 'false', 'data' => '', 'message' => 'not found data'));
      die();
    }
  }

  //================ inspiration Insert data====================//
  public function inspiration_insert_data(Request $request)
  {
    $rules = [
      'title' => 'required',
      'files'    =>  'required|image|mimes:jpeg,png,jpg,gif|max:2048',

    ];

    $data = $request->all();

    $validator = Validator::make($data, $rules);

    $dataa['title'] = '';

    $dataa['files'] = '';

    $error_msg = '';

    $error_msg = $validator->errors()->first();

    if ($validator->fails()) {

      return response()->json(['status' => 'false', 'data' => $dataa, 'message' => $error_msg]);

      die();
    }
    if ($request->file('files')) {
      $imagePath = $request->file('files');
      $image = time() . '.' . $imagePath->getClientOriginalName();
      $destinationPath = public_path('/images');
      $imagePath->move($destinationPath, $image);
    } else {
      $image = 'test.png';
    }
    $datas['title'] = $request->title;
    $datas['files'] = image;
    $data_user = array('title' => $data['title'], 'image' => $image);

    $user = Inspiration::create($data_user);

    if ($user) {

      return response()->json(array('status' => 'true', 'data' => $datas, 'message' => 'Data Register Successfully'));

      die();
    } else {

      return response()->json(array('status' => 'false', 'data' => $dataa, 'message' => 'Somthing went wrong'));

      die();
    }
  }

  //================ inspiration UPDATE data====================//
  public function inspiration_update_data(Request $request)
  {
    $id = $request->id;
    $updated_at = date("Y-m-d H:i:s");
    $rules = [
      'title' => 'required',

    ];

    $data['title'] = $request->title;
    $data['id'] = $request->id;

    $validator = Validator::make($data, $rules);

    $dataa['title'] = '';

    $error_msg = '';

    $error_msg = $validator->errors()->first();

    if ($validator->fails()) {

      return response()->json(['status' => 'false', 'data' => $dataa, 'message' => $error_msg]);

      die();
    }
    if ($request->file('files')) {
      $imagePath = $request->file('files');
      $image = time() . '.' . $imagePath->getClientOriginalName();
      $destinationPath = public_path('/images');
      $imagePath->move($destinationPath, $image);
      $data['files'] = $image;
      $user = Inspiration::where('id', $id)->update(['title' => $request->title, 'image' => $image, 'updated_at' => $request->updated_at]);
    } else {

      $user = Inspiration::where('id', $id)->update(['title' => $request->title, 'updated_at' => $request->updated_at]);
    }
    if ($user) {

      return response()->json(array('status' => 'true', 'data' => $data, 'message' => 'Data Update Successfully'));

      die();
    } else {

      return response()->json(array('status' => 'false', 'data' => $dataa, 'message' => 'Somthing went wrong'));

      die();
    }
  }

  //===============Delete Data=====================//
  public function inspiration_delete_data(Request $request)
  {
    $id = $request->id;
    $data = Inspiration::find($id);
    $image = $data->image;
    $path = public_path() . "/images/" . $image;
    unlink($path);
    $data = Inspiration::find($id)->delete();

    if ($data) {

      return response()->json(array('status' => 'true', 'message' => 'Data Delete Successfully'));

      die();
    } else {

      return response()->json(array('status' => 'false', 'message' => 'Somthing went wrong'));

      die();
    }
  }

  //================ Category get data====================//
  public function category_get_data()
  {
    $data = Category::orderBy('id', 'desc')->get();
    if ($data) {
      return response()->json(array('status' => 'true', 'data' => $data, 'message' => 'successfully get data'));
      die();
    } else {
      return response()->json(array('status' => 'false', 'data' => '', 'message' => 'not found data'));
      die();
    }
  }

  //================ Category delete data====================//
  public function category_delete_data(Request $request)
  {
    $id = $request->id;

    $data = Category::find($id)->delete();

    if ($data) {

      return response()->json(array('status' => 'true', 'message' => 'Data Delete Successfully'));

      die();
    } else {

      return response()->json(array('status' => 'false', 'message' => 'Somthing went wrong'));

      die();
    }
  }

  //================== Category Insert =========================//
  public function category_insert_data(Request $request)
  {
    $rules = [
      'title' => 'required',
      'description'    =>  'required',

    ];

    $data = $request->all();

    $validator = Validator::make($data, $rules);

    $dataa['title'] = '';

    $dataa['description'] = '';

    $error_msg = '';

    $error_msg = $validator->errors()->first();

    if ($validator->fails()) {

      return response()->json(['status' => 'false', 'data' => $dataa, 'message' => $error_msg]);

      die();
    }

    $datas['title'] = $request->title;
    $datas['description'] =  $request->description;
    $data_user = array('title' => $data['title'], 'description' => $data['description']);

    $user = Category::create($data_user);

    if ($user) {

      return response()->json(array('status' => 'true', 'data' => $datas, 'message' => 'Data Register Successfully'));

      die();
    } else {

      return response()->json(array('status' => 'false', 'data' => $dataa, 'message' => 'Somthing went wrong'));

      die();
    }
  }

  //================ Category UPDATE data====================//
  public function category_update_data(Request $request)
  {
    $id = $request->id;
    $updated_at = date("Y-m-d H:i:s");
    $rules = [
      'title' => 'required',
      'description' => 'required',

    ];

    $data['title'] = $request->title;
    $data['description'] = $request->title;
    $data['id'] = $request->id;

    $validator = Validator::make($data, $rules);

    $dataa['title'] = '';
    $dataa['description'] = '';

    $error_msg = '';

    $error_msg = $validator->errors()->first();

    if ($validator->fails()) {

      return response()->json(['status' => 'false', 'data' => $dataa, 'message' => $error_msg]);

      die();
    }

    $cataegory = Category::where('id', $id)->update(['title' => $request->title, 'description' => $request->description, 'updated_at' => $request->updated_at]);

    if ($cataegory) {

      return response()->json(array('status' => 'true', 'data' => $data, 'message' => 'Data Update Successfully'));

      die();
    } else {

      return response()->json(array('status' => 'false', 'data' => $dataa, 'message' => 'Somthing went wrong'));

      die();
    }
  }
}
