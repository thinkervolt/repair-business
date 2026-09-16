import React from 'react';

export default function Footer() {
    return (
        <footer className="border-t border-slate-200 bg-white px-6 py-5">
            <p className="text-center text-xs text-slate-400">
                Copyright &copy; RepairHero 2019-{new Date().getFullYear()}
            </p>
        </footer>
    );
}