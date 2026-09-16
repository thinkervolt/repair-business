import React, { useEffect, useState } from 'react';
import { Pencil, Plus, Trash2, X } from 'lucide-react';
import api from '../../api/client';
import { formatDate, getApiError } from '../../utils/format';
import Card from '../../components/ui/Card';
import Button from '../../components/ui/Button';
import Alert from '../../components/ui/Alert';
import { useAuth } from '../../auth/AuthContext';

export default function CategoriesList() {
    const [data, setData] = useState(null);
    const [message, setMessage] = useState('');
    const [messageTone, setMessageTone] = useState('success');
    const [error, setError] = useState('');
    const [editing, setEditing] = useState(null);
    const [name, setName] = useState('');
    const [saving, setSaving] = useState(false);
    const { isAdmin } = useAuth();

    const load = () => {
        api.get('/inventory/categories')
            .then(({ data }) => setData(data.data))
            .catch((err) => setError(getApiError(err)));
    };

    useEffect(() => {
        load();
    }, []);

    const notify = (text, tone = 'success') => {
        setMessage(text);
        setMessageTone(tone);
        setError('');
    };

    const openCreate = () => {
        setEditing({ id: null });
        setName('');
        setMessage('');
    };

    const openEdit = (category) => {
        setEditing({ id: category.id });
        setName(category.name);
        setMessage('');
    };

    const close = () => {
        setEditing(null);
        setName('');
        setMessage('');
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        setSaving(true);
        try {
            if (editing.id) {
                const { data } = await api.put(`/inventory/categories/${editing.id}`, { name });
                notify(data.message);
            } else {
                const { data } = await api.post('/inventory/categories', { name });
                notify(data.message);
            }
            close();
            load();
        } catch (err) {
            notify(getApiError(err), 'error');
        } finally {
            setSaving(false);
        }
    };

    const handleDelete = async (category) => {
        if (!window.confirm(`Delete the category "${category.name}"?`)) return;
        try {
            const { data } = await api.delete(`/inventory/categories/${category.id}`);
            notify(data.message);
            load();
        } catch (err) {
            notify(getApiError(err), 'error');
        }
    };

    const categories = data ? data.data : [];

    return (
        <div className="space-y-6">
            <div className="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h1 className="text-2xl font-bold tracking-tight text-slate-900">Categories</h1>
                    <p className="mt-1 text-sm text-slate-500">
                        Group products into categories for easier stock management.
                    </p>
                </div>
                {isAdmin && (
                    <Button onClick={openCreate}>
                        <Plus className="h-4 w-4" />
                        New category
                    </Button>
                )}
            </div>

            {message && <Alert tone={messageTone}>{message}</Alert>}
            {error && <Alert tone="error">{error}</Alert>}

            {editing && isAdmin && (
                <Card className="p-6">
                    <form onSubmit={handleSubmit} className="space-y-4">
                        <div className="flex items-center justify-between">
                            <h2 className="text-sm font-semibold text-slate-900">
                                {editing.id ? 'Edit category' : 'New category'}
                            </h2>
                            <button
                                type="button"
                                onClick={close}
                                className="rounded-lg p-1.5 text-slate-400 transition-colors hover:bg-slate-100 hover:text-slate-600"
                                aria-label="Close"
                            >
                                <X className="h-4 w-4" />
                            </button>
                        </div>
                        <div>
                            <label className="mb-1.5 block text-sm font-medium text-slate-700" htmlFor="cat-name">
                                Name *
                            </label>
                            <input
                                id="cat-name"
                                value={name}
                                onChange={(e) => setName(e.target.value)}
                                placeholder="e.g. Screens, Batteries, Cables"
                                className="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                            />
                        </div>
                        <div className="flex justify-end gap-3">
                            <Button type="button" variant="secondary" onClick={close}>
                                Cancel
                            </Button>
                            <Button type="submit" loading={saving}>
                                <Plus className="h-4 w-4" />
                                {editing.id ? 'Save changes' : 'Create category'}
                            </Button>
                        </div>
                    </form>
                </Card>
            )}

            <Card className="overflow-hidden">
                {!data ? (
                    <div className="px-6 py-12 text-center text-sm text-slate-400">Loading...</div>
                ) : categories.length === 0 ? (
                    <div className="px-6 py-12 text-center text-sm text-slate-400">
                        No categories have been found.
                    </div>
                ) : (
                    <ul className="divide-y divide-slate-100">
                        {categories.map((category) => (
                            <li
                                key={category.id}
                                className="flex items-center justify-between gap-3 px-6 py-3 transition-colors hover:bg-slate-50"
                            >
                                <span className="min-w-0">
                                    <span className="block truncate text-sm font-medium text-slate-800">
                                        {category.name}
                                    </span>
                                    <span className="block text-xs text-slate-400">
                                        Created {formatDate(category.created_at)}
                                    </span>
                                </span>
                                {isAdmin && (
                                    <span className="flex shrink-0 items-center gap-1">
                                        <button
                                            type="button"
                                            onClick={() => openEdit(category)}
                                            className="rounded-lg p-1.5 text-slate-400 transition-colors hover:bg-slate-100 hover:text-slate-600"
                                            aria-label="Edit category"
                                        >
                                            <Pencil className="h-4 w-4" />
                                        </button>
                                        <button
                                            type="button"
                                            onClick={() => handleDelete(category)}
                                            className="rounded-lg p-1.5 text-slate-400 transition-colors hover:bg-red-50 hover:text-red-600"
                                            aria-label="Delete category"
                                        >
                                            <Trash2 className="h-4 w-4" />
                                        </button>
                                    </span>
                                )}
                            </li>
                        ))}
                    </ul>
                )}
            </Card>
        </div>
    );
}