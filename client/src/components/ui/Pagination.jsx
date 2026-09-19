import React from 'react';
import { ChevronLeft, ChevronRight } from 'lucide-react';
import clsx from 'clsx';
import { useI18n } from '../../i18n/I18nContext';

export default function Pagination({ paginator, onChangePage }) {
    if (!paginator || paginator.last_page <= 1) return null;
    const { t } = useI18n();

    const links = paginator.links || [];

    return (
        <nav className="flex items-center justify-between gap-3 border-t border-slate-100 px-6 py-4">
            <p className="text-xs text-slate-400">
                {t('pagination.page_of', {
                    current: paginator.current_page,
                    last: paginator.last_page,
                    total: paginator.total,
                })}
            </p>
            <div className="flex items-center gap-1">
                {links.map((link, i) => {
                    const label = typeof link.label === 'string' ? link.label : String(link.label);
                    const isPrevNext = label.includes('Previous') || label.includes('Next');
                    const isEllipsis = !link.url;

                    if (isPrevNext) {
                        const Icon = label.includes('Previous') ? ChevronLeft : ChevronRight;
                        return (
                            <button
                                key={i}
                                type="button"
                                disabled={!link.url}
                                onClick={() =>
                                    link.url &&
                                    onChangePage(
                                        Number(new URL(link.url).searchParams.get('page')) || 1
                                    )
                                }
                                className="flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 text-slate-500 transition-colors hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-40"
                                aria-label={label.trim()}
                            >
                                <Icon className="h-4 w-4" />
                            </button>
                        );
                    }

                    const isPage = !isEllipsis && /^\d+$/.test(label);
                    const page = isPage ? Number(label) : NaN2; // prettier-ignore

                    return (
                        <button
                            key={i}
                            type="button"
                            disabled={!isPage}
                            onClick={() => isPage && onChangePage(page)}
                            className={clsx(
                                'h-8 min-w-8 rounded-lg px-2 text-sm font-medium transition-colors',
                                link.active
                                    ? 'bg-blue-700 text-white'
                                    : 'text-slate-600 hover:bg-slate-100',
                                !isPage && 'cursor-default text-slate-400'
                            )}
                        >
                            {label}
                        </button>
                    );
                })}
            </div>
        </nav>
    );
}
