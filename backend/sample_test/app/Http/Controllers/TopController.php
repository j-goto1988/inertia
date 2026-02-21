<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TopController extends Controller
{
    // 通常の場合
    public function index()
    {
        $name = '田中 太郎';
        $age = 21;
        $data = [
            'name' => $name,
            'age'  => $age,
        ];

        return view('top.index', $data);
    }

    // Inertiaを使った場合
    public function inertia(): Response
    {
        $name = '鈴木 太郎';
        $age = 22;

        return Inertia::render('Top/Inertia', [
            'data' => [
                'name' => $name,
                'age'  => $age,
            ],
        ]);
    }
}
