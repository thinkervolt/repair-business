import React, { useEffect, useState } from 'react';
import { Link, useNavigate, useSearchParams } from 'react-router-dom';
import { Save, UserRound } from 'lucide-react';
import api from '../../api/client';
import { useI18n } from '../../i18n/I18nContext';
import { getApiError } from '../../utils/format';
import Card from '../../components/ui/Card';
import Button from '../../components/ui/Button';
import Input from '../../components/ui/Input';
import Alert from '../../components/ui/Alert';
import CustomerPicker from '../../components/customers/CustomerPicker';

function FieldError({ error }) {
    return error ? <p className="mt-1 text-xs text-red-600">{error[0]}</p> : null;
}

export default function RepairForm() {
    const navigate = useNavigate();
    const { t } = useI18n();
    const [searchParams] = useSearchParams();
    const [form, setForm] = useState({ target: '', data_request: '' });
    const [customer, setCustomer] = useState(null);
    const [errors, setErrors] = useState({});
    const [message, setMessage] = useState('');
    const [saving, setSaving] = useState(false);
    const [pickerOpen, setPickerOpen] = useState(false);

    useEffect(() => {
        const customerId = searchParams.get('customer');
        if (customerId) {
            api.get(`/customers/${customerId}`)
                .then(({ data }) => setCustomer(data.data.customer))
                .catch(() => setCustomer(null));
        }
    }, [searchParams]);

    const handleSubmit = async (e) => {
        e.preventDefault();
        setErrors({});
        setSaving(true);
        try {
            const payload = { ...form };
            if (customer) payload.customer = customer.id;
            const { data } = await api.post('/repairs', payload);
            navigate(`/repairs/${data.data.id}`);
        } catch (err) {
            const response = err.response;
            if (response && response.data && response.data.errors) {
                setErrors(response.data.errors);
            } else {
                setMessage(getApiError(err));
            }
        } finally {
            setSaving(false);
        }
    };

    return (
        <div className="mx-auto max-w-2xl space-y-6">
            <div>
                <Link to="/repairs" className="text-sm font-medium text-blue-700 hover:underline">
                    &larr; {t('repairs.back')}
                </Link>
                <h1 className="mt-2 text-2xl font-bold tracking-tight text-slate-900">{t('repairs.create')}</h1>
                <p className="mt-1 text-sm text-slate-500">
                    {t('repairs.create_subtitle')}
                </p>
            </div>

            {message && <Alert tone="error">{message}</Alert>}

            <Card className="p-6">
                <form onSubmit={handleSubmit} className="space-y-6">
                    <div>
                        <span className="text-xs font-semibold uppercase tracking-wide text-slate-500">
                            {t('repairs.customer_optional')}
                        </span>
                        <div className="mt-2 flex items-center gap-3 rounded-lg border border-slate-200 bg-slate-50/60 px-4 py-3">
                            {customer ? (
                                <div className="min-w-0 flex-1">
                                    <p className="truncate text-sm font-medium text-slate-800">
                                        {customer.first_name} {customer.last_name || ''}
                                    </p>
                                    <p className="truncate text-xs text-slate-500">
                                        {customer.company || customer.email || t('repairs.customer_number', { id: customer.id })}
                                    </p>
                                </div>
                            ) : (
                                <p className="flex-1 text-sm text-slate-500">
                                    {t('repairs.no_customer_selected')}
                                </p>
                            )}
                            <Button
                                type="button"
                                variant="secondary"
                                size="sm"
                                onClick={() => setPickerOpen(true)}
                            >
                                <UserRound className="h-3.5 w-3.5" />
                                {customer ? t('common.change') : t('common.select')}
                            </Button>
                        </div>
                    </div>

                    <div>
                        <label className="mb-1.5 block text-sm font-medium text-slate-700" htmlFor="target">
                            {t('repairs.target')} *
                        </label>
                        <Input
                            id="target"
                            value={form.target}
                            onChange={(e) => setForm((f) => ({ ...f, target: e.target.value }))}
                            placeholder={t('repairs.target_placeholder')}
                            invalid={!!errors.target}
                        />
                        <FieldError error={errors.target} />
                    </div>

                    <div>
                        <label className="mb-1.5 block text-sm font-medium text-slate-700" htmlFor="request">
                            {t('repairs.request')} *
                        </label>
                        <Input
                            id="request"
                            value={form.data_request}
                            onChange={(e) => setForm((f) => ({ ...f, data_request: e.target.value }))}
                            placeholder={t('repairs.request_placeholder')}
                            invalid={!!errors.data_request}
                        />
                        <FieldError error={errors.data_request} />
                    </div>

                    <div className="flex justify-end gap-3 border-t border-slate-100 pt-5">
                        <Link to="/repairs">
                            <Button type="button" variant="secondary">
                                {t('common.cancel')}
                            </Button>
                        </Link>
                        <Button type="submit" loading={saving}>
                            <Save className="h-4 w-4" />
                            {t('repairs.create')}
                        </Button>
                    </div>
                </form>
            </Card>

            <CustomerPicker
                open={pickerOpen}
                onClose={() => setPickerOpen(false)}
                onSelect={(c) => {
                    setCustomer(c);
                    setPickerOpen(false);
                }}
            />
        </div>
    );
}