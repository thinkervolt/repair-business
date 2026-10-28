import React, { useState } from 'react';
import { Send } from 'lucide-react';
import api from '../api/client';
import { getApiError } from '../utils/format';
import { useI18n } from '../i18n/I18nContext';
import AuthLayout from './AuthLayout';
import Button from '../components/ui/Button';
import Input from '../components/ui/Input';
import Field from '../components/ui/Field';
import Alert from '../components/ui/Alert';

export default function ForgotPassword() {
    const { t } = useI18n();
    const [email, setEmail] = useState('');
    const [error, setError] = useState('');
    const [success, setSuccess] = useState('');
    const [submitting, setSubmitting] = useState(false);

    const handleSubmit = async (e) => {
        e.preventDefault();
        setError('');
        setSuccess('');
        setSubmitting(true);
        try {
            const { data } = await api.post('/auth/password/email', { email });
            setSuccess(data.message);
        } catch (err) {
            setError(getApiError(err));
        } finally {
            setSubmitting(false);
        }
    };

    return (
        <AuthLayout
            title={t('auth.forgot_title')}
            subtitle={t('auth.forgot_subtitle')}
        >
            {error && (
                <Alert tone="error" className="mb-5">
                    {error}
                </Alert>
            )}
            {success && (
                <Alert tone="success" className="mb-5">
                    {success}
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
                <Button type="submit" size="lg" className="w-full" loading={submitting}>
                    <Send className="h-4 w-4" />
                    {t('auth.send_reset_link')}
                </Button>
            </form>
        </AuthLayout>
    );
}