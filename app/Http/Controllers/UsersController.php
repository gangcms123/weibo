<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Modules\User;
use Illuminate\Support\Facades\Auth;

class UsersController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->only([
            'update','edit','destroy'
        ]);

        $this->middleware('guest', [
            'only' => ['create']
        ]);
    }

    //显示所有用户
    public function index()
    {
        $users = User::paginate(10);
        return view('users.index', compact('users'));
    }

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

        Auth::login($user);
        session()->flash('success', '欢迎，您将在这里开启一段新的旅程~');
        return redirect()->route('users.show', [$user]);
    }

    //编辑用户
    public function edit($id)
    {
        $user = User::findOrFail($id);
        $this->authorize('update', $user);
        return view('users.edit', compact('user'));
    }

    //更新用户信息
    public function update($id, Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:50',
            'password' => 'required|confirmed|min:6'
        ]);

        $user = User::findOrFail($id);
        $this->authorize('update', $user);
        $user->update([
            'name' => $request->name,
            'password' => bcrypt($request->password),
        ]);
        session()->flash('success', '个人资料更新成功！');
        return redirect()->route('users.show', $id);
    }

    //删除用户
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $this->authorize('destroy', $user);
        $user->delete();
        session()->flash('success', '成功删除用户！');
        return back();
    }
}
