import React, { useEffect, useState } from 'react';
import { Link, useNavigate, useParams } from 'react-router-dom';
import {
    ChevronDown,
    FileText,
    Mail,
    Plus,
    Printer,
    Save,
    Trash2,
    UserRound,
} from 'lucide-react';
import api from '../../api/client';
import { useI18n } from '../../i18n/I18nContext';
import { formatMoney, formatDate, getApiError } from '../../utils/format';
import { toneFor } from '../../utils/colors';
import Card from '../../components/ui/Card';
import Button from '../../components/ui/Button';
import Input from '../../components/ui/Input';
import Badge from '../../components/ui/Badge';
import Alert from '../../components/ui/Alert';
import Pagination from '../../components/ui/Pagination';
import CustomerPicker from '../../components/customers/CustomerPicker';
import ProductPicker from '../../components/inventory/ProductPicker';

function FieldError({ error }) {
    return error ? <p className="mt-1 text-xs text-red-600">{error[0]}</p> : null;
}

const fieldClasses =
    'block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20';

const selectBase =
    'rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20';

function EmptyBlock({ children }) {
    return (
        <div className="px-6 py-8 text-center text-sm text-slate-400">
            {children}
        </div>
    );
}

export default function RepairView() {
    const { id } = useParams();
    const navigate = useNavigate();
    const { t } = useI18n();
    const [data, setData] = useState(null);
    const [form, setForm] = useState(null);
    const [errors, setErrors] = useState({});
    const [saving, setSaving] = useState(false);
    const [deleting, setDeleting] = useState(false);
    const [mailing, setMailing] = useState(false);
    const [itemText, setItemText] = useState('');
    const [itemGroup, setItemGroup] = useState('job');
    const [message, setMessage] = useState('');
    const [messageTone, setMessageTone] = useState('success');
    const [pickerOpen, setPickerOpen] = useState(false);
    const [partsOpen, setPartsOpen] = useState(false);
    const [logsOpen, setLogsOpen] = useState(false);

    const load = () => {
        api.get(`/repairs/${id}`)
            .then(({ data }) => {
                setData(data.data);
                const r = data.data.repair;
                setForm({
                    target: r.target,
                    data_request: r.request,
                    status: r.status != null ? String(r.status) : '',
                    priority: r.priority != null ? String(r.priority) : '',
                    user: r.user != null ? String(r.user) : '',
                    estimate: r.estimate != null ? String(r.estimate) : '',
                });
            })
            .catch((err) => notify(getApiError(err, 'Could not load this repair.'), 'error'));
    };

    useEffect(() => {
        setMessage('');
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
            const { data } = await api.put(`/repairs/${id}`, {
                target: form.target,
                data_request: form.data_request,
                status: form.status || null,
                priority: form.priority || null,
                user: form.user || null,
                estimate: form.estimate || null,
            });
            notify(data.message);
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

    const handleAddItem = async (e) => {
        e.preventDefault();
        try {
            await api.post(`/repairs/${id}/items`, { data: itemText, group: itemGroup });
            setItemText('');
            load();
        } catch (err) {
            notify(getApiError(err), 'error');
        }
    };

    const handleAddPart = async (product) => {
        setPartsOpen(false);
        try {
            const { data } = await api.post(`/inventory/repairs/${id}/products/${product.id}/sell`);
            notify(data.message);
            load();
        } catch (err) {
            notify(getApiError(err), 'error');
        }
    };

    const handleDeleteItem = async (itemId) => {
        try {
            await api.delete(`/repairs/items/${itemId}`);
            load();
        } catch (err) {
            notify(getApiError(err), 'error');
        }
    };

    const handleAssignCustomer = async (customer) => {
        setPickerOpen(false);
        try {
            const { data } = await api.put(`/repairs/${id}/assign-customer/${customer.id}`);
            notify(data.message);
            load();
        } catch (err) {
            notify(getApiError(err), 'error');
        }
    };

    const handlePrint = async () => {
        try {
            const { data } = await api.get(`/repairs/${id}/print`, { responseType: 'blob' });
            const url = window.URL.createObjectURL(data);
            window.open(url, '_blank');
            setTimeout(() => window.URL.revokeObjectURL(url), 30000);
        } catch (err) {
            notify(getApiError(err, 'Could not generate the receipt.'), 'error');
        }
    };

    const handleMail = async () => {
        setMailing(true);
        try {
            const { data } = await api.post(`/repairs/${id}/mail`);
            notify(data.message);
        } catch (err) {
            notify(getApiError(err, 'Could not send the receipt.'), 'error');
        } finally {
            setMailing(false);
        }
    };

    const handleDelete = async () => {
        if (!window.confirm(t('repairs.delete_confirm'))) return;
        setDeleting(true);
        try {
            await api.put(`/repairs/${id}/delete`);
            navigate('/repairs');
        } catch (err) {
            notify(getApiError(err), 'error');
            setDeleting(false);
        }
    };

    if (!data || !form) {
        return <div className="py-12 text-center text-sm text-slate-400">{t('common.loading')}</div>;
    }

    const { repair, comments, jobs, transactions, logs, invoices, users, statuses, priorities } = data;

    return (
        <div className="space-y-6">
            {message && <Alert tone={messageTone}>{message}</Alert>}

            <div className="flex flex-wrap items-start justify-between gap-3">
                <div>
                    <Link to="/repairs" className="text-sm font-medium text-blue-700 hover:underline">
                        &larr; {t('repairs.back')}
                    </Link>
                    <h1 className="mt-2 text-2xl font-bold tracking-tight text-slate-900">
                        {t('repairs.repair_number', { id: repair.id })}
                    </h1>
                    <p className="mt-1 text-sm text-slate-500">
                        {repair.target} &middot; {t('common.created_on', { date: formatDate(repair.created_at) })}
                    </p>
                </div>
                <div className="flex flex-wrap gap-2">
                    <Button type="button" variant="secondary" onClick={handlePrint}>
                        <Printer className="h-4 w-4" />
                        {t('repairs.dropoff_receipt')}
                    </Button>
                    <Button type="button" variant="secondary" onClick={handleMail} loading={mailing}>
                        <Mail className="h-4 w-4" />
                        {t('repairs.email_receipt')}
                    </Button>
                    <Link to={`/invoices/create?repair=${repair.id}`}>
                        <Button>
                            <FileText className="h-4 w-4" />
                            {t('repairs.invoice')}
                        </Button>
                    </Link>
                </div>
            </div>

            <div className="grid gap-6 lg:grid-cols-3">
                <div className="space-y-6 lg:col-span-2">
                    <Card className="p-6">
                        <h2 className="mb-4 text-sm font-semibold uppercase tracking-wide text-slate-500">
                            {t('repairs.details')}
                        </h2>
                        <form onSubmit={handleUpdate} className="space-y-5">
                            <div className="grid gap-5 sm:grid-cols-2">
                                <div>
                                    <label className="mb-1.5 block text-sm font-medium text-slate-700" htmlFor="target">
                                        {t('repairs.target')} *
                                    </label>
                                    <Input
                                        id="target"
                                        value={form.target}
                                        onChange={(e) => setForm((f) => ({ ...f, target: e.target.value }))}
                                        invalid={!!errors.target}
                                    />
                                    <FieldError error={errors.target} />
                                </div>
                                <div>
                                    <label className="mb-1.5 block text-sm font-medium text-slate-700" htmlFor="request">
                                        {t('repairs.request')} *
                                    </label>
                                    <Input
                                        id="request"
                                        value={form.data_request}
                                        onChange={(e) => setForm((f) => ({ ...f, data_request: e.target.value }))}
                                        invalid={!!errors.data_request}
                                    />
                                    <FieldError error={errors.data_request} />
                                </div>
                                <div>
                                    <label className="mb-1.5 block text-sm font-medium text-slate-700" htmlFor="status">
                                        {t('common.status')}
                                    </label>
                                    <select
                                        id="status"
                                        className={fieldClasses}
                                        value={form.status}
                                        onChange={(e) => setForm((f) => ({ ...f, status: e.target.value }))}
                                    >
                                        <option value="">—</option>
                                        {statuses.map((s) => (
                                            <option key={s.id} value={s.id}>
                                                {s.name}
                                            </option>
                                        ))}
                                    </select>
                                </div>
                                <div>
                                    <label className="mb-1.5 block text-sm font-medium text-slate-700" htmlFor="priority">
                                        {t('repairs.priority')}
                                    </label>
                                    <select
                                        id="priority"
                                        className={fieldClasses}
                                        value={form.priority}
                                        onChange={(e) => setForm((f) => ({ ...f, priority: e.target.value }))}
                                    >
                                        <option value="">—</option>
                                        {priorities.map((p) => (
                                            <option key={p.id} value={p.id}>
                                                {p.name}
                                            </option>
                                        ))}
                                    </select>
                                </div>
                                <div>
                                    <label className="mb-1.5 block text-sm font-medium text-slate-700" htmlFor="estimate">
                                        {t('repairs.estimate')}
                                    </label>
                                    <Input
                                        id="estimate"
                                        type="number"
                                        step="0.01"
                                        min="0"
                                        value={form.estimate}
                                        onChange={(e) => setForm((f) => ({ ...f, estimate: e.target.value }))}
                                        invalid={!!errors.estimate}
                                    />
                                    <FieldError error={errors.estimate} />
                                </div>
                                <div>
                                    <label className="mb-1.5 block text-sm font-medium text-slate-700" htmlFor="user">
                                        {t('repairs.assigned_agent')}
                                    </label>
                                    <select
                                        id="user"
                                        className={fieldClasses}
                                        value={form.user}
                                        onChange={(e) => setForm((f) => ({ ...f, user: e.target.value }))}
                                    >
                                        <option value="">—</option>
                                        {users.map((u) => (
                                            <option key={u.id} value={u.id}>
                                                {u.name}
                                            </option>
                                        ))}
                                    </select>
                                </div>
                            </div>
                            <div className="flex justify-end border-t border-slate-100 pt-5">
                                <Button type="submit" loading={saving}>
                                    <Save className="h-4 w-4" />
                                    {t('repairs.update')}
                                </Button>
                            </div>
                        </form>
                    </Card>

                    <Card className="overflow-hidden">
                        <div className="border-b border-slate-100 px-6 py-4">
                            <h2 className="text-sm font-semibold text-slate-900">{t('repairs.jobs_comments')}</h2>
                        </div>
                        <form onSubmit={handleAddItem} className="flex gap-2 border-b border-slate-100 px-6 py-4">
                            <select
                                className={`${selectBase} w-36 shrink-0`}
                                value={itemGroup}
                                onChange={(e) => setItemGroup(e.target.value)}
                            >
                                <option value="job">{t('repairs.job')}</option>
                                <option value="comment">{t('repairs.comment')}</option>
                            </select>
                            <Input
                                value={itemText}
                                onChange={(e) => setItemText(e.target.value)}
                                placeholder={itemGroup === 'job' ? t('repairs.job_placeholder') : t('repairs.comment_placeholder')}
                                className="min-w-0 flex-1"
                            />
                            <Button type="submit" className="shrink-0" disabled={!itemText.trim()}>
                                <Plus className="h-4 w-4" />
                                {t('common.add')}
                            </Button>
                        </form>
                        {comments.length === 0 && jobs.length === 0 ? (
                            <EmptyBlock>{t('repairs.empty_jobs_comments')}</EmptyBlock>
                        ) : (
                            <ul className="divide-y divide-slate-100">
                                {[...jobs, ...comments].map((item) => (
                                    <li key={item.id} className="flex items-start justify-between gap-3 px-6 py-3">
                                        <div className="min-w-0">
                                            <div className="flex items-center gap-2">
                                                <Badge tone={item.group === 'job' ? 'blue' : 'slate'}>
                                                    {item.group === 'job' ? t('repairs.job') : t('repairs.comment')}
                                                </Badge>
                                                <span className="text-sm text-slate-400">
                                                    {item.agent_data ? item.agent_data.name : '—'} &middot;{' '}
                                                    {formatDate(item.created_at)}
                                                </span>
                                            </div>
                                            <p className="mt-1 text-sm text-slate-700">{item.data}</p>
                                        </div>
                                        <button
                                            type="button"
                                            onClick={() => handleDeleteItem(item.id)}
                                            className="shrink-0 rounded-lg p-1.5 text-slate-400 transition-colors hover:bg-red-50 hover:text-red-600"
                                            aria-label="Delete item"
                                        >
                                            <Trash2 className="h-4 w-4" />
                                        </button>
                                    </li>
                                ))}
                            </ul>
                        )}
                    </Card>

                    <Card className="overflow-hidden">
                        <div className="flex items-center justify-between border-b border-slate-100 px-6 py-4">
                            <h2 className="text-sm font-semibold text-slate-900">{t('repairs.parts_used')}</h2>
                            <Button
                                type="button"
                                variant="secondary"
                                size="sm"
                                onClick={() => setPartsOpen(true)}
                            >
                                <Plus className="h-3.5 w-3.5" />
                                {t('repairs.add_part')}
                            </Button>
                        </div>
                        {transactions.length === 0 ? (
                            <EmptyBlock>{t('repairs.empty_parts')}</EmptyBlock>
                        ) : (
                            <div className="overflow-x-auto">
                                <table className="w-full text-left">
                                    <thead>
                                        <tr className="border-b border-slate-100 bg-slate-50/60 text-xs font-semibold uppercase tracking-wide text-slate-500">
                                            <th className="px-6 py-3">{t('inventory.product')}</th>
                                            <th className="px-6 py-3">{t('inventory.type')}</th>
                                            <th className="px-6 py-3">{t('common.qty')}</th>
                                            <th className="px-6 py-3">{t('common.price')}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {transactions.map((tx) => (
                                            <tr key={tx.id} className="border-b border-slate-100 last:border-0">
                                                <td className="px-6 py-3 text-sm text-slate-700">
                                                    {tx.product ? tx.product.name : `#${tx.product_id}`}
                                                </td>
                                                <td className="px-6 py-3">
                                                    <Badge tone={tx.transaction === 'purchase' ? 'emerald' : 'red'}>
                                                        {tx.transaction === 'purchase' ? t('repairs.purchased') : t('repairs.used_on_repair')}
                                                    </Badge>
                                                </td>
                                                <td className="px-6 py-3 text-sm text-slate-600">{tx.quantity}</td>
                                                <td className="px-6 py-3 text-sm text-slate-600">
                                                    {tx.selling_price != null ? `$ ${formatMoney(tx.selling_price)}` : '—'}
                                                </td>
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                            </div>
                        )}
                    </Card>

                    <Card className="overflow-hidden">
                        <div className="border-b border-slate-100 px-6 py-4">
                            <h2 className="text-sm font-semibold text-slate-900">{t('repairs.invoices')}</h2>
                        </div>
                        {invoices.data.length === 0 ? (
                            <EmptyBlock>{t('repairs.empty_invoices')}</EmptyBlock>
                        ) : (
                            <>
                                <div className="overflow-x-auto">
                                    <table className="w-full text-left">
                                        <thead>
                                            <tr className="border-b border-slate-100 bg-slate-50/60 text-xs font-semibold uppercase tracking-wide text-slate-500">
                                                <th className="px-6 py-3">{t('common.id')}</th>
                                                <th className="px-6 py-3">{t('common.status')}</th>
                                                <th className="px-6 py-3">{t('common.balance')}</th>
                                                <th className="px-6 py-3 text-right">{t('common.actions')}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {invoices.data.map((inv) => (
                                                <tr key={inv.id} className="border-b border-slate-100 last:border-0 hover:bg-slate-50">
                                                    <td className="px-6 py-3 text-sm text-slate-700">#{inv.id}</td>
                                                    <td className="px-6 py-3">
                                                        {inv.status_data && (
                                                            <Badge solid tone={toneFor(inv.status_data.color)}>
                                                                {inv.status_data.name}
                                                            </Badge>
                                                        )}
                                                    </td>
                                                    <td
                                                        className={`px-6 py-3 text-sm font-medium ${
                                                            inv.balance < 0
                                                                ? 'text-emerald-600'
                                                                : inv.balance > 0
                                                                  ? 'text-red-600'
                                                                  : 'text-slate-700'
                                                        }`}
                                                    >
                                                        $ {formatMoney(inv.balance)}
                                                    </td>
                                                    <td className="px-6 py-3 text-right">
                                                        <Link
                                                            to={`/invoices/${inv.id}`}
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
                                <Pagination paginator={invoices} onChangePage={() => {}} />
                            </>
                        )}
                    </Card>

                    <Card className="overflow-hidden">
                        <button
                            type="button"
                            onClick={() => setLogsOpen((o) => !o)}
                            className="flex w-full items-center justify-between px-6 py-3 text-left transition-colors hover:bg-slate-50"
                        >
                            <span className="text-xs font-semibold uppercase tracking-wide text-slate-500">
                                {t('repairs.activity_log')}
                                <span className="ml-1.5 font-normal normal-case text-slate-400">({logs.total})</span>
                            </span>
                            <ChevronDown
                                className={`h-4 w-4 text-slate-400 transition-transform ${logsOpen ? 'rotate-180' : ''}`}
                            />
                        </button>
                        {logsOpen &&
                            (logs.data.length === 0 ? (
                                <EmptyBlock>{t('repairs.empty_entries')}</EmptyBlock>
                            ) : (
                                <>
                                    <ul className="divide-y divide-slate-100">
                                        {logs.data.map((log) => (
                                            <li key={log.id} className="px-6 py-2">
                                                <p className="text-xs leading-relaxed text-slate-600">{log.data}</p>
                                                <p className="mt-0.5 text-[10px] text-slate-400">
                                                    {log.user_data ? log.user_data.name : '—'} &middot;{' '}
                                                    {formatDate(log.created_at)}
                                                </p>
                                            </li>
                                        ))}
                                    </ul>
                                    {logs.last_page > 1 && <Pagination paginator={logs} onChangePage={() => {}} />}
                                </>
                            ))}
                    </Card>
                </div>

                <div className="space-y-4">
                    <Card className="p-5">
                        <div className="flex items-center justify-between">
                            <h3 className="text-xs font-semibold uppercase tracking-wide text-slate-500">
                                {t('common.customer')}
                            </h3>
                            {!repair.customer_data && (
                                <button
                                    type="button"
                                    onClick={() => setPickerOpen(true)}
                                    className="text-xs font-medium text-blue-700 hover:underline"
                                >
                                    {t('repairs.assign')}
                                </button>
                            )}
                        </div>
                        {repair.customer_data ? (
                            <>
                                <Link
                                    to={`/customers/${repair.customer_data.id}`}
                                    className="mt-3 block text-sm font-semibold text-slate-900 hover:text-blue-700"
                                >
                                    {repair.customer_data.first_name} {repair.customer_data.last_name || ''}
                                </Link>
                                <dl className="mt-3 space-y-2 text-sm">
                                    <div className="flex justify-between gap-3">
                                        <dt className="text-slate-400">{t('common.phone')}</dt>
                                        <dd className="font-medium text-slate-700">
                                            {repair.customer_data.phone}
                                        </dd>
                                    </div>
                                    <div className="flex justify-between gap-3">
                                        <dt className="text-slate-400">{t('common.email')}</dt>
                                        <dd className="font-medium text-slate-700">
                                            {repair.customer_data.email || '—'}
                                        </dd>
                                    </div>
                                    <div className="flex justify-between gap-3">
                                        <dt className="text-slate-400">{t('common.company')}</dt>
                                        <dd className="font-medium text-slate-700">
                                            {repair.customer_data.company || '—'}
                                        </dd>
                                    </div>
                                </dl>
                                <button
                                    type="button"
                                    onClick={() => setPickerOpen(true)}
                                    className="mt-4 inline-flex items-center gap-1 text-xs font-medium text-blue-700 hover:underline"
                                >
                                    <UserRound className="h-3.5 w-3.5" />
                                    {t('repairs.reassign')}
                                </button>
                            </>
                        ) : (
                            <button
                                type="button"
                                onClick={() => setPickerOpen(true)}
                                className="mt-3 inline-flex w-full items-center justify-center gap-2 rounded-lg border border-dashed border-slate-300 px-3 py-3 text-sm text-slate-500 transition-colors hover:border-blue-400 hover:text-blue-700"
                            >
                                <UserRound className="h-4 w-4" />
                                {t('repairs.assign_customer')}
                            </button>
                        )}
                    </Card>

                    <Card className="p-5">
                        <h3 className="text-xs font-semibold uppercase tracking-wide text-slate-500">
                            {t('repairs.assigned_agent')}
                        </h3>
                        <p className="mt-3 text-sm text-slate-700">
                            {repair.agent_data ? repair.agent_data.name : '—'}
                        </p>
                    </Card>

                    <Card className="p-5">
                        <h3 className="text-xs font-semibold uppercase tracking-wide text-slate-500">
                            {t('repairs.info')}
                        </h3>
                        <dl className="mt-3 space-y-2 text-sm">
                            <div className="flex justify-between gap-3">
                                <dt className="text-slate-400">{t('common.status')}</dt>
                                <dd>
                                    {repair.status_data ? (
                                        <Badge solid tone={toneFor(repair.status_data.color)}>
                                            {repair.status_data.name}
                                        </Badge>
                                    ) : (
                                        <span className="text-slate-500">—</span>
                                    )}
                                </dd>
                            </div>
                            <div className="flex justify-between gap-3">
                                <dt className="text-slate-400">{t('repairs.priority')}</dt>
                                <dd>
                                    {repair.priority_data ? (
                                        <Badge solid tone={toneFor(repair.priority_data.color)}>
                                            {repair.priority_data.name}
                                        </Badge>
                                    ) : (
                                        <span className="text-slate-500">—</span>
                                    )}
                                </dd>
                            </div>
                            <div className="flex justify-between gap-3">
                                <dt className="text-slate-400">{t('common.estimate')}</dt>
                                <dd className="font-medium text-slate-700">
                                    {repair.estimate != null ? `$ ${formatMoney(repair.estimate)}` : '—'}
                                </dd>
                            </div>
                            <div className="flex justify-between gap-3">
                                <dt className="text-slate-400">{t('common.created')}</dt>
                                <dd className="font-medium text-slate-700">{formatDate(repair.created_at)}</dd>
                            </div>
                        </dl>
                    </Card>

                    <Button variant="danger" className="w-full" onClick={handleDelete} loading={deleting}>
                        <Trash2 className="h-4 w-4" />
                        {t('repairs.delete')}
                    </Button>
                </div>
            </div>

            <ProductPicker
                open={partsOpen}
                onClose={() => setPartsOpen(false)}
                onSelect={handleAddPart}
            />
            <CustomerPicker
                open={pickerOpen}
                onClose={() => setPickerOpen(false)}
                onSelect={handleAssignCustomer}
            />
        </div>
    );
}