import React, { useEffect, useRef, useState } from 'react';
import { Link } from 'react-router-dom';
import { Search, Trash2 } from 'lucide-react';
import api from '../../api/client';
import { useI18n } from '../../i18n/I18nContext';
import { formatDate, formatMoney, getApiError } from '../../utils/format';
import Card from '../../components/ui/Card';
import Input from '../../components/ui/Input';
import Badge from '../../components/ui/Badge';
import Alert from '../../components/ui/Alert';
import Pagination from '../../components/ui/Pagination';
import { useConfirm } from '../../components/ui/ConfirmAlert';

export default function TransactionsList() {
    const { t } = useI18n();
    const { confirm, confirmElement } = useConfirm();
    const [data, setData] = useState(null);
    const [search, setSearch] = useState('');
    const [debounced, setDebounced] = useState('');
    const [page, setPage] = useState(1);
    const [error, setError] = useState('');
    const timer = useRef(null);

    useEffect(() => {
        clearTimeout(timer.current);
        timer.current = setTimeout(() => {
            setDebounced(search);
            setPage(1);
        }, 350);
        return () => clearTimeout(timer.current);
    }, [search]);

    const load = () => {
        api.get('/inventory/transactions', { params: { search: debounced, page } })
            .then(({ data }) => setData(data.data))
            .catch((err) => setError(getApiError(err)));
    };

    useEffect(() => {
        load();
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [debounced, page]);

    const handleDelete = async (tx) => {
        const ok = await confirm({
            title: t('common.confirm_title'),
            message: t('inventory.delete_transaction_confirm'),
            confirmLabel: t('common.delete'),
            cancelLabel: t('common.cancel'),
        });
        if (!ok) return;
        const task = tx.repair_id ? 'repair' : tx.invoice_id ? 'invoice' : null;
        try {
            if (task && (tx.repair_id || tx.invoice_id)) {
                const target = task === 'repair' ? tx.repair_id : tx.invoice_id;
                await api.delete(`/inventory/${task}/${target}/transactions/${tx.id}`);
            } else {
                await api.delete(`/inventory/transactions/${tx.id}`);
            }
            load();
        } catch (err) {
            setError(getApiError(err));
        }
    };

    return (
        <div className="space-y-6">
            {confirmElement}
            <div>
                <h1 className="text-2xl font-bold tracking-tight text-slate-900">{t('inventory.transactions')}</h1>
                <p className="mt-1 text-sm text-slate-500">
                    {t('inventory.transactions.subtitle')}
                </p>
            </div>

            {error && <Alert tone="error">{error}</Alert>}

            <Card className="overflow-hidden">
                <div className="border-b border-slate-100 p-4 sm:p-5">
                    <div className="relative max-w-sm">
                        <Search className="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                        <Input
                            type="search"
                            value={search}
                            onChange={(e) => setSearch(e.target.value)}
                            placeholder={t('inventory.transactions.search_placeholder')}
                            className="pl-9"
                        />
                    </div>
                </div>

                {!data ? (
                    <div className="px-6 py-12 text-center text-sm text-slate-400">{t('common.loading')}</div>
                ) : data.data.length > 0 ? (
                    <>
                        <div className="overflow-x-auto">
                            <table className="w-full text-left">
                                <thead>
                                    <tr className="border-b border-slate-100 bg-slate-50/60 text-xs font-semibold uppercase tracking-wide text-slate-500">
                                        <th className="px-6 py-3">{t('inventory.product')}</th>
                                        <th className="px-6 py-3">{t('inventory.type')}</th>
                                        <th className="px-6 py-3">{t('common.qty')}</th>
                                        <th className="px-6 py-3">{t('common.price')}</th>
                                        <th className="px-6 py-3">{t('inventory.source')}</th>
                                        <th className="px-6 py-3">{t('common.date')}</th>
                                        <th className="px-6 py-3 text-right">{t('common.actions')}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {data.data.map((tx) => (
                                        <tr
                                            key={tx.id}
                                            className="border-b border-slate-100 transition-colors last:border-0 hover:bg-slate-50"
                                        >
                                            <td className="px-6 py-3">
                                                {tx.product ? (
                                                    <Link
                                                        to={`/inventory/products/${tx.product_id}`}
                                                        className="text-sm font-medium text-slate-800 hover:text-blue-700"
                                                    >
                                                        {tx.product.name}
                                                    </Link>
                                                ) : (
                                                    <span className="text-sm text-slate-600">#{tx.product_id}</span>
                                                )}
                                            </td>
                                            <td className="px-6 py-3">
                                                <Badge tone={tx.transaction === 'purchase' ? 'emerald' : 'red'}>
                                                    {tx.transaction === 'purchase' ? t('inventory.purchase') : t('inventory.sell')}
                                                </Badge>
                                            </td>
                                            <td className="px-6 py-3 text-sm text-slate-600">{tx.quantity}</td>
                                            <td className="px-6 py-3 text-sm text-slate-600">
                                                {tx.transaction === 'purchase'
                                                    ? tx.purchase_price != null
                                                        ? `$ ${formatMoney(tx.purchase_price)}`
                                                        : '—'
                                                    : tx.selling_price != null
                                                      ? `$ ${formatMoney(tx.selling_price)}`
                                                      : '—'}
                                            </td>
                                            <td className="px-6 py-3 text-sm text-slate-600">
                                                {tx.repair_id
                                                    ? t('inventory.repair_source', { id: tx.repair_id })
                                                    : tx.invoice_id
                                                      ? t('inventory.invoice_source', { id: tx.invoice_id })
                                                      : '—'}
                                            </td>
                                            <td className="px-6 py-3 text-sm text-slate-600">
                                                {formatDate(tx.created_at)}
                                            </td>
                                            <td className="px-6 py-3 text-right">
                                                <button
                                                    type="button"
                                                    onClick={() => handleDelete(tx)}
                                                    className="rounded-lg p-1.5 text-slate-400 transition-colors hover:bg-red-50 hover:text-red-600"
                                                    aria-label="Delete transaction"
                                                >
                                                    <Trash2 className="h-4 w-4" />
                                                </button>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                        <Pagination paginator={data} onChangePage={setPage} />
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