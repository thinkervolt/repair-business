import React, { useEffect, useRef, useState } from 'react';
import { Link } from 'react-router-dom';
import { CreditCard, Search, Save } from 'lucide-react';
import api from '../../api/client';
import { useI18n } from '../../i18n/I18nContext';
import { formatDate, formatMoney, getApiError } from '../../utils/format';
import Card from '../../components/ui/Card';
import Button from '../../components/ui/Button';
import Input from '../../components/ui/Input';
import Alert from '../../components/ui/Alert';
import Pagination from '../../components/ui/Pagination';

const METHODS = ['cash', 'card', 'check', 'other'];

function NewPaymentForm({ onCreated, notify }) {
    const { t } = useI18n();
    const [form, setForm] = useState({ amount: '', method: 'cash', ref: '' });
    const [errors, setErrors] = useState({});
    const [saving, setSaving] = useState(false);

    const handleSubmit = async (e) => {
        e.preventDefault();
        setErrors({});
        setSaving(true);
        try {
            const { data } = await api.post('/payments', {
                ...form,
                amount: Number(form.amount),
                ref: form.ref || null,
            });
            setForm({ amount: '', method: 'cash', ref: '' });
            notify(data.message);
            onCreated();
        } catch (err) {
            const response = err.response;
            if (response && response.data && response.data.errors) {
                setErrors(response.data.errors);
            } else {
                notify(getApiError(err), 'error');
            }
        } finally {
            setSaving(false);
        }
    };

    return (
        <form onSubmit={handleSubmit} className="grid gap-3 sm:grid-cols-[1fr_160px_1fr_auto]">
            <div>
                <label className="mb-1 block text-xs font-medium text-slate-500">{t('common.amount')}</label>
                <Input
                    type="number"
                    step="0.01"
                    value={form.amount}
                    onChange={(e) => setForm((f) => ({ ...f, amount: e.target.value }))}
                    placeholder="0.00"
                    invalid={!!errors.amount}
                />
                {errors.amount && <p className="mt-1 text-xs text-red-600">{errors.amount[0]}</p>}
            </div>
            <div>
                <label className="mb-1 block text-xs font-medium text-slate-500">{t('common.method')}</label>
                <select
                    value={form.method}
                    onChange={(e) => setForm((f) => ({ ...f, method: e.target.value }))}
                    className="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-800 shadow-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                >
{METHODS.map((method) => (
                                        <option key={method} value={method}>
                                            {method.toUpperCase()}
                                        </option>
                                    ))}
                </select>
                {errors.method && <p className="mt-1 text-xs text-red-600">{errors.method[0]}</p>}
            </div>
            <div>
                <label className="mb-1 block text-xs font-medium text-slate-500">{t('common.reference')}</label>
                <Input
                    value={form.ref}
                    onChange={(e) => setForm((f) => ({ ...f, ref: e.target.value }))}
                    placeholder={t('common.optional')}
                    invalid={!!errors.ref}
                />
                {errors.ref && <p className="mt-1 text-xs text-red-600">{errors.ref[0]}</p>}
            </div>
            <div className="flex items-end">
                <Button type="submit" loading={saving} disabled={!form.amount}>
                    <Save className="h-4 w-4" />
                    {t('payments.create')}
                </Button>
            </div>
        </form>
    );
}

export default function PaymentsList() {
    const { t } = useI18n();
    const [data, setData] = useState(null);
    const [search, setSearch] = useState('');
    const [debounced, setDebounced] = useState('');
    const [page, setPage] = useState(1);
    const [error, setError] = useState('');
    const [message, setMessage] = useState('');
    const timer = useRef(null);

    useEffect(() => {
        clearTimeout(timer.current);
        timer.current = setTimeout(() => {
            setDebounced(search);
            setPage(1);
        }, 350);
        return () => clearTimeout(timer.current);
    }, [search]);

    useEffect(() => {
        api.get('/payments', { params: { search: debounced, page } })
            .then(({ data }) => setData(data.data))
            .catch((err) => setError(getApiError(err)));
    }, [debounced, page]);

    const notify = (text, tone = 'success') => {
        setMessage(text);
        setError(tone === 'error' ? text : '');
        window.scrollTo({ top: 0, behavior: 'smooth' });
    };

    const handleReload = () => setData(null);

    return (
        <div className="space-y-6">
            <div>
                <h1 className="text-2xl font-bold tracking-tight text-slate-900">{t('nav.payments')}</h1>
                <p className="mt-1 text-sm text-slate-500">
                    {t('payments.subtitle')}
                </p>
            </div>

            {error && <Alert tone="error">{error}</Alert>}
            {message && <Alert tone="success">{message}</Alert>}

            <Card className="overflow-hidden">
                <div className="border-b border-slate-100 px-5 py-4">
                    <div className="flex items-center gap-2">
                        <CreditCard className="h-4 w-4 text-slate-400" />
                        <h2 className="text-sm font-semibold text-slate-900">{t('payments.create')}</h2>
                    </div>
                    <p className="mt-1 text-xs text-slate-500">
                        {t('payments.create_intro')}
                    </p>
                </div>
                <div className="px-5 py-4">
                    <NewPaymentForm notify={notify} onCreated={() => handleReload()} />
                </div>
            </Card>

            <Card className="overflow-hidden">
                <div className="border-b border-slate-100 p-4 sm:p-5">
                    <div className="relative max-w-sm">
                        <Search className="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                        <Input
                            type="search"
                            value={search}
                            onChange={(e) => setSearch(e.target.value)}
                            placeholder={t('payments.search_placeholder')}
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
                                        <th className="px-6 py-3">{t('common.date')}</th>
                                        <th className="px-6 py-3">{t('payments.invoice')}</th>
                                        <th className="px-6 py-3 text-right">{t('common.amount')}</th>
                                        <th className="px-6 py-3">{t('common.method')}</th>
                                        <th className="px-6 py-3">{t('common.reference')}</th>
                                        <th className="px-6 py-3 text-right">{t('common.actions')}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {data.data.map((payment) => (
                                        <tr
                                            key={payment.id}
                                            className="border-b border-slate-100 transition-colors last:border-0 hover:bg-slate-50"
                                        >
                                            <td className="px-6 py-3 text-sm text-slate-600">
                                                {formatDate(payment.created_at)}
                                            </td>
                                            <td className="px-6 py-3">
                                                {payment.invoice ? (
                                                    <Link
                                                        to={`/invoices/${payment.invoice}`}
                                                        className="text-sm font-medium text-blue-700 hover:underline"
                                                    >
                                                        #{payment.invoice}
                                                    </Link>
                                                ) : (
                                                    <span className="text-sm text-slate-400">—</span>
                                                )}
                                            </td>
                                            <td className="px-6 py-3 text-right">
                                                <span
                                                    className={`text-sm font-medium ${
                                                        payment.amount > 0
                                                            ? 'text-emerald-600'
                                                            : 'text-red-600'
                                                    }`}
                                                >
                                                    $ {formatMoney(payment.amount)}
                                                </span>
                                            </td>
                                            <td className="px-6 py-3 text-sm uppercase text-slate-600">
                                                {payment.method}
                                            </td>
                                            <td className="px-6 py-3 text-sm text-slate-600">
                                                {payment.ref || '—'}
                                            </td>
                                            <td className="px-6 py-3 text-right">
                                                <Link
                                                    to={`/payments/${payment.id}`}
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