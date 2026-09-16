import React, { useState } from 'react';
import { Link } from 'react-router-dom';
import { KeyRound } from 'lucide-react';
import api from '../api/client';
import { getApiError } from '../utils/format';
import AuthLayout from './AuthLayout';
import Button from '../components/ui/Button';
import Input from '../components/ui/Input';
import Field from '../components/ui/Field';
import Alert from '../components/ui/Alert';

export default function ResetPassword() {
    const params = new URLSearchParams(window.location.search);
    const token = params.get('token') || '';
    const emailFromUrl = params.get('email') || '';

    const [email, setEmail] = useState(emailFromUrl);
    const [password, setPassword] = useState('');
    const [passwordConfirmation, setPasswordConfirmation] = useState('');
    const [error, setError] = useState('');
    const [success, setSuccess] = useState('');
    const [submitting, setSubmitting] = useState(false);

    const handleSubmit = async (e) => {
        e.preventDefault();
        setError('');
        setSuccess('');
        setSubmitting(true);
        try {
            const { data } = await api.post('/auth/password/reset', {
                token,
                email,
                password,
                password_confirmation: passwordConfirmation,
            });
            setSuccess(data.message);
        } catch (err) {
            setError(getApiError(err));
        } finally {
            setSubmitting(false);
        }
    };

    if (success) {
        return (
            <AuthLayout title="Password updated" hideLoginLink>
                <Alert tone="success" className="mb-5">
                    {success}
                </Alert>
                <Link to="/login">
                    <Button size="lg" className="w-full">
                        Go to sign in
                    </Button>
                </Link>
            </AuthLayout>
        );
    }

    return (
        <AuthLayout title="Set a new password" subtitle="Choose a new password for your account.">
            {error && (
                <Alert tone="error" className="mb-5">
                    {error}
                </Alert>
            )}
            <form onSubmit={handleSubmit} className="space-y-4">
                <Field label="Email address">
                    <Input
                        type="email"
                        placeholder="you@business.com"
                        value={email}
                        onChange={(e) => setEmail(e.target.value)}
                        required
                        autoFocus
                    />
                </Field>
                <Field label="New password" hint="At least 8 characters">
                    <Input
                        type="password"
                        placeholder="••••••••"
                        value={password}
                        onChange={(e) => setPassword(e.target.value)}
                        required
                        minLength={8}
                    />
                </Field>
                <Field label="Confirm new password">
                    <Input
                        type="password"
                        placeholder="••••••••"
                        value={passwordConfirmation}
                        onChange={(e) => setPasswordConfirmation(e.target.value)}
                        required
                    />
                </Field>
                <Button type="submit" size="lg" className="w-full" loading={submitting}>
                    <KeyRound className="h-4 w-4" />
                    Update password
                </Button>
            </form>
        </AuthLayout>
    );
}