import React from 'react';
import clsx from 'clsx';

const tones = {
    slate: 'bg-slate-100 text-slate-700',
    blue: 'bg-blue-50 text-blue-700',
    emerald: 'bg-emerald-50 text-emerald-700',
    amber: 'bg-amber-50 text-amber-700',
    red: 'bg-red-50 text-red-700',
    sky: 'bg-sky-50 text-sky-700',
};

const solidTones = {
    slate: 'bg-slate-600 text-white',
    blue: 'bg-blue-700 text-white',
    emerald: 'bg-emerald-600 text-white',
    amber: 'bg-amber-500 text-white',
    red: 'bg-red-600 text-white',
    sky: 'bg-sky-600 text-white',
};

export default function Badge({ tone = 'slate', solid = false, className, children }) {
    return (
        <span
            className={clsx(
                'inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-xs font-medium',
                solid ? solidTones[tone] : tones[tone],
                className
            )}
        >
            {children}
        </span>
    );
}