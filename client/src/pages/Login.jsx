import React, { useState } from 'react';
import { useNavigate, useLocation, Navigate, Link } from 'react-router-dom';
import { Lock } from 'lucide-react';
import { useAuth } from '../auth/AuthContext';
import { useI18n } from '../i18n/I18nContext';
import { getApiError } from '../utils/format';
import AuthLayout from './AuthLayout';
import Button from '../components/ui/Button';
import Input from '../components/ui/Input';
import Field from '../components/ui/Field';
import Alert from '../components/ui/Alert';

export default function Login() {
    const { user, login } = useAuth();
    const { t } = useI18n();
    const navigate = useNavigate();
    const location = useLocation();
    const [email, setEmail] = useState('');
    const [password, setPassword] = useState('');
    const [error, setError] = useState('');
    const [submitting, setSubmitting] = useState(false);

    if (user) {
        return <Navigate to="/dashboard" replace />;
    }

    const handleSubmit = async (e) => {
        e.preventDefault();
        setError('');
        setSubmitting(true);
        try {
            await login(email, password);
            const from = location.state && location.state.from && location.state.from.pathname;
            navigate(from || '/dashboard', { replace: true });
        } catch (err) {
            setError(getApiError(err, t('auth.invalid_credentials')));
        } finally {
            setSubmitting(false);
        }
    };

    return (
        <AuthLayout title={t('auth.welcome_back')} subtitle={t('auth.sign_in_subtitle')} hideLoginLink>
            {error && (
                <Alert tone="error" className="mb-5">
                    {error}
                </Alert>
            )}
            <form onSubmit={handleSubmit} className="space-y-4">
                <Field label={t('auth.email')}>
                    <Input
                        type="email"
                        placeholder="you@business.com"
                        value={email}
                        onChange={(e) => setEmail(e.target.value)}
                        required
                        autoFocus
                    />
                </Field>
                <Field label={t('auth.password')}>
                    <Input
                        type="password"
                        placeholder="••••••••"
                        value={password}
                        onChange={(e) => setPassword(e.target.value)}
                        required
                    />
                </Field>
                <div className="flex items-center justify-end">
                    <Link
                        to="/forgot-password"
                        className="text-sm font-medium text-blue-600 hover:text-blue-700"
                    >
                        {t('auth.forgot_password')}
                    </Link>
                </div>
                <Button type="submit" size="lg" className="w-full" loading={submitting}>
                    <Lock className="h-4 w-4" />
                    {t('auth.sign_in')}
                </Button>
            </form>
        </AuthLayout>
    );
}