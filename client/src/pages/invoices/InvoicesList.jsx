import React, { useEffect, useRef, useState } from 'react';
import { Link, useSearchParams } from 'react-router-dom';
import { CirclePlus, Search } from 'lucide-react';
import clsx from 'clsx';
import api from '../../api/client';
import { useI18n } from '../../i18n/I18nContext';
import { formatDate, formatMoney, getApiError } from '../../utils/format';
import { solidToneFor, toneFor } from '../../utils/colors';
import Card from '../../components/ui/Card';
import Button from '../../components/ui/Button';
import Input from '../../components/ui/Input';
import Badge from '../../components/ui/Badge';
import Alert from '../../components/ui/Alert';
import Pagination from '../../components/ui/Pagination';

export default function InvoicesList() {
    const { t } = useI18n();
    const [searchParams, setSearchParams] = useSearchParams();
    const [data, setData] = useState(null);
    const [search, setSearch] = useState('');
    const [debounced, setDebounced] = useState('');
    const [page, setPage] = useState(1);
    const [error, setError] = useState('');
    const timer = useRef(null);

    const filter = searchParams.get('filter') || '';

    useEffect(() => {
        clearTimeout(timer.current);
        timer.current = setTimeout(() => {
            setDebounced(search);
            setPage(1);
        }, 350);
        return () => clearTimeout(timer.current);
    }, [search]);

    useEffect(() => {
        const params = { search: debounced, page };
        if (filter === 'unpaid') {
            params.task = 'unpaid';
        } else if (filter) {
            params.task = 'group_by_status';
            params.status = filter;
        }
        api.get('/invoices', { params })
            .then(({ data }) => setData(data.data))
            .catch((err) => setError(getApiError(err)));
    }, [debounced, page, filter]);

    const selectFilter = (value) => {
        setPage(1);
        if (value) {
            setSearchParams({ filter: value });
        } else {
            setSearchParams({});
        }
    };

    const balanceTone = (balance) => {
        if (balance < 0) return 'text-emerald-600';
        if (balance > 0) return 'text-red-600';
        return 'text-slate-700';
    };

    return (
        <div className="space-y-6">
            <div className="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h1 className="text-2xl font-bold tracking-tight text-slate-900">{t('nav.invoices')}</h1>
                    <p className="mt-1 text-sm text-slate-500">
                        {t('invoices.subtitle')}
                    </p>
                </div>
                <Link to="/invoices/create">
                    <Button>
                        <CirclePlus className="h-4 w-4" />
                        {t('invoices.new')}
                    </Button>
                </Link>
            </div>

            {error && <Alert tone="error">{error}</Alert>}

            <Card className="overflow-hidden">
                <div className="border-b border-slate-100 p-4 sm:p-5">
                    <div className="flex flex-wrap items-center gap-2">
                        <button
                            type="button"
                            onClick={() => selectFilter('')}
                            className={`rounded-full px-3 py-1 text-xs font-medium transition-colors ${
                                !filter
                                    ? 'bg-blue-700 text-white'
                                    : 'bg-slate-100 text-slate-600 hover:bg-slate-200'
                            }`}
                        >
                            {t('common.all')}
                        </button>
                        <button
                            type="button"
                            onClick={() => selectFilter('unpaid')}
                            className={`rounded-full px-3 py-1 text-xs font-medium transition-colors ${
                                filter === 'unpaid'
                                    ? 'bg-red-600 text-white'
                                    : 'bg-slate-100 text-slate-600 hover:bg-slate-200'
                            }`}
                        >
                            {t('invoices.unpaid')}
                        </button>
                        {data &&
                            data.statuses.map((setting) => (
                                <button
                                    key={setting.id}
                                    type="button"
                                    onClick={() =>
                                        selectFilter(String(setting.id) === filter ? '' : String(setting.id))
                                    }
                                    className={clsx(
                                        'rounded-full px-3 py-1 text-xs font-medium text-white transition-opacity',
                                        solidToneFor(setting.color),
                                        String(setting.id) === filter
                                            ? 'ring-2 ring-blue-700 ring-offset-1'
                                            : 'opacity-60 hover:opacity-100'
                                    )}
                                >
                                    {setting.name}
                                </button>
                            ))}
                    </div>
                </div>

                <div className="border-b border-slate-100 p-4 sm:p-5">
                    <div className="relative max-w-sm">
                        <Search className="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                        <Input
                            type="search"
                            value={search}
                            onChange={(e) => setSearch(e.target.value)}
                            placeholder={t('invoices.search_placeholder')}
                            className="pl-9"
                        />
                    </div>
                </div>

                {!data ? (
                    <div className="px-6 py-12 text-center text-sm text-slate-400">{t('common.loading')}</div>
                ) : data.invoices.data.length > 0 ? (
                    <>
                        <div className="overflow-x-auto">
                            <table className="w-full text-left">
                                <thead>
                                    <tr className="border-b border-slate-100 bg-slate-50/60 text-xs font-semibold uppercase tracking-wide text-slate-500">
                                        <th className="px-6 py-3">{t('common.id')}</th>
                                        <th className="px-6 py-3">{t('common.customer')}</th>
                                        <th className="px-6 py-3">{t('common.status')}</th>
                                        <th className="px-6 py-3 text-right">{t('common.total')}</th>
                                        <th className="px-6 py-3 text-right">{t('common.balance')}</th>
                                        <th className="px-6 py-3">{t('common.date')}</th>
                                        <th className="px-6 py-3 text-right">{t('common.actions')}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {data.invoices.data.map((invoice) => (
                                        <tr
                                            key={invoice.id}
                                            className="border-b border-slate-100 transition-colors last:border-0 hover:bg-slate-50"
                                        >
                                            <td className="px-6 py-3 text-sm font-medium text-slate-700">
                                                #{invoice.id}
                                            </td>
                                            <td className="px-6 py-3">
                                                <span className="block text-sm font-medium text-slate-700">
                                                    {invoice.customer_name || '—'}
                                                </span>
                                                {invoice.customer_company && (
                                                    <span className="block text-xs text-slate-400">
                                                        {invoice.customer_company}
                                                    </span>
                                                )}
                                            </td>
                                            <td className="px-6 py-3">
                                                {invoice.status_data && (
                                                    <Badge solid tone={toneFor(invoice.status_data.color)}>
                                                        {invoice.status_data.name}
                                                    </Badge>
                                                )}
                                            </td>
                                            <td className="px-6 py-3 text-right text-sm text-slate-700">
                                                $ {formatMoney(invoice.total)}
                                            </td>
                                            <td
                                                className={`px-6 py-3 text-right text-sm font-medium ${balanceTone(
                                                    invoice.balance
                                                )}`}
                                            >
                                                $ {formatMoney(invoice.balance)}
                                            </td>
                                            <td className="px-6 py-3 text-sm text-slate-600">
                                                {formatDate(invoice.created_at)}
                                            </td>
                                            <td className="px-6 py-3 text-right">
                                                <Link
                                                    to={`/invoices/${invoice.id}`}
                                                    className="text-sm font-medium text-blue-700 hover:underline"
                                                >
                                                    {t('common.view')}
                                                </Link>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                        <Pagination paginator={data.invoices} onChangePage={setPage} />
                    </>
                ) : (
                    <div className="px-6 py-12 text-center text-sm text-slate-400">
                        {t('common.nothing_found')}
                    </div>
                )}
            </Card>
        </div>
    );
}