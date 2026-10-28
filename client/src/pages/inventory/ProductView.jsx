import React, { useEffect, useState } from 'react';
import { Link, useNavigate, useParams } from 'react-router-dom';
import { PackagePlus, Pencil, Trash2, Zap } from 'lucide-react';
import api from '../../api/client';
import { useI18n } from '../../i18n/I18nContext';
import { formatDate, formatMoney, getApiError } from '../../utils/format';
import Card, { CardHeader } from '../../components/ui/Card';
import Button from '../../components/ui/Button';
import Input from '../../components/ui/Input';
import Badge from '../../components/ui/Badge';
import Alert from '../../components/ui/Alert';
import Pagination from '../../components/ui/Pagination';
import { useAuth } from '../../auth/AuthContext';
import { useConfirm } from '../../components/ui/ConfirmAlert';

function stockTone(stock, product) {
    const min = Number(product.min_stock);
    if (min > 0 && stock <= min) return 'red';
    if (min > 0 && stock <= min * 2) return 'amber';
    return 'emerald';
}

export default function ProductView() {
    const { id } = useParams();
    const navigate = useNavigate();
    const { t } = useI18n();
    const { confirm, confirmElement } = useConfirm();
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
        const ok = await confirm({
            title: t('common.confirm_title'),
            message: t('inventory.delete_transaction_confirm'),
            confirmLabel: t('common.delete'),
            cancelLabel: t('common.cancel'),
        });
        if (!ok) return;
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
        const ok = await confirm({
            title: t('common.confirm_title'),
            message: t('inventory.delete_product_confirm'),
            confirmLabel: t('common.delete'),
            cancelLabel: t('common.cancel'),
        });
        if (!ok) return;
        try {
            await api.delete(`/inventory/products/${id}`);
            navigate('/inventory/products');
        } catch (err) {
            notify(getApiError(err), 'error');
        }
    };

    if (!data) {
        return <div className="py-12 text-center text-sm text-slate-400">{t('common.loading')}</div>;
    }

    const { product, stock, categories } = data;

    return (
        <div className="space-y-6">
            {confirmElement}
            {message && <Alert tone={messageTone}>{message}</Alert>}

            <div className="flex flex-wrap items-start justify-between gap-3">
                <div>
                    <Link to="/inventory/products" className="text-sm font-medium text-blue-700 hover:underline">
                        &larr; {t('inventory.back')}
                    </Link>
                    <h1 className="mt-2 text-2xl font-bold tracking-tight text-slate-900">{product.name}</h1>
                    <p className="mt-1 text-sm text-slate-500">
                        {product.barcode ? `${t('inventory.barcode_label', { barcode: product.barcode })} · ` : ''}
                        {categories.find((c) => c.id === Number(product.category_id))?.name || t('inventory.no_category')}
                    </p>
                </div>
                <div className="flex flex-wrap gap-2">
                    <Button type="button" variant="secondary" onClick={handleQuickSell} loading={working}>
                        <Zap className="h-4 w-4" />
                        {t('inventory.quick_sell')}
                    </Button>
                    <Link to={`/inventory/products/${id}/edit`}>
                        <Button type="button" variant="secondary">
                            <Pencil className="h-4 w-4" />
                            {t('common.edit')}
                        </Button>
                    </Link>
                    {isAdmin && (
                        <Button type="button" variant="danger" onClick={handleDeleteProduct}>
                            <Trash2 className="h-4 w-4" />
                            {t('common.delete')}
                        </Button>
                    )}
                </div>
            </div>

            <div className="grid gap-6 lg:grid-cols-3">
                <div className="space-y-6 lg:col-span-2">
                    <Card className="overflow-hidden">
                        <CardHeader title={t('inventory.stock_info')} />
                        <div className="grid gap-6 p-6 sm:grid-cols-3">
                            <div>
                                <p className="text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    {t('inventory.current_stock')}
                                </p>
                                <Badge solid tone={stockTone(stock, product)} className="mt-2 text-base">
                                    {stock}
                                </Badge>
                            </div>
                            <div>
                                <p className="text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    {t('inventory.selling_price_label')}
                                </p>
                                <p className="mt-2 text-lg font-semibold text-slate-800">
                                    $ {formatMoney(product.selling_price)}
                                </p>
                            </div>
                            <div>
                                <p className="text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    {t('inventory.alert_range')}
                                </p>
                                <p className="mt-2 text-sm text-slate-700">
                                    {product.min_stock != null ? product.min_stock : '—'} &mdash;{' '}
                                    {product.max_stock != null ? product.max_stock : '—'}
                                </p>
                                {product.email_alert === 'yes' && (
                                    <p className="mt-1 text-xs text-slate-400">{t('inventory.email_alerts_on')}</p>
                                )}
                            </div>
                        </div>
                    </Card>

                    <Card className="overflow-hidden">
                        <CardHeader title={t('inventory.restock')} subtitle={t('inventory.restock_subtitle')} />
                        <form onSubmit={handleRestock} className="grid gap-5 border-b border-slate-100 p-6 sm:grid-cols-2">
                            <div>
                                <label className="mb-1.5 block text-sm font-medium text-slate-700" htmlFor="rp">
                                    {t('inventory.purchase_price')} *
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
                                    {t('common.quantity')} *
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
                                    {t('inventory.add_to_stock')}
                                </Button>
                            </div>
                        </form>
                    </Card>

                    <Card className="overflow-hidden">
                        <CardHeader title={t('inventory.transactions')} subtitle={t('inventory.transactions_subtitle')} />
                        {transactions.length === 0 ? (
                            <div className="px-6 py-8 text-center text-sm text-slate-400">
                                {t('inventory.no_transactions')}
                            </div>
                        ) : (
                            <>
                                <div className="overflow-x-auto">
                                    <table className="w-full text-left">
                                        <thead>
                                            <tr className="border-b border-slate-100 bg-slate-50/60 text-xs font-semibold uppercase tracking-wide text-slate-500">
                                                <th className="px-6 py-3">{t('inventory.type')}</th>
                                                <th className="px-6 py-3">{t('common.qty')}</th>
                                                <th className="px-6 py-3">{t('common.price')}</th>
                                                <th className="px-6 py-3">{t('inventory.source')}</th>
                                                <th className="px-6 py-3">{t('common.date')}</th>
                                                <th className="px-6 py-3 text-right">{t('common.actions')}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {transactions.map((tx) => (
                                                <tr
                                                    key={tx.id}
                                                    className="border-b border-slate-100 last:border-0 hover:bg-slate-50"
                                                >
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
                                                            onClick={() => handleDeleteTransaction(tx)}
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
                            {t('inventory.product_info')}
                        </h3>
                        <dl className="mt-3 space-y-2 text-sm">
                            <div className="flex justify-between gap-3">
                                <dt className="text-slate-400">{t('inventory.supplier')}</dt>
                                <dd className="font-medium text-slate-700">{product.supplier || '—'}</dd>
                            </div>
                            <div className="flex justify-between gap-3">
                                <dt className="text-slate-400">{t('inventory.barcode')}</dt>
                                <dd className="font-mono text-xs text-slate-700">{product.barcode || '—'}</dd>
                            </div>
                            <div className="flex justify-between gap-3">
                                <dt className="text-slate-400">{t('inventory.email_alert')}</dt>
                                <dd className="font-medium text-slate-700">
                                    {product.email_alert === 'yes' ? t('common.yes') : t('common.no')}
                                </dd>
                            </div>
                            <div className="flex justify-between gap-3">
                                <dt className="text-slate-400">{t('common.created')}</dt>
                                <dd className="font-medium text-slate-700">{formatDate(product.created_at)}</dd>
                            </div>
                        </dl>
                    </Card>
                </div>
            </div>
        </div>
    );
}