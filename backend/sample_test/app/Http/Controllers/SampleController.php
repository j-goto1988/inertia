<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SampleController extends Controller
{
    // Inertiaを使った場合
    public function sample1(): Response
    {
        $name = '鈴木 太郎';
        $age = 22;
        $str = '&amp;';

        return Inertia::render('Sample/Sample1', [
            'data' => [
                'name' => $name,
                'age'  => $age,
                'str' => $str,
            ],
        ]);
    }

    public function sample2(): Response
    {
        $datas = [
            ['name' => '鈴木', 'age' => 22],
            ['name' => '佐藤', 'age' => 27],
            ['name' => '田中', 'age' => 24],
            ['name' => '安藤', 'age' => 29],
        ];

        return Inertia::render('Sample/Sample2', [
           'datas' => $datas,
        ]);
    }

    public function sample3(): Response
    {
        $datas = [
            ['id' => 1, 'name' => '鈴木', 'age' => 22],
            ['id' => 2, 'name' => '佐藤', 'age' => 27],
            ['id' => 3, 'name' => '田中', 'age' => 24],
            ['id' => 4, 'name' => '安藤', 'age' => 29],
        ];

        return Inertia::render('Sample/Sample3', [
           'datas' => $datas,
        ]);
    }

    public function sample4(): Response
    {
        $datas = [
            ['id' => 1, 'name' => '鈴木', 'age' => 22],
            ['id' => 2, 'name' => '佐藤', 'age' => 27],
            ['id' => 3, 'name' => '田中', 'age' => 24],
            ['id' => 4, 'name' => '安藤', 'age' => 29],
        ];

        return Inertia::render('Sample/Sample4', [
           'datas' => $datas,
        ]);
    }

    public function sample5(): Response
    {
        $datas = [
            ['name' => '鈴木', 'age' => 22],
            ['name' => '佐藤', 'age' => 27],
            ['name' => '田中', 'age' => 24],
            ['name' => '安藤', 'age' => 29],
        ];

        return Inertia::render('Sample/Sample5', [
           'datas' => $datas,
        ]);
    }

    public function sample6(): Response
    {
        $name = '鈴木 太郎';
        $age = 22;
        $str = '&amp;';
        $num = 0;

        return Inertia::render('Sample/Sample6', [
            'data' => [
                'name' => $name,
                'age'  => $age,
                'str' => $str,
                'num' => $num,
            ],
        ]);
    }

    public function sample7(): Response
    {
        $name = '鈴木 太郎';
        $age = 22;
        $str = '&amp;';
        $num = 0;

        return Inertia::render('Sample/Sample7', [
            'data' => [
                'name' => $name,
                'age'  => $age,
                'str' => $str,
                'num' => $num,
            ],
        ]);
    }

    public function sample8(): Response
    {
        $name = '鈴木 太郎';
        $age = 22;
        $str = '&amp;';

        return Inertia::render('Sample/Sample8', [
            'data' => [
                'name' => $name,
                'age'  => $age,
                'str' => $str,
            ],
        ]);
    }
}
