import Layout from '@/layouts/authenticated';
import { Head } from '@inertiajs/react';
import { useState } from 'react';

// Array#sortメソッドを利用することで、配列をソートする
// 実際はLaravel側で並び替えることが多い
export default function Sample4({ datas }) {
    const sorted = [...datas].sort((m, n) => m.age - n.age);
    return (
        <Layout>
            {sorted.map((data) => (
                <div key={data.id}>
                    <p>{data.name},{data.age}歳</p>
                </div>
            ))}
        </Layout>
    )
}