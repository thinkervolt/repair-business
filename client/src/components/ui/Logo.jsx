import React from 'react';
import { Wrench } from 'lucide-react';
import clsx from 'clsx';

export default function Logo({ className }) {
    return (
        <span
            className={clsx(
                'inline-flex items-center justify-center rounded-xl bg-blue-600 text-white shadow-sm',
                className
            )}
        >
            <Wrench className="h-5 w-5" strokeWidth={2.5} />
        </span>
    );
}