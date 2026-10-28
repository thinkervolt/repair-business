import React, { useEffect, useState } from 'react';
import { KeyRound, UserRound } from 'lucide-react';
import api, { setToken } from '../../api/client';
import { useI18n } from '../../i18n/I18nContext';
import { getApiError } from '../../utils/format';
import Card, { CardHeader, CardBody } from '../../components/ui/Card';
import Button from '../../components/ui/Button';
import Input from '../../components/ui/Input';
import Alert from '../../components/ui/Alert';

export default function Profile() {
    const { t } = useI18n();
    const [profile, setProfile] = useState(null);
    const [error, setError] = useState('');
    const [message, setMessage] = useState('');
    const [form, setForm] = useState({ current_password: '', password: '', password_confirmation: '' });
    const [fieldErrors, setFieldErrors] = useState({});
    const [saving, setSaving] = useState(false);

    useEffect(() => {
        api.get('/users/profile')
            .then(({ data }) => setProfile(data.data.user))
            .catch((err) => setError(getApiError(err)));
    }, []);

    const handleSubmit = async (e) => {
        e.preventDefault();
        setFieldErrors({});
        setError('');
        setMessage('');
        setSaving(true);
        try {
            const { data } = await api.put('/users/profile/password', form);
            setMessage(data.message);
            setTimeout(() => {
                setToken(null);
                window.location.assign('/login');
            }, 1500);
        } catch (err) {
            const response = err.response;
            if (response && response.data && response.data.errors) {
                setFieldErrors(response.data.errors);
            } else {
                setError(getApiError(err));
            }
        } finally {
            setSaving(false);
        }
    };

    if (!profile) {
        return (
            <div className="space-y-6">
                <div>
                    <h1 className="text-2xl font-bold tracking-tight text-slate-900">{t('profile.title')}</h1>
                    <p className="mt-1 text-sm text-slate-500">{t('profile.subtitle')}</p>
                </div>
                {error && <Alert tone="error">{error}</Alert>}
                <Card className="overflow-hidden">
                    <div className="px-6 py-12 text-center text-sm text-slate-400">{t('common.loading')}</div>
                </Card>
            </div>
        );
    }

    return (
        <div className="space-y-6">
            <div>
                <h1 className="text-2xl font-bold tracking-tight text-slate-900">{t('profile.title')}</h1>
                <p className="mt-1 text-sm text-slate-500">{t('profile.subtitle')}</p>
            </div>

            {error && <Alert tone="error">{error}</Alert>}
            {message && <Alert tone="success">{message}</Alert>}

            <Card>
                <CardHeader
                    title={t('profile.account')}
                    subtitle={t('profile.account_subtitle')}
                />
                <CardBody>
                    <dl className="grid gap-4 sm:grid-cols-3">
                        <div>
                            <dt className="text-xs font-medium uppercase tracking-wide text-slate-400">{t('common.name')}</dt>
                            <dd className="mt-1 flex items-center gap-1.5 text-sm font-medium text-slate-700">
                                <UserRound className="h-4 w-4 text-slate-400" />
                                {profile.name}
                            </dd>
                        </div>
                        <div>
                            <dt className="text-xs font-medium uppercase tracking-wide text-slate-400">{t('common.email')}</dt>
                            <dd className="mt-1 text-sm font-medium text-slate-700">{profile.email}</dd>
                        </div>
                        <div>
                            <dt className="text-xs font-medium uppercase tracking-wide text-slate-400">{t('users.role')}</dt>
                            <dd className="mt-1 text-sm font-medium text-slate-700">
                                <span className="rounded-full bg-blue-50 px-2.5 py-0.5 text-xs font-semibold text-blue-700">
                                    {profile.role === 'admin' ? t('users.admin') : t('users.user')}
                                </span>
                            </dd>
                        </div>
                    </dl>
                </CardBody>
            </Card>

            <Card>
                <CardHeader
                    title={t('profile.password')}
                    subtitle={t('profile.password_subtitle')}
                />
                <CardBody>
                    <form onSubmit={handleSubmit} className="grid max-w-lg gap-4">
                        <div>
                            <label className="mb-1 block text-xs font-medium text-slate-500">{t('profile.current_password')}</label>
                            <Input
                                type="password"
                                autoComplete="current-password"
                                value={form.current_password}
                                onChange={(e) => setForm((f) => ({ ...f, current_password: e.target.value }))}
                                required
                                invalid={!!fieldErrors.current_password}
                            />
                            {fieldErrors.current_password && (
                                <p className="mt-1 text-xs text-red-600">{fieldErrors.current_password[0]}</p>
                            )}
                        </div>
                        <div>
                            <label className="mb-1 block text-xs font-medium text-slate-500">{t('profile.new_password')}</label>
                            <Input
                                type="password"
                                autoComplete="new-password"
                                value={form.password}
                                onChange={(e) => setForm((f) => ({ ...f, password: e.target.value }))}
                                required
                                invalid={!!fieldErrors.password}
                            />
                            {fieldErrors.password && (
                                <p className="mt-1 text-xs text-red-600">{fieldErrors.password[0]}</p>
                            )}
                        </div>
                        <div>
                            <label className="mb-1 block text-xs font-medium text-slate-500">{t('profile.confirm_password')}</label>
                            <Input
                                type="password"
                                autoComplete="new-password"
                                value={form.password_confirmation}
                                onChange={(e) => setForm((f) => ({ ...f, password_confirmation: e.target.value }))}
                                required
                                invalid={!!fieldErrors.password_confirmation}
                            />
                            {fieldErrors.password_confirmation && (
                                <p className="mt-1 text-xs text-red-600">{fieldErrors.password_confirmation[0]}</p>
                            )}
                        </div>
                        <div>
                            <Button type="submit" loading={saving}>
                                <KeyRound className="h-4 w-4" />
                                {t('auth.update_password')}
                            </Button>
                        </div>
                    </form>
                </CardBody>
            </Card>
        </div>
    );
}