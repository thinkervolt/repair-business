import React, { useEffect, useState } from 'react';
import { Pencil, Plus, Trash2, X } from 'lucide-react';
import api from '../../api/client';
import { useI18n } from '../../i18n/I18nContext';
import { getApiError } from '../../utils/format';
import { toneFor } from '../../utils/colors';
import Card from '../../components/ui/Card';
import Button from '../../components/ui/Button';
import Input from '../../components/ui/Input';
import Badge from '../../components/ui/Badge';
import Alert from '../../components/ui/Alert';

const COLORS = ['primary', 'secondary', 'success', 'danger', 'warning', 'info'];

function FieldError({ error }) {
    return error ? <p className="mt-1 text-xs text-red-600">{error[0]}</p> : null;
}

const fieldClasses =
    'block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20';

export default function InvoiceSettings() {
    const { t } = useI18n();
    const [statuses, setStatuses] = useState([]);
    const [tax, setTax] = useState('0');
    const [error, setError] = useState('');
    const [message, setMessage] = useState('');
    const [createForm, setCreateForm] = useState({ name: '', group: 'status', color: 'primary' });
    const [createErrors, setCreateErrors] = useState({});
    const [editing, setEditing] = useState(null);
    const [editForm, setEditForm] = useState({ name: '', group: 'status', color: 'primary' });
    const [editErrors, setEditErrors] = useState({});
    const [savingTax, setSavingTax] = useState(false);

    const load = () => {
        api.get('/invoice-settings')
            .then(({ data }) => {
                setStatuses(data.data.statuses);
                setTax(String(data.data.tax));
            })
            .catch((err) => setError(getApiError(err)));
    };

    useEffect(() => {
        load();
    }, []);

    const notify = (text, tone = 'success') => {
        setMessage(text);
        setError(tone === 'success' ? '' : text);
        window.scrollTo({ top: 0, behavior: 'smooth' });
    };

    const handleCreate = async (e) => {
        e.preventDefault();
        setCreateErrors({});
        try {
            const { data } = await api.post('/invoice-settings', createForm);
            setCreateForm({ name: '', group: 'status', color: 'primary' });
            notify(data.message);
            load();
        } catch (err) {
            const response = err.response;
            if (response && response.data && response.data.errors) {
                setCreateErrors(response.data.errors);
            } else {
                notify(getApiError(err), 'error');
            }
        }
    };

    const openEdit = (setting) => {
        setEditing(setting);
        setEditForm({ name: setting.name, group: setting.group, color: setting.color });
        setEditErrors({});
    };

    const handleUpdate = async (e) => {
        e.preventDefault();
        setEditErrors({});
        try {
            const { data } = await api.put(`/invoice-settings/${editing.id}`, editForm);
            setEditing(null);
            notify(data.message);
            load();
        } catch (err) {
            const response = err.response;
            if (response && response.data && response.data.errors) {
                setEditErrors(response.data.errors);
            } else {
                notify(getApiError(err), 'error');
            }
        }
    };

    const handleDelete = async (setting) => {
        if (!window.confirm(t('invoices.settings.delete_confirm', { name: setting.name }))) return;
        try {
            const { data } = await api.delete(`/invoice-settings/${setting.id}`);
            notify(data.message);
            load();
        } catch (err) {
            notify(getApiError(err), 'error');
        }
    };

    const handleSaveTax = async (e) => {
        e.preventDefault();
        setSavingTax(true);
        try {
            const { data } = await api.put('/invoice-settings/tax', { tax: Number(tax) });
            notify(data.message);
            setTax(String(data.data.tax));
        } catch (err) {
            notify(getApiError(err), 'error');
        } finally {
            setSavingTax(false);
        }
    };

    return (
        <div className="space-y-6">
            <div>
                <h1 className="text-2xl font-bold tracking-tight text-slate-900">{t('invoices.settings.title')}</h1>
                <p className="mt-1 text-sm text-slate-500">
                    {t('invoices.settings.subtitle')}
                </p>
            </div>

            {error && <Alert tone="error">{error}</Alert>}
            {message && !error && <Alert tone="success">{message}</Alert>}

            <Card className="p-6">
                <h2 className="mb-4 text-sm font-semibold uppercase tracking-wide text-slate-500">
                    {t('invoices.settings.default_tax')}
                </h2>
                <form onSubmit={handleSaveTax} className="flex max-w-sm items-end gap-3">
                    <div className="flex-1">
                        <label className="mb-1.5 block text-sm font-medium text-slate-700" htmlFor="tax">
                            {t('invoices.settings.tax_percentage')}
                        </label>
                        <Input
                            id="tax"
                            type="number"
                            step="0.01"
                            min="0"
                            max="100"
                            value={tax}
                            onChange={(e) => setTax(e.target.value)}
                        />
                    </div>
                    <Button type="submit" loading={savingTax}>
                        {t('common.save')}
                    </Button>
                </form>
            </Card>

            <Card className="p-6">
                <h2 className="mb-4 text-sm font-semibold uppercase tracking-wide text-slate-500">
                    {t('invoices.settings.new_status')}
                </h2>
                <form onSubmit={handleCreate} className="grid gap-4 sm:grid-cols-3">
                    <div>
                        <label className="mb-1.5 block text-sm font-medium text-slate-700" htmlFor="new-name">
                            {t('common.name')}
                        </label>
                        <Input
                            id="new-name"
                            value={createForm.name}
                            onChange={(e) => setCreateForm((f) => ({ ...f, name: e.target.value.toUpperCase() }))}
                            placeholder="e.g. PENDING"
                            invalid={!!createErrors.name}
                        />
                        <FieldError error={createErrors.name} />
                    </div>
                    <div>
                        <label className="mb-1.5 block text-sm font-medium text-slate-700" htmlFor="new-color">
                            {t('invoices.settings.color')}
                        </label>
                        <select
                            id="new-color"
                            className={fieldClasses}
                            value={createForm.color}
                            onChange={(e) => setCreateForm((f) => ({ ...f, color: e.target.value }))}
                        >
                            {COLORS.map((c) => (
                                <option key={c} value={c}>
                                    {c}
                                </option>
                            ))}
                        </select>
                        <FieldError error={createErrors.color} />
                    </div>
                    <div className="flex items-end">
                        <Button type="submit">
                            <Plus className="h-4 w-4" />
                            {t('invoices.settings.create_status')}
                        </Button>
                    </div>
                </form>
            </Card>

            <Card className="overflow-hidden">
                <div className="border-b border-slate-100 px-6 py-4">
                    <h2 className="text-sm font-semibold uppercase tracking-wide text-slate-500">{t('invoices.settings.statuses')}</h2>
                </div>
                {statuses.length === 0 ? (
                    <div className="px-6 py-8 text-center text-sm text-slate-400">
                        {t('invoices.settings.empty')}
                    </div>
                ) : (
                    <ul className="divide-y divide-slate-100">
                        {statuses.map((setting) => (
                            <li key={setting.id} className="flex items-center justify-between gap-3 px-6 py-3">
                                <div className="flex items-center gap-3">
                                    <Badge tone={toneFor(setting.color)}>{setting.name}</Badge>
                                    <span className="text-xs text-slate-400">#{setting.id}</span>
                                </div>
                                <div className="flex items-center gap-1">
                                    <button
                                        type="button"
                                        onClick={() => openEdit(setting)}
                                        className="rounded-lg p-1.5 text-slate-400 transition-colors hover:bg-slate-100 hover:text-slate-700"
                                        aria-label="Edit"
                                    >
                                        <Pencil className="h-4 w-4" />
                                    </button>
                                    <button
                                        type="button"
                                        onClick={() => handleDelete(setting)}
                                        className="rounded-lg p-1.5 text-slate-400 transition-colors hover:bg-red-50 hover:text-red-600"
                                        aria-label="Delete"
                                    >
                                        <Trash2 className="h-4 w-4" />
                                    </button>
                                </div>
                            </li>
                        ))}
                    </ul>
                )}
            </Card>

            {editing && (
                <div
                    className="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 p-4"
                    onClick={() => setEditing(null)}
                >
                    <div
                        className="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl"
                        onClick={(e) => e.stopPropagation()}
                    >
                        <div className="flex items-center justify-between">
                            <h3 className="text-sm font-semibold text-slate-900">{t('invoices.settings.edit')}</h3>
                            <button
                                type="button"
                                onClick={() => setEditing(null)}
                                className="rounded-lg p-1.5 text-slate-400 transition-colors hover:bg-slate-100 hover:text-slate-600"
                                aria-label="Close"
                            >
                                <X className="h-4 w-4" />
                            </button>
                        </div>
                        <form onSubmit={handleUpdate} className="mt-5 space-y-4">
                            <div>
                                <label className="mb-1.5 block text-sm font-medium text-slate-700" htmlFor="edit-name">
                                    {t('common.name')}
                                </label>
                                <Input
                                    id="edit-name"
                                    value={editForm.name}
                                    onChange={(e) =>
                                        setEditForm((f) => ({ ...f, name: e.target.value.toUpperCase() }))
                                    }
                                    invalid={!!editErrors.name}
                                />
                                <FieldError error={editErrors.name} />
                            </div>
                            <div>
                                <label className="mb-1.5 block text-sm font-medium text-slate-700" htmlFor="edit-color">
                                    {t('invoices.settings.color')}
                                </label>
                                <select
                                    id="edit-color"
                                    className={fieldClasses}
                                    value={editForm.color}
                                    onChange={(e) => setEditForm((f) => ({ ...f, color: e.target.value }))}
                                >
                                    {COLORS.map((c) => (
                                        <option key={c} value={c}>
                                            {c}
                                        </option>
                                    ))}
                                </select>
                                <FieldError error={editErrors.color} />
                            </div>
                            <div className="flex justify-end gap-3 border-t border-slate-100 pt-4">
                                <Button type="button" variant="secondary" onClick={() => setEditing(null)}>
                                    {t('common.cancel')}
                                </Button>
                                <Button type="submit">{t('common.save_changes')}</Button>
                            </div>
                        </form>
                    </div>
                </div>
            )}
        </div>
    );
}