import React, { useEffect, useRef, useState } from 'react';
import { Link } from 'react-router-dom';
import { Search, Trash2 } from 'lucide-react';
import api from '../../api/client';
import { formatDate, formatMoney, getApiError } from '../../utils/format';
import Card from '../../components/ui/Card';
import Input from '../../components/ui/Input';
import Badge from '../../components/ui/Badge';
import Alert from '../../components/ui/Alert';
import Pagination from '../../components/ui/Pagination';

export default function TransactionsList() {
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

    const handleDelete = async (t) => {
        if (!window.confirm('Delete this transaction?')) return;
        const task = t.repair_id ? 'repair' : t.invoice_id ? 'invoice' : null;
        try {
            if (task && (t.repair_id || t.invoice_id)) {
                const target = task === 'repair' ? t.repair_id : t.invoice_id;
                await api.delete(`/inventory/${task}/${target}/transactions/${t.id}`);
            } else {
                await api.delete(`/inventory/transactions/${t.id}`);
            }
            load();
        } catch (err) {
            setError(getApiError(err));
        }
    };

    return (
        <div className="space-y-6">
            <div>
                <h1 className="text-2xl font-bold tracking-tight text-slate-900">Transactions</h1>
                <p className="mt-1 text-sm text-slate-500">
                    Purchases, restocks and sales across your inventory.
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
                            placeholder="Search product, supplier, type, invoice..."
                            className="pl-9"
                        />
                    </div>
                </div>

                {!data ? (
                    <div className="px-6 py-12 text-center text-sm text-slate-400">Loading...</div>
                ) : data.data.length > 0 ? (
                    <>
                        <div className="overflow-x-auto">
                            <table className="w-full text-left">
                                <thead>
                                    <tr className="border-b border-slate-100 bg-slate-50/60 text-xs font-semibold uppercase tracking-wide text-slate-500">
                                        <th className="px-6 py-3">Product</th>
                                        <th className="px-6 py-3">Type</th>
                                        <th className="px-6 py-3">Qty</th>
                                        <th className="px-6 py-3">Price</th>
                                        <th className="px-6 py-3">Source</th>
                                        <th className="px-6 py-3">Date</th>
                                        <th className="px-6 py-3 text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {data.data.map((t) => (
                                        <tr
                                            key={t.id}
                                            className="border-b border-slate-100 transition-colors last:border-0 hover:bg-slate-50"
                                        >
                                            <td className="px-6 py-3">
                                                {t.product ? (
                                                    <Link
                                                        to={`/inventory/products/${t.product_id}`}
                                                        className="text-sm font-medium text-slate-800 hover:text-blue-700"
                                                    >
                                                        {t.product.name}
                                                    </Link>
                                                ) : (
                                                    <span className="text-sm text-slate-600">#{t.product_id}</span>
                                                )}
                                            </td>
                                            <td className="px-6 py-3">
                                                <Badge tone={t.transaction === 'purchase' ? 'emerald' : 'red'}>
                                                    {t.transaction === 'purchase' ? 'Purchase' : 'Sell'}
                                                </Badge>
                                            </td>
                                            <td className="px-6 py-3 text-sm text-slate-600">{t.quantity}</td>
                                            <td className="px-6 py-3 text-sm text-slate-600">
                                                {t.transaction === 'purchase'
                                                    ? t.purchase_price != null
                                                        ? `$ ${formatMoney(t.purchase_price)}`
                                                        : '—'
                                                    : t.selling_price != null
                                                      ? `$ ${formatMoney(t.selling_price)}`
                                                      : '—'}
                                            </td>
                                            <td className="px-6 py-3 text-sm text-slate-600">
                                                {t.repair_id
                                                    ? `Repair #${t.repair_id}`
                                                    : t.invoice_id
                                                      ? `Invoice #${t.invoice_id}`
                                                      : '—'}
                                            </td>
                                            <td className="px-6 py-3 text-sm text-slate-600">
                                                {formatDate(t.created_at)}
                                            </td>
                                            <td className="px-6 py-3 text-right">
                                                <button
                                                    type="button"
                                                    onClick={() => handleDelete(t)}
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
                        Nothing has been found.
                    </div>
                )}
            </Card>
        </div>
    );
}