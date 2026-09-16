import React from 'react';
import { Link } from 'react-router-dom';
import { ShieldCheck, Wrench, Receipt } from 'lucide-react';
import Logo from '../components/ui/Logo';

export default function AuthLayout({ title, subtitle, children, hideLoginLink = false }) {
    return (
        <div className="flex min-h-screen items-center justify-center bg-gradient-to-br from-slate-100 via-slate-50 to-blue-100/70 p-4">
            <div className="grid w-full max-w-4xl overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xl shadow-slate-200/60 md:grid-cols-2">
                <div className="hidden flex-col justify-between bg-slate-900 p-10 text-white md:flex">
                    <div>
                        <div className="flex items-center gap-2.5">
                            <Logo className="h-10 w-10 bg-blue-600" />
                            <span className="text-lg font-bold tracking-tight">RepairHero</span>
                        </div>
                        <h2 className="mt-12 text-3xl font-bold leading-tight tracking-tight">
                            Repairs, invoices and inventory in one place.
                        </h2>
                        <p className="mt-3 text-sm leading-relaxed text-slate-300">
                            Run your repair business faster — from drop-off to delivery, without
                            the paper trail.
                        </p>
                    </div>
                    <ul className="space-y-4 text-sm text-slate-300">
                        <li className="flex items-center gap-3">
                            <Wrench className="h-4.5 w-4.5 text-blue-400" /> Track repairs and
                            drop-off receipts
                        </li>
                        <li className="flex items-center gap-3">
                            <Receipt className="h-4.5 w-4.5 text-blue-400" /> Invoice, print
                            and email customers
                        </li>
                        <li className="flex items-center gap-3">
                            <ShieldCheck className="h-4.5 w-4.5 text-blue-400" /> Secure, verified
                            staff accounts
                        </li>
                    </ul>
                </div>
                <div className="p-8 sm:p-10">
                    <div className="mb-6 md:hidden">
                        <div className="flex items-center gap-2.5">
                            <Logo className="h-9 w-9" />
                            <span className="text-base font-bold tracking-tight text-slate-900">
                                RepairHero
                            </span>
                        </div>
                    </div>
                    <h1 className="text-2xl font-bold tracking-tight text-slate-900">{title}</h1>
                    {subtitle && <p className="mt-1.5 text-sm text-slate-500">{subtitle}</p>}
                    <div className="mt-7">{children}</div>
                    {!hideLoginLink && (
                        <p className="mt-6 text-center text-sm text-slate-500">
                            Already have an account?{' '}
                            <Link
                                to="/login"
                                className="font-semibold text-blue-600 hover:text-blue-700"
                            >
                                Sign in
                            </Link>
                        </p>
                    )}
                </div>
            </div>
        </div>
    );
}