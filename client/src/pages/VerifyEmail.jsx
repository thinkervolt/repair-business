import React, { useEffect, useState } from 'react';
import { Link } from 'react-router-dom';
import { MailCheck, MailX } from 'lucide-react';
import api from '../api/client';
import { getApiError } from '../utils/format';
import AuthLayout from './AuthLayout';
import Alert from '../components/ui/Alert';
import Button from '../components/ui/Button';
import Spinner from '../components/ui/Spinner';
import { useI18n } from '../i18n/I18nContext';

export default function VerifyEmail() {
    const { t } = useI18n();
    const [status, setStatus] = useState('loading');
    const [message, setMessage] = useState('');

    useEffect(() => {
        const params = new URLSearchParams(window.location.search);
        const payload = {
            id: params.get('id'),
            hash: params.get('hash'),
            expires: params.get('expires'),
        };

        if (!payload.id || !payload.hash || !payload.expires) {
            setStatus('error');
            setMessage(t('auth.link_invalid'));
            return;
        }

        api.post('/auth/email/verify', payload)
            .then(({ data }) => {
                setStatus('success');
                setMessage(data.message);
            })
            .catch((err) => {
                setStatus('error');
                setMessage(getApiError(err, t('auth.link_invalid')));
            });
    }, []);

    return (
        <AuthLayout title={t('auth.email_verification')} hideLoginLink>
            {status === 'loading' && (
                <div className="flex items-center justify-center gap-2 py-4 text-slate-500">
                    <Spinner className="h-5 w-5 animate-spin text-blue-600" />
                    {t('auth.verifying')}
                </div>
            )}
            {status === 'success' && (
                <div className="space-y-5">
                    <Alert tone="success">
                        <span className="inline-flex items-center gap-2">
                            <MailCheck className="h-4 w-4" /> {message}
                        </span>
                    </Alert>
                    <Link to="/login">
                        <Button size="lg" className="w-full">
                            {t('auth.go_to_sign_in')}
                        </Button>
                    </Link>
                </div>
            )}
            {status === 'error' && (
                <div className="space-y-5">
                    <Alert tone="error">
                        <span className="inline-flex items-center gap-2">
                            <MailX className="h-4 w-4" /> {message}
                        </span>
                    </Alert>
                    <Link to="/login">
                        <Button variant="secondary" size="lg" className="w-full">
                            {t('auth.back_to_sign_in')}
                        </Button>
                    </Link>
                </div>
            )}
        </AuthLayout>
    );
}