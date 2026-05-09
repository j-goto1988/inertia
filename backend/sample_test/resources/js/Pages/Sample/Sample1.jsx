import Layout from '@/layouts/authenticated';
import { Head } from '@inertiajs/react';
import { useState } from 'react';

// 関数コーポネントで、react自体もクラスコンポーネントではなく関数コンポーネントが主流
// Reactで再描画されるのは、下記の3つのタイミング
// Stateが更新された場合
// 渡されたPropsが変更された場合
// 親コンポーネントが再描画された場合
export default function Sample1({ data }) {
    const [count, setCount] = useState(0) // 第1引数はState値を格納する変数、第2引数はState値を更新するための関数
    console.log(`count:${count}`);
    return (
        <Layout>
            <p>Name: {data.name}</p>
            <p>Age: {data.age}</p>
            <p>Str: {data.str}</p>
            <button style={{
                padding: 12,
                border: '2px solid black',
                background: 'white',
                position: 'relative',
                zIndex: 9999,
            }} onClick={() => setCount(count + 1)}>
                カウント
            </button>
            <p>{count}回、クリックされました。</p>
        </Layout>
    )
}