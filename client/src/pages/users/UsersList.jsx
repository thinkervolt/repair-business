import React, { useEffect, useState } from 'react';
import { KeyRound, Save, Trash2, UserPlus, X } from 'lucide-react';
import api from '../../api/client';
import { useI18n } from '../../i18n/I18nContext';
import { getApiError } from '../../utils/format';
import Card, { CardHeader, CardBody } from '../../components/ui/Card';
import Button from '../../components/ui/Button';
import Alert from '../../components/ui/Alert';
import Input from '../../components/ui/Input';

function UserRow({ user, notify, onChanged, onResetPassword }) {
    const { t } = useI18n();
    const [form, setForm] = useState({
        name: user.name,
        email: user.email,
        role: user.role,
    });
    const [busy, setBusy] = useState('');

    const push = (setter, newValue) => setForm((f) => ({ ...f, [setter]: newValue }));

    const update = async (e) => {
        e.preventDefault();
        setBusy('update');
        try {
            const { data } = await api.put(`/users/${user.id}`, form);
            notify(data.message);
            onChanged();
        } catch (err) {
            notify(getApiError(err), 'error');
        } finally {
            setBusy('');
        }
    };

    const destroy = async () => {
        if (!window.confirm(t('users.delete_confirm', { name: user.name, email: user.email }))) return;
        setBusy('delete');
        try {
            const { data } = await api.delete(`/users/${user.id}`);
            notify(data.message);
            onChanged();
        } catch (err) {
            notify(getApiError(err), 'error');
        } finally {
            setBusy('');
        }
    };

    return (
        <tr className="border-b border-slate-100 transition-colors last:border-0 hover:bg-slate-50">
            <td className="px-6 py-3">
                <input
                    type="text"
                    value={form.name}
                    onChange={(e) => push('name', e.target.value)}
                    required
                    minLength={2}
                    className="w-full rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-sm text-slate-800 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                />
            </td>
            <td className="px-6 py-3">
                <input
                    type="email"
                    value={form.email}
                    onChange={(e) => push('email', e.target.value)}
                    required
                    className="w-full rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-sm text-slate-800 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                />
            </td>
            <td className="px-6 py-3">
                <select
                    value={form.role}
                    onChange={(e) => push('role', e.target.value)}
                    required
                    className="w-full rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-sm text-slate-800 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                >
                    <option value="user">{t('users.user')}</option>
                    <option value="admin">{t('users.admin')}</option>
                </select>
            </td>
            <td className="px-6 py-3">
                <div className="flex items-center justify-end gap-1.5">
                    <Button
                        variant="secondary"
                        size="sm"
                        type="button"
                        onClick={() => onResetPassword(user)}
                    >
                        <KeyRound className="h-3.5 w-3.5" />
                        {t('users.reset_password')}
                    </Button>
                    <Button variant="secondary" size="sm" loading={busy === 'update'} onClick={update}>
                        <Save className="h-3.5 w-3.5" />
                        {t('users.update')}
                    </Button>
                    <Button variant="danger" size="sm" loading={busy === 'delete'} onClick={destroy}>
                        <Trash2 className="h-3.5 w-3.5" />
                        {t('users.delete')}
                    </Button>
                </div>
            </td>
        </tr>
    );
}

const blank = {
    name: '',
    email: '',
    role: 'user',
    password: '',
    password_confirmation: '',
};

function CreateUserCard({ notify, onCreated }) {
    const { t } = useI18n();
    const [form, setForm] = useState(blank);
    const [errors, setErrors] = useState({});
    const [busy, setBusy] = useState(false);

    const setField = (key) => (e) => setForm((f) => ({ ...f, [key]: e.target.value }));

    const submit = async (e) => {
        e.preventDefault();
        setErrors({});
        setBusy(true);
        try {
            const { data } = await api.post('/auth/register', {
                name: form.name,
                email: form.email,
                password: form.password,
                password_confirmation: form.password_confirmation,
            });
            if (form.role === 'admin' && data.data && data.data.id) {
                await api.put(`/users/${data.data.id}`, {
                    name: form.name,
                    email: form.email,
                    role: 'admin',
                });
            }
            notify(data.message);
            setForm(blank);
            onCreated();
        } catch (err) {
            const response = err.response;
            if (response && response.data && response.data.errors) {
                setErrors(response.data.errors);
            } else {
                notify(getApiError(err), 'error');
            }
        } finally {
            setBusy(false);
        }
    };

    return (
        <Card>
            <CardHeader
                title={t('users.create')}
                subtitle={t('users.create_subtitle')}
            />
            <CardBody>
                <form onSubmit={submit} className="grid gap-5 sm:grid-cols-2">
                    <div>
                        <label className="mb-1.5 block text-sm font-medium text-slate-700" htmlFor="new-name">
                            {t('common.name')} *
                        </label>
                        <Input
                            id="new-name"
                            value={form.name}
                            onChange={setField('name')}
                            placeholder={t('users.name_placeholder')}
                            invalid={!!errors.name}
                        />
                        {errors.name && <p className="mt-1 text-xs text-red-600">{errors.name[0]}</p>}
                    </div>
                    <div>
                        <label className="mb-1.5 block text-sm font-medium text-slate-700" htmlFor="new-email">
                            {t('common.email')} *
                        </label>
                        <Input
                            id="new-email"
                            type="email"
                            value={form.email}
                            onChange={setField('email')}
                            placeholder={t('users.email_placeholder')}
                            invalid={!!errors.email}
                        />
                        {errors.email && <p className="mt-1 text-xs text-red-600">{errors.email[0]}</p>}
                    </div>
                    <div>
                        <label className="mb-1.5 block text-sm font-medium text-slate-700" htmlFor="new-role">
                            {t('users.role')} *
                        </label>
                        <select
                            id="new-role"
                            className="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                            value={form.role}
                            onChange={setField('role')}
                        >
                            <option value="user">{t('users.user')}</option>
                            <option value="admin">{t('users.admin')}</option>
                        </select>
                    </div>
                    <div>
                        <label className="mb-1.5 block text-sm font-medium text-slate-700" htmlFor="new-password">
                            {t('users.password')} *
                        </label>
                        <Input
                            id="new-password"
                            type="password"
                            value={form.password}
                            onChange={setField('password')}
                            placeholder={t('auth.min_8')}
                            invalid={!!errors.password}
                        />
                        {errors.password && (
                            <p className="mt-1 text-xs text-red-600">{errors.password[0]}</p>
                        )}
                    </div>
                    <div className="sm:col-span-2">
                        <label className="mb-1.5 block text-sm font-medium text-slate-700" htmlFor="new-password-confirm">
                            {t('users.confirm_password')} *
                        </label>
                        <Input
                            id="new-password-confirm"
                            type="password"
                            value={form.password_confirmation}
                            onChange={setField('password_confirmation')}
                            placeholder={t('users.repeat_password')}
                            invalid={!!errors.password_confirmation}
                        />
                        {errors.password_confirmation && (
                            <p className="mt-1 text-xs text-red-600">{errors.password_confirmation[0]}</p>
                        )}
                    </div>
                    <div className="flex justify-end sm:col-span-2">
                        <Button type="submit" loading={busy}>
                            <UserPlus className="h-4 w-4" />
                            {t('users.create')}
                        </Button>
                    </div>
                </form>
            </CardBody>
        </Card>
    );
}

function ResetPasswordModal({ user, notify, onChanged, onClose }) {
    const { t } = useI18n();
    const [form, setForm] = useState({ password: '', password_confirmation: '' });
    const [errors, setErrors] = useState({});
    const [busy, setBusy] = useState(false);

    const setField = (key) => (e) => setForm((f) => ({ ...f, [key]: e.target.value }));

    const submit = async (e) => {
        e.preventDefault();
        setErrors({});
        setBusy(true);
        try {
            const { data } = await api.put(`/users/${user.id}/password`, form);
            notify(data.message);
            onChanged();
            onClose();
        } catch (err) {
            const response = err.response;
            if (response && response.data && response.data.errors) {
                setErrors(response.data.errors);
            } else {
                notify(getApiError(err), 'error');
            }
        } finally {
            setBusy(false);
        }
    };

    return (
        <div
            className="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 p-4"
            onClick={onClose}
        >
            <div
                className="w-full max-w-sm rounded-2xl bg-white p-6 shadow-2xl"
                onClick={(e) => e.stopPropagation()}
            >
                <div className="flex items-start justify-between">
                    <div className="flex items-center gap-2">
                        <KeyRound className="h-5 w-5 text-blue-700" />
                        <h3 className="text-lg font-bold text-slate-900">{t('users.reset_password')}</h3>
                    </div>
                    <button
                        type="button"
                        onClick={onClose}
                        className="rounded-lg p-1 text-slate-400 hover:bg-slate-100 hover:text-slate-600"
                    >
                        <X className="h-5 w-5" />
                    </button>
                </div>
                <p className="mt-1 text-sm text-slate-500">
                    {t('users.reset_password_intro', { name: user.name })}
                </p>
                <form onSubmit={submit} className="mt-4 space-y-4">
                    <div>
                        <label className="mb-1.5 block text-sm font-medium text-slate-700" htmlFor={`reset-password-${user.id}`}>
                            {t('profile.new_password')} *
                        </label>
                        <Input
                            id={`reset-password-${user.id}`}
                            type="password"
                            value={form.password}
                            onChange={setField('password')}
                            placeholder={t('auth.min_8')}
                            invalid={!!errors.password}
                            autoFocus
                        />
                        {errors.password && <p className="mt-1 text-xs text-red-600">{errors.password[0]}</p>}
                    </div>
                    <div>
                        <label className="mb-1.5 block text-sm font-medium text-slate-700" htmlFor={`reset-password-confirm-${user.id}`}>
                            {t('users.confirm_password')} *
                        </label>
                        <Input
                            id={`reset-password-confirm-${user.id}`}
                            type="password"
                            value={form.password_confirmation}
                            onChange={setField('password_confirmation')}
                            placeholder={t('users.repeat_password')}
                            invalid={!!errors.password_confirmation}
                        />
                        {errors.password_confirmation && (
                            <p className="mt-1 text-xs text-red-600">
                                {errors.password_confirmation[0]}
                            </p>
                        )}
                    </div>
                    <div className="flex justify-end gap-2 pt-1">
                        <Button type="button" variant="secondary" onClick={onClose}>
                            {t('common.cancel')}
                        </Button>
                        <Button type="submit" loading={busy}>
                            <KeyRound className="h-4 w-4" />
                            {t('users.reset_password')}
                        </Button>
                    </div>
                </form>
            </div>
        </div>
    );
}

export default function UsersList() {
    const { t } = useI18n();
    const [users, setUsers] = useState(null);
    const [error, setError] = useState('');
    const [message, setMessage] = useState('');
    const [resetUser, setResetUser] = useState(null);

    const load = () => {
        api.get('/users')
            .then(({ data }) => setUsers(data.data.users))
            .catch((err) => setError(getApiError(err)));
    };

    useEffect(() => {
        load();
    }, []);

    const notify = (text, tone = 'success') => {
        setMessage(tone === 'success' ? text : '');
        setError(tone === 'error' ? text : '');
        window.scrollTo({ top: 0, behavior: 'smooth' });
    };

    return (
        <div className="space-y-6">
            <div>
                <h1 className="text-2xl font-bold tracking-tight text-slate-900">{t('nav.users')}</h1>
                <p className="mt-1 text-sm text-slate-500">
                    {t('users.subtitle')}
                </p>
            </div>

            {error && <Alert tone="error">{error}</Alert>}
            {message && <Alert tone="success">{message}</Alert>}

            <CreateUserCard notify={notify} onCreated={() => load()} />

            {resetUser && (
                <ResetPasswordModal
                    user={resetUser}
                    notify={notify}
                    onChanged={() => load()}
                    onClose={() => setResetUser(null)}
                />
            )}

            <Card>
                <CardHeader
                    title={t('users.manage')}
                    subtitle={t('users.manage_subtitle')}
                />
                {!users ? (
                    <CardBody>
                        <div className="px-6 py-12 text-center text-sm text-slate-400">{t('common.loading')}</div>
                    </CardBody>
                ) : users.length > 0 ? (
                    <div className="overflow-x-auto">
                        <table className="w-full text-left">
                            <thead>
                                <tr className="border-b border-slate-100 bg-slate-50/60 text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    <th className="px-6 py-3">{t('common.name')}</th>
                                    <th className="px-6 py-3">{t('common.email')}</th>
                                    <th className="px-6 py-3">{t('users.role')}</th>
                                    <th className="px-6 py-3 text-right">{t('common.actions')}</th>
                                </tr>
                            </thead>
                            <tbody>
                                {users.map((user) => (
                                    <UserRow
                                        key={user.id}
                                        user={user}
                                        notify={notify}
                                        onChanged={() => load()}
                                        onResetPassword={setResetUser}
                                    />
                                ))}
                            </tbody>
                        </table>
                    </div>
                ) : (
                    <CardBody>
                        <div className="px-6 py-12 text-center text-sm text-slate-400">
                            {t('users.no_other_users')}
                        </div>
                    </CardBody>
                )}
            </Card>
        </div>
    );
}