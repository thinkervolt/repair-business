import React from 'react';
import clsx from 'clsx';
import Spinner from './Spinner';

const variants = {
    primary: 'bg-blue-700 text-white hover:bg-blue-800 focus-visible:outline-blue-700',
    secondary: 'bg-white text-slate-700 border border-slate-200 hover:bg-slate-50 focus-visible:outline-slate-400',
    danger: 'bg-red-600 text-white hover:bg-red-700 focus-visible:outline-red-600',
    ghost: 'text-slate-600 hover:bg-slate-100 focus-visible:outline-slate-400',
};

const sizes = {
    sm: 'h-8 px-3 text-xs',
    md: 'h-9 px-4 text-sm',
    lg: 'h-10 px-5 text-sm',
};

const Button = React.forwardRef(function Button(
    { variant = 'primary', size = 'md', loading = false, className, children, disabled, ...props },
    ref
) {
    return (
        <button
            ref={ref}
            disabled={disabled || loading}
            className={clsx(
                'inline-flex items-center justify-center gap-2 rounded-lg font-medium',
                'transition-colors focus-visible:outline-2 focus-visible:outline-offset-2',
                'disabled:cursor-not-allowed disabled:opacity-60',
                variants[variant],
                sizes[size],
                className
            )}
            {...props}
        >
            {loading && <Spinner className="h-3.5 w-3.5" />}
            {children}
        </button>
    );
});

export default Button;