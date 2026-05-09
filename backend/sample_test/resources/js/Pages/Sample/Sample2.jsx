import Layout from '@/layouts/authenticated';
import { Head } from '@inertiajs/react';
import { useState } from 'react';

// 配列をループする場合はkeyがないと、下記のエラーが出るので要注意
// Each child in a list should have a unique "key" prop.
// 下記のように配列のインデックスでも動くが、追加・削除・並び替えのいずれかがある場合はバグるので要注意
export default function Sample2({ datas }) {
    return (
        <Layout>
            {datas.map((data, index) => (
                <div key={index}>
                    <p>{data.name},{data.age}歳</p>
                </div>
            ))}
        </Layout>
    )
}