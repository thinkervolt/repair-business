import React, { useEffect, useState } from 'react';
import { Link, useNavigate, useParams } from 'react-router-dom';
import { PackagePlus, Pencil, Trash2, Zap } from 'lucide-react';
import api from '../../api/client';
import { formatDate, formatMoney, getApiError } from '../../utils/format';
import Card, { CardHeader } from '../../components/ui/Card';
import Button from '../../components/ui/Button';
import Input from '../../components/ui/Input';
import Badge from '../../components/ui/Badge';
import Alert from '../../components/ui/Alert';
import Pagination from '../../components/ui/Pagination';
import { useAuth } from '../../auth/AuthContext';

function stockTone(stock, product) {
    const min = Number(product.min_stock);
    if (min > 0 && stock <= min) return 'red';
    if (min > 0 && stock <= min * 2) return 'amber';
    return 'emerald';
}

export default function ProductView() {
    const { id } = useParams();
    const navigate = useNavigate();
    const [data, setData] = useState(null);
    const [restock, setRestock] = useState({ purchase_price: '', quantity: '' });
    const [errors, setErrors] = useState({});
    const [message, setMessage] = useState('');
    const [messageTone, setMessageTone] = useState('success');
    const [page, setPage] = useState(1);
    const [working, setWorking] = useState(false);
    const { isAdmin } = useAuth();

    const load = () => {
        setMessage('');
        api.get(`/inventory/products/${id}`)
            .then(({ data }) => setData(data.data))
            .catch((err) => notify(getApiError(err), 'error'));
    };

    useEffect(() => {
        load();
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [id]);

    const notify = (text, tone = 'success') => {
        setMessage(text);
        setMessageTone(tone);
        window.scrollTo({ top: 0, behavior: 'smooth' });
    };

    const transactions = data ? data.transactions.data : [];
    const links = data ? data.transactions.links : [];

    const fetchTransactions = (p) => {
        api.get(`/inventory/products/${id}`, { params: { page: p } })
            .then(({ data }) => {
                setPage(p);
                setData((d) => ({ ...d, transactions: data.data.transactions }));
            })
            .catch((err) => notify(getApiError(err), 'error'));
    };

    const handleRestock = async (e) => {
        e.preventDefault();
        setErrors({});
        setWorking(true);
        try {
            const { data } = await api.post(`/inventory/products/${id}/restock`, restock);
            notify(data.message);
            setRestock({ purchase_price: '', quantity: '' });
            load();
        } catch (err) {
            const response = err.response;
            if (response && response.data && response.data.errors) {
                setErrors(response.data.errors);
            } else {
                notify(getApiError(err), 'error');
            }
        } finally {
            setWorking(false);
        }
    };

    const handleQuickSell = async () => {
        setWorking(true);
        try {
            const { data } = await api.post(`/inventory/products/${id}/quick-sell`);
            notify(data.message);
            load();
        } catch (err) {
            notify(getApiError(err), 'error');
        } finally {
            setWorking(false);
        }
    };

    const handleDeleteTransaction = async (transaction) => {
        if (!window.confirm('Delete this transaction?')) return;
        const task = transaction.repair_id ? 'repair' : transaction.invoice_id ? 'invoice' : null;
        try {
            if (task && (transaction.repair_id || transaction.invoice_id)) {
                const target = task === 'repair' ? transaction.repair_id : transaction.invoice_id;
                await api.delete(`/inventory/${task}/${target}/transactions/${transaction.id}`);
            } else {
                await api.delete(`/inventory/transactions/${transaction.id}`);
            }
            load();
        } catch (err) {
            notify(getApiError(err), 'error');
        }
    };

    const handleDeleteProduct = async () => {
        if (!window.confirm('Delete this product and all of its transactions?')) return;
        try {
            await api.delete(`/inventory/products/${id}`);
            navigate('/inventory/products');
        } catch (err) {
            notify(getApiError(err), 'error');
        }
    };

    if (!data) {
        return <div className="py-12 text-center text-sm text-slate-400">Loading...</div>;
    }

    const { product, stock, categories } = data;

    return (
        <div className="space-y-6">
            {message && <Alert tone={messageTone}>{message}</Alert>}

            <div className="flex flex-wrap items-start justify-between gap-3">
                <div>
                    <Link to="/inventory/products" className="text-sm font-medium text-blue-700 hover:underline">
                        &larr; Back to products
                    </Link>
                    <h1 className="mt-2 text-2xl font-bold tracking-tight text-slate-900">{product.name}</h1>
                    <p className="mt-1 text-sm text-slate-500">
                        {product.barcode ? `Barcode ${product.barcode} · ` : ''}
                        {categories.find((c) => c.id === Number(product.category_id))?.name || 'No category'}
                    </p>
                </div>
                <div className="flex flex-wrap gap-2">
                    <Button type="button" variant="secondary" onClick={handleQuickSell} loading={working}>
                        <Zap className="h-4 w-4" />
                        Quick sell
                    </Button>
                    <Link to={`/inventory/products/${id}/edit`}>
                        <Button type="button" variant="secondary">
                            <Pencil className="h-4 w-4" />
                            Edit
                        </Button>
                    </Link>
                    <Button type="button" variant="danger" onClick={handleDeleteProduct}>
                        <Trash2 className="h-4 w-4" />
                        Delete
                    </Button>
                </div>
            </div>

            <div className="grid gap-6 lg:grid-cols-3">
                <div className="space-y-6 lg:col-span-2">
                    <Card className="overflow-hidden">
                        <CardHeader title="Stock information" />
                        <div className="grid gap-6 p-6 sm:grid-cols-3">
                            <div>
                                <p className="text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    Current stock
                                </p>
                                <Badge solid tone={stockTone(stock, product)} className="mt-2 text-base">
                                    {stock}
                                </Badge>
                            </div>
                            <div>
                                <p className="text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    Selling price
                                </p>
                                <p className="mt-2 text-lg font-semibold text-slate-800">
                                    $ {formatMoney(product.selling_price)}
                                </p>
                            </div>
                            <div>
                                <p className="text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    Alert range
                                </p>
                                <p className="mt-2 text-sm text-slate-700">
                                    {product.min_stock != null ? product.min_stock : '—'} &mdash;{' '}
                                    {product.max_stock != null ? product.max_stock : '—'}
                                </p>
                                {product.email_alert === 'yes' && (
                                    <p className="mt-1 text-xs text-slate-400">Email alerts on</p>
                                )}
                            </div>
                        </div>
                    </Card>

                    <Card className="overflow-hidden">
                        <CardHeader title="Restock" subtitle="Add a purchase transaction to this product." />
                        <form onSubmit={handleRestock} className="grid gap-5 border-b border-slate-100 p-6 sm:grid-cols-2">
                            <div>
                                <label className="mb-1.5 block text-sm font-medium text-slate-700" htmlFor="rp">
                                    Purchase price ($) *
                                </label>
                                <Input
                                    id="rp"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    value={restock.purchase_price}
                                    onChange={(e) => setRestock((r) => ({ ...r, purchase_price: e.target.value }))}
                                    placeholder="0.00"
                                    invalid={!!errors.purchase_price}
                                />
                                {errors.purchase_price && (
                                    <p className="mt-1 text-xs text-red-600">{errors.purchase_price[0]}</p>
                                )}
                            </div>
                            <div>
                                <label className="mb-1.5 block text-sm font-medium text-slate-700" htmlFor="rq">
                                    Quantity *
                                </label>
                                <Input
                                    id="rq"
                                    type="number"
                                    min="1"
                                    value={restock.quantity}
                                    onChange={(e) => setRestock((r) => ({ ...r, quantity: e.target.value }))}
                                    placeholder="e.g. 5"
                                    invalid={!!errors.quantity}
                                />
                                {errors.quantity && (
                                    <p className="mt-1 text-xs text-red-600">{errors.quantity[0]}</p>
                                )}
                            </div>
                            <div className="sm:col-span-2">
                                <Button type="submit" loading={working}>
                                    <PackagePlus className="h-4 w-4" />
                                    Add to stock
                                </Button>
                            </div>
                        </form>
                    </Card>

                    <Card className="overflow-hidden">
                        <CardHeader title="Transactions" subtitle="Movement history for this product." />
                        {transactions.length === 0 ? (
                            <div className="px-6 py-8 text-center text-sm text-slate-400">
                                No transactions have been found.
                            </div>
                        ) : (
                            <>
                                <div className="overflow-x-auto">
                                    <table className="w-full text-left">
                                        <thead>
                                            <tr className="border-b border-slate-100 bg-slate-50/60 text-xs font-semibold uppercase tracking-wide text-slate-500">
                                                <th className="px-6 py-3">Type</th>
                                                <th className="px-6 py-3">Qty</th>
                                                <th className="px-6 py-3">Price</th>
                                                <th className="px-6 py-3">Source</th>
                                                <th className="px-6 py-3">Date</th>
                                                <th className="px-6 py-3 text-right">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {transactions.map((t) => (
                                                <tr
                                                    key={t.id}
                                                    className="border-b border-slate-100 last:border-0 hover:bg-slate-50"
                                                >
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
                                                            onClick={() => handleDeleteTransaction(t)}
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
                                <Pagination paginator={data.transactions} onChangePage={fetchTransactions} />
                            </>
                        )}
                    </Card>
                </div>

                <div className="space-y-4">
                    <Card className="p-5">
                        <h3 className="text-xs font-semibold uppercase tracking-wide text-slate-500">
                            Product info
                        </h3>
                        <dl className="mt-3 space-y-2 text-sm">
                            <div className="flex justify-between gap-3">
                                <dt className="text-slate-400">Supplier</dt>
                                <dd className="font-medium text-slate-700">{product.supplier || '—'}</dd>
                            </div>
                            <div className="flex justify-between gap-3">
                                <dt className="text-slate-400">Barcode</dt>
                                <dd className="font-mono text-xs text-slate-700">{product.barcode || '—'}</dd>
                            </div>
                            <div className="flex justify-between gap-3">
                                <dt className="text-slate-400">Email alert</dt>
                                <dd className="font-medium text-slate-700">
                                    {product.email_alert === 'yes' ? 'Yes' : 'No'}
                                </dd>
                            </div>
                            <div className="flex justify-between gap-3">
                                <dt className="text-slate-400">Created</dt>
                                <dd className="font-medium text-slate-700">{formatDate(product.created_at)}</dd>
                            </div>
                        </dl>
                    </Card>
                </div>
            </div>
        </div>
    );
}