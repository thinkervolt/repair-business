import React from 'react';
import { Link, useLocation } from 'react-router-dom';
import clsx from 'clsx';
import { useAuth } from '../../auth/AuthContext';
import Logo from '../ui/Logo';
import { NAV_SECTIONS } from './nav';

function NavList({ sections, collapsed, onNavigate }) {
    const location = useLocation();

    return (
        <nav
            className={
                collapsed
                    ? 'flex flex-1 flex-col items-center gap-1 px-2 py-4'
                    : 'flex-1 space-y-6 overflow-y-auto scrollbar-thin px-3 py-5'
            }
        >
            {sections.map((section, i) => (
                <div key={section.section || i} className={collapsed ? 'flex flex-col items-center gap-1' : ''}>
                    {!collapsed && section.section && (
                        <p className="px-3 pb-2 text-[11px] font-semibold uppercase tracking-wider text-slate-400">
                            {section.section}
                        </p>
                    )}
                    {collapsed && section.section && (
                        <div className="mb-1 h-px w-8 bg-slate-200" />
                    )}
                    <ul className={collapsed ? 'flex flex-col items-center gap-1' : 'space-y-1'}>
                        {section.items.map((item) => {
                            const active = location.pathname === item.to;
                            if (collapsed) {
                                return (
                                    <li key={item.to}>
                                        <Link
                                            to={item.to}
                                            title={item.label}
                                            onClick={onNavigate}
                                            className={clsx(
                                                'flex h-10 w-10 items-center justify-center rounded-lg transition-colors',
                                                active
                                                    ? 'bg-blue-50 text-blue-600'
                                                    : 'text-slate-500 hover:bg-slate-100 hover:text-slate-800'
                                            )}
                                        >
                                            <item.icon className="h-5 w-5" />
                                        </Link>
                                    </li>
                                );
                            }
                            return (
                                <li key={item.to}>
                                    <Link
                                        to={item.to}
                                        onClick={onNavigate}
                                        className={clsx(
                                            'group flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors',
                                            active
                                                ? 'bg-blue-50 text-blue-700'
                                                : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900'
                                        )}
                                    >
                                        <item.icon
                                            className={clsx(
                                                'h-[18px] w-[18px] shrink-0',
                                                active
                                                    ? 'text-blue-600'
                                                    : 'text-slate-400 group-hover:text-slate-600'
                                            )}
                                        />
                                        {item.label}
                                    </Link>
                                </li>
                            );
                        })}
                    </ul>
                </div>
            ))}
        </nav>
    );
}

export default function Sidebar({ collapsed, mobileOpen, onCloseMobile }) {
    const { isAdmin } = useAuth();
    const sections = NAV_SECTIONS.filter((s) => !s.adminOnly || isAdmin);

    return (
        <>
            {mobileOpen && (
                <div
                    className="fixed inset-0 z-40 bg-slate-900/40 lg:hidden"
                    onClick={onCloseMobile}
                    aria-hidden="true"
                />
            )}
            <aside
                className={clsx(
                    'fixed inset-y-0 left-0 z-50 flex flex-col border-r border-slate-200 bg-white',
                    'transition-all duration-200 lg:translate-x-0',
                    collapsed ? 'w-64 lg:w-[72px]' : 'w-64',
                    mobileOpen ? 'translate-x-0' : '-translate-x-full'
                )}
            >
                <div
                    className={clsx(
                        'flex h-16 shrink-0 items-center gap-2.5 border-b border-slate-100 px-4',
                        collapsed && 'lg:justify-center lg:px-0'
                    )}
                >
                    <Logo className="h-9 w-9 shrink-0" />
                    <span
                        className={clsx(
                            'text-[15px] font-bold tracking-tight text-slate-900',
                            collapsed && 'lg:hidden'
                        )}
                    >
                        RepairHero
                    </span>
                </div>
                <NavList sections={sections} collapsed={collapsed} onNavigate={onCloseMobile} />
            </aside>
        </>
    );
}