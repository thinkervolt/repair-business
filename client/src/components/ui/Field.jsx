import React from 'react';

export default function Field({ label, hint, error, children }) {
    return (
        <div>
            {label && (
                <label className="mb-1.5 block text-sm font-medium text-slate-700">{label}</label>
            )}
            {children}
            {error ? (
                <p className="mt-1 text-xs text-red-600">{error}</p>
            ) : (
                hint && <p className="mt-1 text-xs text-slate-400">{hint}</p>
            )}
        </div>
    );
}