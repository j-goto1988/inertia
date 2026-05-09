import Layout from '@/layouts/authenticated';
import { Head } from '@inertiajs/react';
import { useState } from 'react';

export default function Sample6({ data }) {
    const [count, setCount] = useState(data.num) // 第1引数はState値を格納する変数、第2引数はState値を更新するための関数
    console.log(`count:${count}`);
    const handleClick = () => {
        // この書き方では1回クリックしただけでは、1が増えるだけ
        // 複数のState更新がある場合、まとめて後から更新されるため
        setCount(count + 1); // ここを2に変更しても、1が増える
        setCount(count + 1); // ここを2に変更すると、2が増える
    };

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
            }} onClick={handleClick}>
                カウント
            </button>
            <p>{count}回、クリックされました。</p>
        </Layout>
    )
}