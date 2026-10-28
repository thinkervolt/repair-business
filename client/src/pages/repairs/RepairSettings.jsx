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
import { useConfirm } from '../../components/ui/ConfirmAlert';

const GROUPS = ['status', 'priority'];
const COLORS = ['primary', 'secondary', 'success', 'danger', 'warning', 'info'];

function FieldError({ error }) {
    return error ? <p className="mt-1 text-xs text-red-600">{error[0]}</p> : null;
}

const fieldClasses =
    'block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20';

export default function RepairSettings() {
    const { t } = useI18n();
    const { confirm, confirmElement } = useConfirm();
    const [settings, setSettings] = useState([]);
    const [error, setError] = useState('');
    const [message, setMessage] = useState('');
    const [createForm, setCreateForm] = useState({ name: '', group: 'status', color: 'primary' });
    const [createErrors, setCreateErrors] = useState({});
    const [editing, setEditing] = useState(null);
    const [editForm, setEditForm] = useState({ name: '', group: 'status', color: 'primary' });
    const [editErrors, setEditErrors] = useState({});

    const load = () => {
        api.get('/repairs/settings')
            .then(({ data }) => setSettings(data.data))
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
            const { data } = await api.post('/repairs/settings', createForm);
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
            const { data } = await api.put(`/repairs/settings/${editing.id}`, editForm);
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
        const ok = await confirm({
            title: t('common.confirm_title'),
            message: t('repairs.settings.delete_confirm', { name: setting.name }),
            confirmLabel: t('common.delete'),
            cancelLabel: t('common.cancel'),
        });
        if (!ok) return;
        try {
            const { data } = await api.delete(`/repairs/settings/${setting.id}`);
            notify(data.message);
            load();
        } catch (err) {
            notify(getApiError(err), 'error');
        }
    };

    const grouped = GROUPS.map((group) => ({
        group,
        items: settings.filter((s) => s.group === group),
    }));

    return (
        <div className="space-y-6">
            {confirmElement}
            <div>
                <h1 className="text-2xl font-bold tracking-tight text-slate-900">{t('repairs.settings.title')}</h1>
                <p className="mt-1 text-sm text-slate-500">
                    {t('repairs.settings.subtitle')}
                </p>
            </div>

            {error && <Alert tone="error">{error}</Alert>}
            {message && !error && <Alert tone="success">{message}</Alert>}

            <Card className="p-6">
                <h2 className="mb-4 text-sm font-semibold uppercase tracking-wide text-slate-500">
                    {t('repairs.settings.new')}
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
                        <label className="mb-1.5 block text-sm font-medium text-slate-700" htmlFor="new-group">
                            {t('repairs.settings.group')}
                        </label>
                        <select
                            id="new-group"
                            className={fieldClasses}
                            value={createForm.group}
                            onChange={(e) => setCreateForm((f) => ({ ...f, group: e.target.value }))}
                        >
                            {GROUPS.map((g) => (
                                <option key={g} value={g}>
                                    {g}
                                </option>
                            ))}
                        </select>
                        <FieldError error={createErrors.group} />
                    </div>
                    <div>
                        <label className="mb-1.5 block text-sm font-medium text-slate-700" htmlFor="new-color">
                            {t('repairs.settings.color')}
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
                    <div className="sm:col-span-3">
                        <Button type="submit">
                            <Plus className="h-4 w-4" />
                            {t('repairs.settings.create')}
                        </Button>
                    </div>
                </form>
            </Card>

            {grouped.map(({ group, items }) => (
                <Card key={group} className="overflow-hidden">
                    <div className="border-b border-slate-100 px-6 py-4">
                        <h2 className="text-sm font-semibold uppercase tracking-wide text-slate-500">
                            {group === 'status' ? t('repairs.settings.statuses') : t('repairs.settings.priorities')}
                        </h2>
                    </div>
                    {items.length === 0 ? (
                        <div className="px-6 py-8 text-center text-sm text-slate-400">
                            {t('repairs.settings.empty')}
                        </div>
                    ) : (
                        <ul className="divide-y divide-slate-100">
                            {items.map((setting) => (
                                <li
                                    key={setting.id}
                                    className="flex items-center justify-between gap-3 px-6 py-3"
                                >
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
            ))}

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
                            <h3 className="text-sm font-semibold text-slate-900">{t('repairs.settings.edit')}</h3>
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
                            <div className="grid grid-cols-2 gap-4">
                                <div>
                                    <label className="mb-1.5 block text-sm font-medium text-slate-700" htmlFor="edit-group">
                                        {t('repairs.settings.group')}
                                    </label>
                                    <select
                                        id="edit-group"
                                        className={fieldClasses}
                                        value={editForm.group}
                                        onChange={(e) => setEditForm((f) => ({ ...f, group: e.target.value }))}
                                    >
                                        {GROUPS.map((g) => (
                                            <option key={g} value={g}>
                                                {g}
                                            </option>
                                        ))}
                                    </select>
                                    <FieldError error={editErrors.group} />
                                </div>
                                <div>
                                    <label className="mb-1.5 block text-sm font-medium text-slate-700" htmlFor="edit-color">
                                        {t('repairs.settings.color')}
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