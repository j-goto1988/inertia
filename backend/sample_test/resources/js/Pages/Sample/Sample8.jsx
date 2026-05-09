import Layout from '@/layouts/authenticated';
import { Head } from '@inertiajs/react';
import { useState } from 'react';
import StateCounter from './StateCounter';

export default function Sample8({ data }) {
    const [count, setCount] = useState(0) // 第1引数はState値を格納する変数、第2引数はState値を更新するための関数
    console.log(`count:${count}`);

    const update = step => setCount(c => c + step);

    // Update関数をStateCounterコンポーネントに引き渡す
    // 親コンポーネントから自身のStateを更新するためのupdate関数を、子コンポーネントに引き渡している点
    // 子コンポーネントでは、任意のタイミングでupdate関数を呼び出し、親コンポーネントのStateを更新できる
    return (
        <Layout>
            <p>Name: {data.name}</p>
            <p>Age: {data.age}</p>
            <p>Str: {data.str}</p>
            <p>総カウント:{count}</p>
            <StateCounter step={1} onUpdate={update} />
            <StateCounter step={5} onUpdate={update} />
            <StateCounter step={-1} onUpdate={update} />
        </Layout>
    )
}