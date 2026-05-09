import Layout from '@/layouts/authenticated';
import { Head } from '@inertiajs/react';
import { useState } from 'react';

// Array#filterメソッドを利用することで、任意の条件に基づいて既存の配列を絞り込む
// 実際はLaravel側で絞り込むことが多い
export default function Sample3({ datas }) {
    const lowAge = datas.filter(data => data.age <= 25);
    return (
        <Layout>
            {lowAge.map((data) => (
                <div key={data.id}>
                    <p>{data.name},{data.age}歳</p>
                </div>
            ))}
        </Layout>
    )
}