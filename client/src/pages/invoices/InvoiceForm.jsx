import React, { useEffect, useState } from 'react';
import { Link, useNavigate, useSearchParams } from 'react-router-dom';
import { FileText, Save, UserRound, Wrench } from 'lucide-react';
import api from '../../api/client';
import { useI18n } from '../../i18n/I18nContext';
import { formatMoney, getApiError } from '../../utils/format';
import Card from '../../components/ui/Card';
import Button from '../../components/ui/Button';
import Alert from '../../components/ui/Alert';
import CustomerPicker from '../../components/customers/CustomerPicker';

export default function InvoiceForm() {
    const navigate = useNavigate();
    const { t } = useI18n();
    const [searchParams] = useSearchParams();
    const repairId = searchParams.get('repair');
    const [customer, setCustomer] = useState(null);
    const [repair, setRepair] = useState(null);
    const [message, setMessage] = useState('');
    const [creating, setCreating] = useState(false);
    const [pickerOpen, setPickerOpen] = useState(false);

    useEffect(() => {
        if (repairId) {
            api.get(`/repairs/${repairId}`)
                .then(({ data }) => setRepair(data.data.repair))
                .catch(() => {
                    setMessage(t('invoices.could_not_load_repair'));
                });
        }
    }, [repairId]);

    const handleCreate = async (e) => {
        e.preventDefault();
        setCreating(true);
        setMessage('');
        try {
            const payload = {};
            if (repairId) {
                payload.repair = repairId;
            } else if (customer) {
                payload.customer = customer.id;
            }
            const { data } = await api.post('/invoices', payload);
            navigate(`/invoices/${data.data.invoice.id}`);
        } catch (err) {
            setMessage(getApiError(err));
            setCreating(false);
        }
    };

    return (
        <div className="mx-auto max-w-2xl space-y-6">
            <div>
                <Link to="/invoices" className="text-sm font-medium text-blue-700 hover:underline">
                    &larr; {t('invoices.back')}
                </Link>
                <h1 className="mt-2 text-2xl font-bold tracking-tight text-slate-900">{t('invoices.create')}</h1>
                <p className="mt-1 text-sm text-slate-500">
                    {repairId
                        ? t('invoices.create_for_repair')
                        : t('invoices.create_walkin')}
                </p>
            </div>

            {message && <Alert tone="error">{message}</Alert>}
            {repairId && repair && (
                <Card className="p-6">
                    <div className="flex items-start justify-between gap-3">
                        <div className="min-w-0">
                            <div className="flex items-center gap-2">
                                <Wrench className="h-4 w-4 text-slate-400" />
                                <h2 className="text-sm font-semibold text-slate-900">{t('invoices.repair_number', { id: repair.id })}</h2>
                            </div>
                            <p className="mt-1 text-sm text-slate-700">{repair.target}</p>
                            <p className="mt-0.5 text-xs text-slate-500">{repair.request}</p>
                        </div>
                        <div className="shrink-0 text-right">
                            <p className="text-xs text-slate-400">
                                {repair.customer_data
                                    ? `${repair.customer_data.first_name} ${repair.customer_data.last_name || ''}`
                                    : t('invoices.no_customer_assigned')}
                            </p>
                            <p className="mt-1 text-sm font-semibold text-slate-900">
                                {repair.estimate != null ? `$ ${formatMoney(repair.estimate)}` : '—'}
                            </p>
                        </div>
                    </div>
                </Card>
            )}

            <Card className="p-6">
                <form onSubmit={handleCreate} className="space-y-6">
                    {!repairId && (
                        <div>
                            <span className="text-xs font-semibold uppercase tracking-wide text-slate-500">
                                {t('invoices.customer_optional')}
                            </span>
                            <div className="mt-2 flex items-center gap-3 rounded-lg border border-slate-200 bg-slate-50/60 px-4 py-3">
                                {customer ? (
                                    <div className="min-w-0 flex-1">
                                        <p className="truncate text-sm font-medium text-slate-800">
                                            {customer.first_name} {customer.last_name || ''}
                                        </p>
                                        <p className="truncate text-xs text-slate-500">
                                            {customer.company || customer.email || t('invoices.customer_number', { id: customer.id })}
                                        </p>
                                    </div>
                                ) : (
                                    <p className="flex-1 text-sm text-slate-500">
                                        {t('invoices.no_customer_selected')}
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
                    )}

                    <div className="flex justify-end gap-3 border-t border-slate-100 pt-5">
                        <Link to="/invoices">
                            <Button type="button" variant="secondary">
                                {t('common.cancel')}
                            </Button>
                        </Link>
                        <Button type="submit" loading={creating} disabled={!repairId && !customer}>
                            <FileText className="h-4 w-4" />
                            {repairId ? t('invoices.invoice_this_repair') : t('invoices.create_empty')}
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