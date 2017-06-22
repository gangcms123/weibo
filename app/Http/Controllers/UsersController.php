<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Modules\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

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
        $user = User::findOrFail($id);
        $statuses = $user->statuses()
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        return view('users.show', compact('user', 'statuses'));
    }

    //注册用户
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

        $this->sendEmailConfirmationTo($user);
        session()->flash('success', '验证邮件已发送到你的注册邮箱上，请注意查收。');
        return redirect('/');
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

    //给用户发送确认邮件
    protected function sendEmailConfirmationTo($user)
    {
        $view = 'emails.confirm';
        $data = compact('user');
        $to = $user->email;
        $subject = "感谢注册 Sample 应用！请确认你的邮箱。";

        //html方式
        Mail::send($view, $data, function ($message) use ($to, $subject) {
            $message->to($to)->subject($subject);
        });

        //纯文本方式
       /* Mail::raw($data, function ($message) use ($to,$subject) {
            $message->subject($subject .date('Y-m-d H:i:s'));
            $message->to($to);
        });*/
    }

    //用户邮箱验证
    public function confirmEmail($token)
    {
        $user = User::where('activation_token', $token)->firstOrFail();

        $user->activated = true;
        $user->activation_token = null;
        $user->save();

        Auth::login($user);
        session()->flash('success', '恭喜你，激活成功！');
        return redirect()->route('users.show', [$user]);
    }
}
