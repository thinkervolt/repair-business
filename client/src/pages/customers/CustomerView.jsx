import React, { useEffect, useState } from 'react';
import { Link, useNavigate, useParams } from 'react-router-dom';
import { Save, Trash2, Wrench } from 'lucide-react';
import api from '../../api/client';
import { useI18n } from '../../i18n/I18nContext';
import { formatDate, formatMoney, formatPhone, getApiError } from '../../utils/format';
import { toneFor } from '../../utils/colors';
import Card from '../../components/ui/Card';
import Button from '../../components/ui/Button';
import Badge from '../../components/ui/Badge';
import Alert from '../../components/ui/Alert';
import CustomerFields from '../../components/customers/CustomerFields';

function EmptyTable() {
    const { t } = useI18n();
    return <div className="px-6 py-10 text-center text-sm text-slate-400">{t('common.nothing_found')}</div>;
}

export default function CustomerView() {
    const { id } = useParams();
    const navigate = useNavigate();
    const { t } = useI18n();
    const [data, setData] = useState(null);
    const [form, setForm] = useState(null);
    const [errors, setErrors] = useState({});
    const [saving, setSaving] = useState(false);
    const [deleting, setDeleting] = useState(false);
    const [message, setMessage] = useState('');
    const [messageTone, setMessageTone] = useState('success');

    const load = () => {
        api.get(`/customers/${id}`)
            .then(({ data }) => {
                setData(data.data);
                const c = data.data.customer;
                setForm({
                    first_name: c.first_name,
                    last_name: c.last_name,
                    phone: c.phone,
                    email: c.email,
                    address: c.address,
                    city: c.city,
                    state: c.state,
                    zip: c.zip,
                    company: c.company,
                });
            })
            .catch((err) => setMessage(getApiError(err, 'Could not load this customer.')));
    };

    useEffect(() => {
        setMessage('');
        load();
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [id]);

    const notify = (text, tone = 'success') => {
        setMessage(text);
        setMessageTone(tone);
        window.scrollTo({ top: 0, behavior: 'smooth' });
    };

    const handleUpdate = async (e) => {
        e.preventDefault();
        setErrors({});
        setSaving(true);
        try {
            const { data } = await api.put(`/customers/${id}`, form);
            notify(data.message);
            setForm((f) => ({ ...f, ...data.data }));
        } catch (err) {
            const response = err.response;
            if (response && response.data && response.data.errors) {
                setErrors(response.data.errors);
                setMessage(response.data.message);
                setMessageTone('error');
            } else {
                notify(getApiError(err), 'error');
            }
        } finally {
            setSaving(false);
        }
    };

    const handleDelete = async () => {
        if (!window.confirm(t('customers.view.delete_confirm'))) return;
        setDeleting(true);
        try {
            await api.put(`/customers/${id}/delete`);
            navigate('/customers');
        } catch (err) {
            notify(getApiError(err), 'error');
            setDeleting(false);
        }
    };

    if (!data || !form) {
        return <div className="py-12 text-center text-sm text-slate-400">{t('common.loading')}</div>;
    }

    const { customer, repairs, invoices } = data;

    return (
        <div className="space-y-6">
            {message && <Alert tone={messageTone}>{message}</Alert>}

            <div className="flex flex-wrap items-start justify-between gap-3">
                <div>
                    <Link to="/customers" className="text-sm font-medium text-blue-700 hover:underline">
                        &larr; {t('customers.back')}
                    </Link>
                    <h1 className="mt-2 text-2xl font-bold tracking-tight text-slate-900">
                        {customer.first_name} {customer.last_name || ''}
                    </h1>
                    <p className="mt-1 text-sm text-slate-500">
                        {t('customers.view.registered', {
                            id: customer.id,
                            date: formatDate(customer.created_at),
                        })}
                    </p>
                </div>
                <Link
                    to={`/repairs/create?customer=${customer.id}`}
                    className="shrink-0"
                >
                    <Button>
                        <Wrench className="h-4 w-4" />
                        {t('repairs.new')}
                    </Button>
                </Link>
            </div>

            <div className="grid gap-6 lg:grid-cols-3">
                <div className="space-y-6 lg:col-span-2">
                    <Card className="p-6">
                        <h2 className="mb-4 text-sm font-semibold uppercase tracking-wide text-slate-500">
                            {t('customers.view.details')}
                        </h2>
                        <form onSubmit={handleUpdate} className="space-y-6">
                            <CustomerFields value={form} onChange={setForm} errors={errors} />
                            <div className="flex justify-end gap-3 border-t border-slate-100 pt-5">
                                <Button
                                    type="button"
                                    variant="danger"
                                    onClick={handleDelete}
                                    loading={deleting}
                                >
                                    <Trash2 className="h-4 w-4" />
                                    {t('customers.delete')}
                                </Button>
                                <Button type="submit" loading={saving}>
                                    <Save className="h-4 w-4" />
                                    {t('customers.update')}
                                </Button>
                            </div>
                        </form>
                    </Card>
                </div>

                <div className="space-y-4">
                    <Card className="p-5">
                        <h3 className="text-xs font-semibold uppercase tracking-wide text-slate-500">
                            {t('customers.view.contact')}
                        </h3>
                        <dl className="mt-3 space-y-2 text-sm">
                            <div className="flex justify-between gap-3">
                                <dt className="text-slate-400">{t('common.phone')}</dt>
                                <dd className="font-medium text-slate-700">{formatPhone(customer.phone)}</dd>
                            </div>
                            <div className="flex justify-between gap-3">
                                <dt className="text-slate-400">{t('common.email')}</dt>
                                <dd className="font-medium text-slate-700">{customer.email || '—'}</dd>
                            </div>
                            <div className="flex justify-between gap-3">
                                <dt className="text-slate-400">{t('common.company')}</dt>
                                <dd className="font-medium text-slate-700">{customer.company || '—'}</dd>
                            </div>
                        </dl>
                    </Card>
                    <Card className="p-5">
                        <h3 className="text-xs font-semibold uppercase tracking-wide text-slate-500">
                            {t('customers.view.address')}
                        </h3>
                        <p className="mt-3 text-sm text-slate-700">
                            {[customer.address, customer.city, customer.state, customer.zip]
                                .filter(Boolean)
                                .join(', ') || '—'}
                        </p>
                    </Card>
                </div>
            </div>

            <section>
                <h2 className="mb-3 text-lg font-semibold text-slate-900">{t('customers.view.repairs')}</h2>
                <Card className="overflow-hidden">
                    {repairs.data.length > 0 ? (
                        <>
                            <div className="overflow-x-auto">
                                <table className="w-full text-left">
                                    <thead>
                                        <tr className="border-b border-slate-100 bg-slate-50/60 text-xs font-semibold uppercase tracking-wide text-slate-500">
                                            <th className="px-6 py-3">{t('common.id')}</th>
                                            <th className="px-6 py-3">{t('repairs.target')}</th>
                                            <th className="px-6 py-3">{t('repairs.request')}</th>
                                            <th className="px-6 py-3">{t('common.status')}</th>
                                            <th className="px-6 py-3">{t('repairs.priority')}</th>
                                            <th className="px-6 py-3">{t('common.date')}</th>
                                            <th className="px-6 py-3 text-right">{t('common.actions')}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {repairs.data.map((repair) => (
                                            <tr
                                                key={repair.id}
                                                className="border-b border-slate-100 last:border-0 hover:bg-slate-50"
                                            >
                                                <td className="px-6 py-3 text-sm text-slate-700">{repair.id}</td>
                                                <td className="px-6 py-3 text-sm text-slate-700">{repair.target}</td>
                                                <td className="max-w-xs truncate px-6 py-3 text-sm text-slate-600">
                                                    {repair.request}
                                                </td>
                                                <td className="px-6 py-3">
                                                    {repair.status_data && (
                                                        <Badge solid tone={toneFor(repair.status_data.color)}>
                                                            {repair.status_data.name}
                                                        </Badge>
                                                    )}
                                                </td>
                                                <td className="px-6 py-3">
                                                    {repair.priority_data && (
                                                        <Badge solid tone={toneFor(repair.priority_data.color)}>
                                                            {repair.priority_data.name}
                                                        </Badge>
                                                    )}
                                                </td>
                                                <td className="px-6 py-3 text-sm text-slate-600">
                                                    {formatDate(repair.created_at)}
                                                </td>
                                                <td className="px-6 py-3 text-right">
                                                    <Link
                                                        to={`/repairs/${repair.id}`}
                                                        className="text-sm font-medium text-blue-700 hover:underline"
                                                    >
                                                        {t('common.view')}
                                                    </Link>
                                                </td>
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                            </div>
                        </>
                    ) : (
                        <EmptyTable />
                    )}
                </Card>
            </section>

            <section>
                <h2 className="mb-3 text-lg font-semibold text-slate-900">{t('customers.view.invoices')}</h2>
                <Card className="overflow-hidden">
                    {invoices.data.length > 0 ? (
                        <div className="overflow-x-auto">
                            <table className="w-full text-left">
                                <thead>
                                    <tr className="border-b border-slate-100 bg-slate-50/60 text-xs font-semibold uppercase tracking-wide text-slate-500">
                                        <th className="px-6 py-3">{t('common.id')}</th>
                                        <th className="px-6 py-3">{t('common.status')}</th>
                                        <th className="px-6 py-3">{t('common.balance')}</th>
                                        <th className="px-6 py-3">{t('common.date')}</th>
                                        <th className="px-6 py-3 text-right">{t('common.actions')}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {invoices.data.map((invoice) => (
                                        <tr
                                            key={invoice.id}
                                            className="border-b border-slate-100 last:border-0 hover:bg-slate-50"
                                        >
                                            <td className="px-6 py-3 text-sm text-slate-700">{invoice.id}</td>
                                            <td className="px-6 py-3">
                                                {invoice.status_data && (
                                                    <Badge solid tone={toneFor(invoice.status_data.color)}>
                                                        {invoice.status_data.name}
                                                    </Badge>
                                                )}
                                            </td>
                                            <td
                                                className={`px-6 py-3 text-sm font-medium ${
                                                    invoice.balance < 0
                                                        ? 'text-emerald-600'
                                                        : invoice.balance > 0
                                                          ? 'text-red-600'
                                                          : 'text-slate-700'
                                                }`}
                                            >
                                                $ {formatMoney(invoice.balance)}
                                            </td>
                                            <td className="px-6 py-3 text-sm text-slate-600">
                                                {formatDate(invoice.created_at)}
                                            </td>
                                            <td className="px-6 py-3 text-right">
                                                <Link
                                                    to={`/invoices/${invoice.id}`}
                                                    className="text-sm font-medium text-blue-700 hover:underline"
                                                >
                                                    {t('common.view')}
                                                </Link>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    ) : (
                        <EmptyTable />
                    )}
                </Card>
            </section>
        </div>
    );
}