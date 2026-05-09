import Layout from '@/layouts/authenticated';
import { Head } from '@inertiajs/react';

// 関数コーポネントで、react自体もクラスコンポーネントではなく関数コンポーネントが主流
export default function Inertia({ data }) {
    return (
        <Layout>
            <p>Name: {data.name}</p>
            <p>Age: {data.age}</p>
        </Layout>
    )
}