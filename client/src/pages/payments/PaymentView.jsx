import React, { useEffect, useState } from 'react';
import { Link, useNavigate, useParams } from 'react-router-dom';
import { FileText, Pencil, Save, Trash2, X } from 'lucide-react';
import api from '../../api/client';
import { formatDate, formatDateTime, formatMoney, getApiError } from '../../utils/format';
import Card from '../../components/ui/Card';
import Button from '../../components/ui/Button';
import Input from '../../components/ui/Input';
import Badge from '../../components/ui/Badge';
import Alert from '../../components/ui/Alert';

const METHODS = ['cash', 'card', 'check', 'other'];

function FieldError({ error }) {
    return error ? <p className="mt-1 text-xs text-red-600">{error[0]}</p> : null;
}

const fieldClasses =
    'block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20';

export default function PaymentView() {
    const { id } = useParams();
    const navigate = useNavigate();
    const [data, setData] = useState(null);
    const [form, setForm] = useState(null);
    const [errors, setErrors] = useState({});
    const [saving, setSaving] = useState(false);
    const [deleting, setDeleting] = useState(false);
    const [editing, setEditing] = useState(false);
    const [message, setMessage] = useState('');
    const [messageTone, setMessageTone] = useState('success');

    const load = () => {
        api.get(`/payments/${id}`)
            .then(({ data }) => {
                setData(data.data);
                const p = data.data.payment;
                setForm({
                    amount: p.amount != null ? String(p.amount) : '',
                    method: p.method || 'cash',
                    ref: p.ref || '',
                });
            })
            .catch((err) => notify(getApiError(err, 'Could not load this payment.'), 'error'));
    };

    useEffect(() => {
        setMessage('');
        setEditing(false);
        load();
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [id]);

    const notify = (text, tone = 'success') => {
        setMessage(text);
        setMessageTone(tone);
        window.scrollTo({ top: 0, behavior: 'smooth' });
    };

    const handleUpdate = async (e) => {
        e.preventDefault();
        setErrors({});
        setSaving(true);
        try {
            const { data } = await api.put(`/payments/${id}`, {
                ...form,
                amount: Number(form.amount),
                ref: form.ref || null,
            });
            notify(data.message);
            setEditing(false);
            load();
        } catch (err) {
            const response = err.response;
            if (response && response.data && response.data.errors) {
                setErrors(response.data.errors);
                setMessage(response.data.message);
                setMessageTone('error');
            } else {
                notify(getApiError(err), 'error');
            }
        } finally {
            setSaving(false);
        }
    };

    const handleDelete = async () => {
        if (!window.confirm('Delete this payment? This cannot be undone.')) return;
        setDeleting(true);
        try {
            await api.delete(`/payments/${id}`);
            navigate('/payments');
        } catch (err) {
            notify(getApiError(err), 'error');
            setDeleting(false);
        }
    };

    if (!data || !form) {
        return <div className="py-12 text-center text-sm text-slate-400">Loading...</div>;
    }

    const { payment, invoice } = data;

    return (
        <div className="mx-auto max-w-2xl space-y-6">
            {message && <Alert tone={messageTone}>{message}</Alert>}

            <div className="flex flex-wrap items-start justify-between gap-3">
                <div>
                    <Link to="/payments" className="text-sm font-medium text-blue-700 hover:underline">
                        &larr; Back to payments
                    </Link>
                    <div className="mt-2 flex items-center gap-3">
                        <h1 className="text-2xl font-bold tracking-tight text-slate-900">
                            Payment #{payment.id}
                        </h1>
                        <Badge tone="emerald">$ {formatMoney(payment.amount)}</Badge>
                    </div>
                    <p className="mt-1 text-sm text-slate-500">Created {formatDate(payment.created_at)}</p>
                </div>
                <div className="flex flex-wrap items-center gap-2">
                    {invoice && (
                        <Button type="button" variant="secondary" onClick={() => navigate(`/invoices/${invoice.id}`)}>
                            <FileText className="h-4 w-4" />
                            View invoice
                        </Button>
                    )}
                    {!editing && (
                        <Button type="button" variant="secondary" onClick={() => setEditing(true)}>
                            <Pencil className="h-4 w-4" />
                            Edit
                        </Button>
                    )}
                    {!invoice && (
                        <button
                            type="button"
                            onClick={handleDelete}
                            disabled={deleting}
                            className="rounded-lg p-2 text-slate-400 transition-colors hover:bg-red-50 hover:text-red-600 disabled:opacity-50"
                            aria-label="Delete payment"
                            title="Delete payment"
                        >
                            <Trash2 className="h-4 w-4" />
                        </button>
                    )}
                </div>
            </div>

            <Card className="overflow-hidden">
                <form onSubmit={handleUpdate}>
                    <div className="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 px-6 py-4">
                        <h2 className="text-sm font-semibold text-slate-900">Payment details</h2>
                        {editing && (
                            <div className="flex gap-2">
                                <Button
                                    type="button"
                                    variant="secondary"
                                    size="sm"
                                    onClick={() => {
                                        setEditing(false);
                                        setErrors({});
                                        load();
                                    }}
                                >
                                    <X className="h-3.5 w-3.5" />
                                    Cancel
                                </Button>
                                <Button type="submit" size="sm" loading={saving}>
                                    <Save className="h-3.5 w-3.5" />
                                    Save
                                </Button>
                            </div>
                        )}
                    </div>

                    <div className="grid gap-6 px-6 py-5 sm:grid-cols-2">
                        <div>
                            <p className="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                Amount
                            </p>
                            {editing ? (
                                <div className="mt-1.5">
                                    <Input
                                        type="number"
                                        step="0.01"
                                        value={form.amount}
                                        onChange={(e) => setForm((f) => ({ ...f, amount: e.target.value }))}
                                        invalid={!!errors.amount}
                                        autoFocus
                                    />
                                    <FieldError error={errors.amount} />
                                </div>
                            ) : (
                                <p className="mt-1 text-base font-semibold text-slate-900">
                                    $ {formatMoney(payment.amount)}
                                </p>
                            )}
                        </div>

                        <div>
                            <p className="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                Method
                            </p>
                            {editing ? (
                                <div className="mt-1.5">
                                    <select
                                        className={fieldClasses}
                                        value={form.method}
                                        onChange={(e) => setForm((f) => ({ ...f, method: e.target.value }))}
                                    >
                                        {METHODS.map((method) => (
                                            <option key={method} value={method}>
                                                {method.toUpperCase()}
                                            </option>
                                        ))}
                                    </select>
                                    <FieldError error={errors.method} />
                                </div>
                            ) : (
                                <p className="mt-1 text-sm font-medium uppercase text-slate-700">
                                    {payment.method}
                                </p>
                            )}
                        </div>

                        <div className="sm:col-span-2">
                            <p className="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                Reference
                            </p>
                            {editing ? (
                                <div className="mt-1.5">
                                    <Input
                                        value={form.ref}
                                        onChange={(e) => setForm((f) => ({ ...f, ref: e.target.value }))}
                                        placeholder="Optional"
                                        invalid={!!errors.ref}
                                    />
                                    <FieldError error={errors.ref} />
                                </div>
                            ) : (
                                <p className="mt-1 text-sm text-slate-700">{payment.ref || '—'}</p>
                            )}
                        </div>

                        <div>
                            <p className="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                Invoice
                            </p>
                            {invoice ? (
                                <Link
                                    to={`/invoices/${invoice.id}`}
                                    className="mt-1 inline-block text-sm font-medium text-blue-700 hover:underline"
                                >
                                    #{invoice.id} &middot; {invoice.customer_name || 'No customer'} &middot;{' '}
                                    $ {formatMoney(invoice.total)}
                                </Link>
                            ) : (
                                <p className="mt-1 text-sm text-slate-500">Not attached to an invoice</p>
                            )}
                        </div>
                    </div>

                    <div className="flex flex-wrap items-center gap-x-6 gap-y-2 border-t border-slate-100 px-6 py-4 text-xs text-slate-400">
                        <span>id: {payment.id}</span>
                        <span>created_at: {formatDateTime(payment.created_at)}</span>
                        <span>updated_at: {formatDateTime(payment.updated_at)}</span>
                    </div>
                </form>
            </Card>
        </div>
    );
}