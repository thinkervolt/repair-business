import React, { createContext, useEffect, useRef, useState } from 'react';
import { Link } from 'react-router-dom';
import clsx from 'clsx';

const DropdownContext = createContext({ close: () => {} });

export default function Dropdown({ trigger, align = 'right', className, children, id }) {
    const [open, setOpen] = useState(false);
    const ref = useRef(null);

    useEffect(() => {
        function onKey(e) {
            if (e.key === 'Escape') {
                setOpen(false);
            }
        }
        document.addEventListener('keydown', onKey);
        return () => document.removeEventListener('keydown', onKey);
    }, []);

    return (
        <div className={clsx('relative', className)} ref={ref}>
            <button
                type="button"
                id={id}
                onClick={() => setOpen((o) => !o)}
                aria-haspopup="true"
                aria-expanded={open}
                className="cursor-pointer"
            >
                {trigger}
            </button>
            {open && (
                <>
                    <div
                        className="fixed inset-0 z-40"
                        onClick={() => setOpen(false)}
                        aria-hidden="true"
                    />
                    <div
                        className={clsx(
                            'absolute z-50 mt-2 min-w-56 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-lg',
                            align === 'right' ? 'right-0' : 'left-0'
                        )}
                        onClick={() => setOpen(false)}
                    >
                        <DropdownContext.Provider value={{ close: () => setOpen(false) }}>
                            {children}
                        </DropdownContext.Provider>
                    </div>
                </>
            )}
        </div>
    );
}

export function DropdownItem({ to, onClick, icon: Icon, children, danger = false, className }) {
    const itemClass = clsx(
        'flex w-full items-center gap-2.5 px-4 py-2 text-left text-sm transition-colors',
        danger
            ? 'text-red-600 hover:bg-red-50'
            : 'text-slate-700 hover:bg-slate-50',
        className
    );

    const content = (
        <>
            {Icon && <Icon className="h-4 w-4" />}
            {children}
        </>
    );

    if (to) {
        return (
            <Link to={to} className={itemClass}>
                {content}
            </Link>
        );
    }

    return (
        <button type="button" onClick={onClick} className={itemClass}>
            {content}
        </button>
    );
}

export function DropdownLabel({ children }) {
    return <div className="px-4 py-2.5 text-xs font-semibold uppercase tracking-wide text-slate-400">{children}</div>;
}

export function DropdownSeparator() {
    return <div className="my-1 h-px bg-slate-100" />;
}