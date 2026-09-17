import React, { useEffect, useState } from 'react';
import { Info } from 'lucide-react';
import api from '../../api/client';
import { getApiError } from '../../utils/format';
import Card from '../../components/ui/Card';
import Button from '../../components/ui/Button';
import Input from '../../components/ui/Input';
import Alert from '../../components/ui/Alert';

const LANGUAGES = [
    { value: 'en', label: 'English' },
    { value: 'es', label: 'Español' },
];

const fieldClasses =
    'block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20';

export default function SettingsPage() {
    const [profile, setProfile] = useState({ name: '', phone: '', email: '', address: '', terms: '' });
    const [tax, setTax] = useState('0');
    const [language, setLanguage] = useState('en');
    const [languageId, setLanguageId] = useState(null);
    const [loading, setLoading] = useState(true);
    const [saving, setSaving] = useState('');
    const [message, setMessage] = useState('');
    const [error, setError] = useState('');

    const load = () => {
        setLoading(true);
        api.get('/settings')
            .then(({ data }) => {
                const groups = data.data || {};

                const profileFromGroup = (field) =>
                    (groups.business_profile || []).find((s) => s.name === field)?.data || '';

                setProfile({
                    name: profileFromGroup('name'),
                    phone: profileFromGroup('phone'),
                    email: profileFromGroup('email'),
                    address: profileFromGroup('address'),
                    terms: profileFromGroup('terms'),
                });

                const langSetting = (groups.language || [])[0];
                setLanguageId(langSetting ? langSetting.id : null);
                setLanguage(langSetting ? langSetting.data : 'en');

                const taxSetting = (groups.tax || []).find((s) => s.name === 'invoice_tax');
                setTax(taxSetting ? String(taxSetting.data) : '0');
            })
            .catch((err) => setError(getApiError(err)))
            .finally(() => setLoading(false));
    };

    useEffect(() => {
        load();
    }, []);

    const notify = (text, tone = 'success') => {
        setMessage(tone === 'success' ? text : '');
        setError(tone === 'success' ? '' : text);
        window.scrollTo({ top: 0, behavior: 'smooth' });
    };

    const handleSaveProfile = async (e) => {
        e.preventDefault();
        setSaving('profile');
        try {
            const { data } = await api.put('/settings/business-profile', profile);
            notify(data.message);
        } catch (err) {
            notify(getApiError(err), 'error');
        } finally {
            setSaving('');
        }
    };

    const handleSaveLanguage = async (e) => {
        e.preventDefault();
        if (!languageId) return;
        setSaving('language');
        try {
            const { data } = await api.put(`/settings/${languageId}`, { data: language });
            notify(data.message);
        } catch (err) {
            notify(getApiError(err), 'error');
        } finally {
            setSaving('');
        }
    };

    const handleSaveTax = async (e) => {
        e.preventDefault();
        setSaving('tax');
        try {
            const { data } = await api.put('/invoice-settings/tax', { tax: Number(tax) });
            notify(data.message);
            setTax(String(data.data.tax));
        } catch (err) {
            notify(getApiError(err), 'error');
        } finally {
            setSaving('');
        }
    };

    if (loading) {
        return (
            <div className="flex items-center justify-center py-24 text-sm text-slate-400">
                Loading settings…
            </div>
        );
    }

    return (
        <div className="space-y-6">
            <div>
                <h1 className="text-2xl font-bold tracking-tight text-slate-900">Settings</h1>
                <p className="mt-1 text-sm text-slate-500">
                    Manage your business profile, default language, and invoice tax.
                </p>
            </div>

            {error && <Alert tone="error">{error}</Alert>}
            {message && <Alert tone="success">{message}</Alert>}

            <Card className="p-6">
                <div className="mb-5 border-b border-slate-100 pb-4">
                    <h2 className="text-sm font-semibold uppercase tracking-wide text-slate-500">
                        Business profile
                    </h2>
                    <p className="mt-1 text-xs text-slate-400">
                        Used as the sender on invoices and printed reports.
                    </p>
                </div>
                <form onSubmit={handleSaveProfile} className="space-y-4">
                    <div>
                        <label className="mb-1.5 block text-sm font-medium text-slate-700" htmlFor="bp-name">
                            Business name
                        </label>
                        <Input id="bp-name" value={profile.name} onChange={(e) => setProfile((p) => ({ ...p, name: e.target.value }))} />
                    </div>
                    <div className="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label className="mb-1.5 block text-sm font-medium text-slate-700" htmlFor="bp-phone">
                                Phone
                            </label>
                            <Input id="bp-phone" value={profile.phone} onChange={(e) => setProfile((p) => ({ ...p, phone: e.target.value }))} />
                        </div>
                        <div>
                            <label className="mb-1.5 block text-sm font-medium text-slate-700" htmlFor="bp-email">
                                Email
                            </label>
                            <Input id="bp-email" type="email" value={profile.email} onChange={(e) => setProfile((p) => ({ ...p, email: e.target.value }))} />
                        </div>
                    </div>
                    <div>
                        <label className="mb-1.5 block text-sm font-medium text-slate-700" htmlFor="bp-address">
                            Address
                        </label>
                        <Input id="bp-address" value={profile.address} onChange={(e) => setProfile((p) => ({ ...p, address: e.target.value }))} />
                    </div>
                    <div>
                        <label className="mb-1.5 block text-sm font-medium text-slate-700" htmlFor="bp-terms">
                            Invoice terms
                        </label>
                        <textarea
                            id="bp-terms"
                            rows="3"
                            className={fieldClasses}
                            value={profile.terms}
                            onChange={(e) => setProfile((p) => ({ ...p, terms: e.target.value }))}
                        />
                    </div>
                    <div className="flex justify-end border-t border-slate-100 pt-4">
                        <Button type="submit" loading={saving === 'profile'}>
                            Save profile
                        </Button>
                    </div>
                </form>
            </Card>

            <Card className="p-6">
                <div className="mb-5 border-b border-slate-100 pb-4">
                    <h2 className="text-sm font-semibold uppercase tracking-wide text-slate-500">General</h2>
                    <p className="mt-1 text-xs text-slate-400">
                        Default language for the interface and the invoice tax percentage.
                    </p>
                </div>
                <div className="grid gap-6 sm:grid-cols-2">
                    <form onSubmit={handleSaveLanguage} className="space-y-3">
                        <div>
                            <label className="mb-1.5 block text-sm font-medium text-slate-700" htmlFor="lang">
                                Language
                            </label>
                            <select
                                id="lang"
                                className={fieldClasses}
                                value={language}
                                onChange={(e) => setLanguage(e.target.value)}
                            >
                                {LANGUAGES.map((l) => (
                                    <option key={l.value} value={l.value}>
                                        {l.label}
                                    </option>
                                ))}
                            </select>
                        </div>
                        <Button type="submit" loading={saving === 'language'}>
                            Save language
                        </Button>
                    </form>
                    <form onSubmit={handleSaveTax} className="space-y-3">
                        <div>
                            <label className="mb-1.5 block text-sm font-medium text-slate-700" htmlFor="tax">
                                Tax percentage
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
                        <Button type="submit" loading={saving === 'tax'}>
                            Save tax
                        </Button>
                    </form>
                </div>
            </Card>

            <div className="flex items-start gap-2 text-xs text-slate-400">
                <Info className="mt-0.5 h-4 w-4 shrink-0" />
                <p>Settings changes take effect immediately and apply to new invoices and printed reports.</p>
            </div>
        </div>
    );
}