import React from 'react';
import clsx from 'clsx';
import { CircleCheck, CircleX, Info, TriangleAlert } from 'lucide-react';

const styles = {
    success: {
        wrap: 'border-emerald-200 bg-emerald-50 text-emerald-800',
        icon: <CircleCheck className="h-4 w-4 text-emerald-500" />,
    },
    error: {
        wrap: 'border-red-200 bg-red-50 text-red-800',
        icon: <CircleX className="h-4 w-4 text-red-500" />,
    },
    info: {
        wrap: 'border-sky-200 bg-sky-50 text-sky-800',
        icon: <Info className="h-4 w-4 text-sky-500" />,
    },
    warning: {
        wrap: 'border-amber-200 bg-amber-50 text-amber-800',
        icon: <TriangleAlert className="h-4 w-4 text-amber-500" />,
    },
};

export default function Alert({ tone = 'info', className, children }) {
    const style = styles[tone];
    return (
        <div
            role="alert"
            className={clsx('flex items-start gap-2.5 rounded-lg border px-4 py-3 text-sm', style.wrap, className)}
        >
            {style.icon}
            <div className="flex-1">{children}</div>
        </div>
    );
}