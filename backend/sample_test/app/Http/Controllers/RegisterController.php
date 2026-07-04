<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    // 入力画面
    public function input()
    {
        return view('register.input', []);
    }

    // 確認画面
    public function confirm(RegisterRequest $request)
    {
        return view('register.confirm', [
             'form' => $request->validated(),
        ]);
    }

    public function store(RegisterRequest $request)
    {
        User::create([
            'name' => $request->validated('name'),
            'email' => $request->validated('email'),
            'password' => Hash::make(
                $request->validated('password')
            ),
        ]);

        return redirect('/register/done');
    }

    public function back(RegisterRequest $request)
    {
        return redirect('/register/input')
        ->withInput(
            $request->validated()
        );
    }

     // 完了画面
    public function done()
    {
        return view('register.done', []);
    }
}
