import React from 'react';
import clsx from 'clsx';

const Input = React.forwardRef(function Input({ className, invalid = false, ...props }, ref) {
    return (
        <input
            ref={ref}
            className={clsx(
                'block w-full rounded-lg border bg-white px-3 py-2 text-sm text-slate-800',
                'placeholder:text-slate-400 shadow-sm outline-none transition',
                invalid
                    ? 'border-red-400 focus:border-red-500 focus:ring-2 focus:ring-red-500/20'
                    : 'border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20',
                className
            )}
            {...props}
        />
    );
});

export default Input;