import Layout from '@/layouts/authenticated';
import { Head } from '@inertiajs/react';
import { useState } from 'react';

export default function StateCounter({ step, onUpdate }) {
    // ボタンクリックで親State(count)にstep値だけ加算
    const handleClick = () => onUpdate(step);

    return (
         <button style={{
            padding: 12,
            border: '2px solid black',
            background: 'white',
            position: 'relative',
            zIndex: 9999,
        }} onClick={handleClick}>
            <span>{step}</span>
        </button>
    )
}