import Layout from '@/layouts/authenticated';
import { Head } from '@inertiajs/react';

export default function Inertia({ data }) {
    return (
        <Layout>
            <p>Name: {data.name}</p>
            <p>Age: {data.age}</p>
        </Layout>
    )
}