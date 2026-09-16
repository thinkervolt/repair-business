import React from 'react';
import clsx from 'clsx';

export default function Card({ className, children, ...props }) {
    return (
        <div
            className={clsx('rounded-xl border border-slate-200 bg-white shadow-sm', className)}
            {...props}
        >
            {children}
        </div>
    );
}

export function CardHeader({ title, subtitle, actions, className }) {
    return (
        <div
            className={clsx(
                'flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 px-6 py-4',
                className
            )}
        >
            <div>
                <h3 className="text-sm font-semibold text-slate-900">{title}</h3>
                {subtitle && <p className="mt-0.5 text-xs text-slate-500">{subtitle}</p>}
            </div>
            {actions}
        </div>
    );
}

export function CardBody({ className, children }) {
    return <div className={clsx('p-6', className)}>{children}</div>;
}