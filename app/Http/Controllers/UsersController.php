<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Modules\User;

class UsersController extends Controller
{
    //创建用户页面
    public function create()
    {
        return view('users.create');
    }

    //显示用户信息
    public function show($id)
    {
        $user = User::find($id);
        return view('users.show', compact('user'));
    }

    //添加用户
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:50',
            'email' => 'required|email|unique:users|max:255',
            'password' => 'required|confirmed|min:6'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        session()->flash('success', '欢迎，您将在这里开启一段新的旅程~');
        return redirect()->route('users.show', [$user]);
    }
}
