import React, { useState } from 'react';
import { UserPlus } from 'lucide-react';
import { useAuth } from '../auth/AuthContext';
import { getApiError } from '../utils/format';
import AuthLayout from './AuthLayout';
import Button from '../components/ui/Button';
import Input from '../components/ui/Input';
import Field from '../components/ui/Field';
import Alert from '../components/ui/Alert';

export default function Register() {
    const { register } = useAuth();
    const [form, setForm] = useState({
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
    });
    const [error, setError] = useState('');
    const [success, setSuccess] = useState('');
    const [submitting, setSubmitting] = useState(false);

    const update = (key) => (e) => setForm((f) => ({ ...f, [key]: e.target.value }));

    const handleSubmit = async (e) => {
        e.preventDefault();
        setError('');
        setSuccess('');
        setSubmitting(true);
        try {
            await register(form);
            setSuccess(
                'User created. They will receive a verification email before they can work.'
            );
            setForm({ name: '', email: '', password: '', password_confirmation: '' });
        } catch (err) {
            setError(getApiError(err));
        } finally {
            setSubmitting(false);
        }
    };

    return (
        <AuthLayout title="Create a user" subtitle="Add a new staff member to your business.">
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
                <Field label="Full name">
                    <Input
                        type="text"
                        placeholder="Jane Smith"
                        value={form.name}
                        onChange={update('name')}
                        required
                        autoFocus
                    />
                </Field>
                <Field label="Email address">
                    <Input
                        type="email"
                        placeholder="jane@business.com"
                        value={form.email}
                        onChange={update('email')}
                        required
                    />
                </Field>
                <div className="grid gap-4 sm:grid-cols-2">
                    <Field label="Password" hint="At least 8 characters">
                        <Input
                            type="password"
                            placeholder="••••••••"
                            value={form.password}
                            onChange={update('password')}
                            required
                            minLength={8}
                        />
                    </Field>
                    <Field label="Confirm password">
                        <Input
                            type="password"
                            placeholder="••••••••"
                            value={form.password_confirmation}
                            onChange={update('password_confirmation')}
                            required
                        />
                    </Field>
                </div>
                <Button type="submit" size="lg" className="w-full" loading={submitting}>
                    <UserPlus className="h-4 w-4" />
                    Create user
                </Button>
            </form>
        </AuthLayout>
    );
}