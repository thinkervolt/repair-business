import React, { useState } from 'react';
import { Link } from 'react-router-dom';
import { BarChart3, Printer } from 'lucide-react';
import api from '../../api/client';
import { useI18n } from '../../i18n/I18nContext';
import { formatDate, formatMoney, formatPhone, getApiError } from '../../utils/format';
import { toneFor } from '../../utils/colors';
import Card from '../../components/ui/Card';
import Button from '../../components/ui/Button';
import Input from '../../components/ui/Input';
import Alert from '../../components/ui/Alert';
import Badge from '../../components/ui/Badge';

function today() {
    const now = new Date();
    const mm = String(now.getMonth() + 1).padStart(2, '0');
    const dd = String(now.getDate()).padStart(2, '0');
    return `${now.getFullYear()}-${mm}-${dd}`;
}

const MODULES = [
    { key: 'invoices', labelKey: 'nav.invoices' },
    { key: 'repairs', labelKey: 'nav.repairs' },
    { key: 'payments', labelKey: 'nav.payments' },
];

const statusTone = (color) => toneFor(color);

export default function ReportsPage() {
    const { t } = useI18n();
    const [from, setFrom] = useState(today());
    const [to, setTo] = useState(today());
    const [include, setInclude] = useState({ invoices: true, repairs: true, payments: true });
    const [result, setResult] = useState(null);
    const [loading, setLoading] = useState(false);
    const [printing, setPrinting] = useState(false);
    const [error, setError] = useState('');
    const [message, setMessage] = useState('');

    const toggle = (key) => setInclude((i) => ({ ...i, [key]: !i[key] }));

    const handleGenerate = async (e) => {
        e.preventDefault();
        setError('');
        setMessage('');
        setLoading(true);
        try {
            const { data } = await api.post('/reports/preview', {
                from,
                to,
                invoices: include.invoices,
                repairs: include.repairs,
                payments: include.payments,
            });
            setResult(data.data);
        } catch (err) {
            setError(getApiError(err, 'Could not generate the report.'));
            setResult(null);
        } finally {
            setLoading(false);
        }
    };

    const handlePrint = async () => {
        setPrinting(true);
        try {
            const params = new URLSearchParams({
                from,
                to,
                invoices: include.invoices ? '1' : '0',
                repairs: include.repairs ? '1' : '0',
                payments: include.payments ? '1' : '0',
            });
            const { data } = await api.get(`/reports/print?${params}`, { responseType: 'blob' });
            const url = window.URL.createObjectURL(data);
            window.open(url, '_blank');
            setTimeout(() => window.URL.revokeObjectURL(url), 30000);
        } catch (err) {
            setError(getApiError(err, 'Could not print the report.'));
        } finally {
            setPrinting(false);
        }
    };

    const showCard = (key) => result && result.report && result.report[key];

    return (
        <div className="space-y-6">
            <div>
                <h1 className="text-2xl font-bold tracking-tight text-slate-900">{t('nav.create_report')}</h1>
                <p className="mt-1 text-sm text-slate-500">
                    {t('reports.subtitle')}
                </p>
            </div>

            {error && <Alert tone="error">{error}</Alert>}
            {message && <Alert tone="success">{message}</Alert>}

            <Card>
                <div className="border-b border-slate-100 px-5 py-4">
                    <div className="flex items-center gap-2">
                        <BarChart3 className="h-4 w-4 text-slate-400" />
                        <h2 className="text-sm font-semibold text-slate-900">{t('reports.options')}</h2>
                    </div>
                </div>
                <form onSubmit={handleGenerate} className="px-5 py-4">
                    <div className="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label className="mb-1 block text-xs font-medium text-slate-500">{t('reports.from')}</label>
                            <Input type="date" value={from} onChange={(e) => setFrom(e.target.value)} />
                        </div>
                        <div>
                            <label className="mb-1 block text-xs font-medium text-slate-500">{t('reports.to')}</label>
                            <Input type="date" value={to} onChange={(e) => setTo(e.target.value)} />
                        </div>
                    </div>
                    <div className="mt-4 flex flex-wrap gap-6">
                        {MODULES.map((mod) => (
                            <label key={mod.key} className="flex items-center gap-2 text-sm text-slate-700">
                                <input
                                    type="checkbox"
                                    checked={include[mod.key]}
                                    onChange={() => toggle(mod.key)}
                                    className="h-4 w-4 rounded border-slate-300 text-blue-700 focus:ring-blue-500"
                                />
                                {mod.labelKey ? t(mod.labelKey) : mod.label}
                            </label>
                        ))}
                    </div>
                    <div className="mt-5 flex flex-wrap items-center gap-3">
                        <Button type="submit" loading={loading}>
                            {t('reports.generate')}
                        </Button>
                        {result && (
                            <Button type="button" variant="secondary" onClick={handlePrint} loading={printing}>
                                <Printer className="h-4 w-4" />
                                {t('common.print')}
                            </Button>
                        )}
                    </div>
                </form>
            </Card>

            {result && (
                <div className="space-y-6">
                    <div className="flex flex-wrap items-center justify-between gap-3">
                        <h2 className="text-lg font-semibold text-slate-900">
                            {t('reports.report_range', { from: result.report.from, to: result.report.to })}
                        </h2>
                    </div>

                    {showCard('invoices') && <InvoicesSection data={result.invoices} />}
                    {showCard('repairs') && <RepairsSection data={result.repairs} />}
                    {showCard('payments') && <PaymentsSection data={result.payments} />}
                </div>
            )}
        </div>
    );
}

const th = 'px-6 py-3';
const td = 'px-6 py-3 text-sm text-slate-600 align-top';

function InvoicesSection({ data }) {
    const { t } = useI18n();
    return (
        <Card className="overflow-hidden">
            <div className="border-b border-slate-100 px-5 py-4">
                <h3 className="text-sm font-semibold uppercase tracking-wide text-slate-900">{t('nav.invoices')}</h3>
                <div className="mt-2 flex flex-wrap gap-6 text-sm">
                    <span className="text-slate-600">
                        {t('reports.invoices_count', { count: data.count })}
                    </span>
                    <span className="text-slate-600">
                        {t('reports.unpaid_amount', { amount: formatMoney(data.balance) })}
                    </span>
                    <span className="text-slate-600">
                        {t('reports.earnings', { amount: formatMoney(data.earnings) })}
                    </span>
                </div>
            </div>
            <div className="overflow-x-auto">
                <table className="w-full text-left">
                    <thead>
                        <tr className="border-b border-slate-100 bg-slate-50/60 text-xs font-semibold uppercase tracking-wide text-slate-500">
                            <th className={th}>{t('common.id')}</th>
                            <th className={th}>{t('common.customer')}</th>
                            <th className={th}>{t('invoices.items')}</th>
                            <th className={th}>{t('common.status')}</th>
                            <th className={`${th} text-right`}>{t('common.balance')}</th>
                            <th className={`${th} text-right`}>{t('common.total')}</th>
                            <th className={th}>{t('common.date')}</th>
                            <th className={`${th} text-right`}>{t('common.actions')}</th>
                        </tr>
                    </thead>
                    <tbody>
                        {data.items.length === 0 ? (
                            <tr>
                                <td colSpan={8} className="px-6 py-12 text-center text-sm text-slate-400">
                                    {t('reports.no_info')}
                                </td>
                            </tr>
                        ) : (
                            data.items.map((invoice) => (
                                <tr
                                    key={invoice.id}
                                    className="border-b border-slate-100 transition-colors last:border-0 hover:bg-slate-50"
                                >
                                    <td className={td}>{invoice.id}</td>
                                    <td className={td}>
                                        <p className="m-0">{invoice.customer_name}</p>
                                        <p className="m-0 text-xs">{formatPhone(invoice.customer_phone)}</p>
                                        <p className="m-0 text-xs">{invoice.customer_email}</p>
                                    </td>
                                    <td className={td}>
                                        {invoice.items.map((item) => (
                                            <div key={item.id} className="mb-2 last:mb-0">
                                                <p className="m-0">{item.name}</p>
                                                <p className="m-0 text-xs">{item.description}</p>
                                                <p className="m-0 text-xs">{item.sub_description}</p>
                                            </div>
                                        ))}
                                    </td>
                                    <td className={td}>
                                        {invoice.status_data ? (
                                            <Badge solid tone={statusTone(invoice.status_data.color)}>
                                                {invoice.status_data.name}
                                            </Badge>
                                        ) : null}
                                    </td>
                                    <td className={`${td} text-right`}>
                                        <span
                                            className={
                                                invoice.balance < 0
                                                    ? 'font-medium text-emerald-600'
                                                    : invoice.balance > 0
                                                      ? 'font-medium text-red-600'
                                                      : ''
                                            }
                                        >
                                            $ {formatMoney(invoice.balance)}
                                        </span>
                                    </td>
                                    <td className={`${td} text-right`}>$ {formatMoney(invoice.total)}</td>
                                    <td className={td}>{formatDate(invoice.created_at)}</td>
                                    <td className={`${td} text-right`}>
                                        <Link
                                            to={`/invoices/${invoice.id}`}
                                            className="text-sm font-medium text-blue-700 hover:underline"
                                        >
                                            {t('common.view')}
                                        </Link>
                                    </td>
                                </tr>
                            ))
                        )}
                    </tbody>
                </table>
            </div>
        </Card>
    );
}

function RepairsSection({ data }) {
    const { t } = useI18n();
    return (
        <Card className="overflow-hidden">
            <div className="border-b border-slate-100 px-5 py-4">
                <h3 className="text-sm font-semibold uppercase tracking-wide text-slate-900">{t('nav.repairs')}</h3>
                <div className="mt-2 text-sm text-slate-600">
                    {t('reports.repairs_count', { count: data.count })}
                </div>
            </div>
            <div className="overflow-x-auto">
                <table className="w-full text-left">
                    <thead>
                        <tr className="border-b border-slate-100 bg-slate-50/60 text-xs font-semibold uppercase tracking-wide text-slate-500">
                            <th className={th}>{t('common.id')}</th>
                            <th className={th}>{t('common.customer')}</th>
                            <th className={th}>{t('repairs.target')}</th>
                            <th className={th}>{t('repairs.request')}</th>
                            <th className={th}>{t('common.status')}</th>
                            <th className={th}>{t('repairs.priority')}</th>
                            <th className={th}>{t('common.date')}</th>
                            <th className={`${th} text-right`}>{t('common.actions')}</th>
                        </tr>
                    </thead>
                    <tbody>
                        {data.items.length === 0 ? (
                            <tr>
                                <td colSpan={8} className="px-6 py-12 text-center text-sm text-slate-400">
                                    {t('reports.no_info')}
                                </td>
                            </tr>
                        ) : (
                            data.items.map((repair) => (
                                <tr
                                    key={repair.id}
                                    className="border-b border-slate-100 transition-colors last:border-0 hover:bg-slate-50"
                                >
                                    <td className={td}>{repair.id}</td>
                                    <td className={td}>
                                        {repair.customer_data ? (
                                            <>
                                                <p className="m-0">
                                                    {repair.customer_data.first_name} {repair.customer_data.last_name}
                                                </p>
                                                <p className="m-0 text-xs">{formatPhone(repair.customer_data.phone)}</p>
                                                <p className="m-0 text-xs">{repair.customer_data.email}</p>
                                            </>
                                        ) : (
                                            <span className="text-slate-400">—</span>
                                        )}
                                    </td>
                                    <td className={td}>{repair.target}</td>
                                    <td className={td}>{repair.request}</td>
                                    <td className={td}>
                                        {repair.status_data ? (
                                            <Badge solid tone={statusTone(repair.status_data.color)}>
                                                {repair.status_data.name}
                                            </Badge>
                                        ) : null}
                                    </td>
                                    <td className={td}>
                                        {repair.priority_data ? (
                                            <Badge solid tone={statusTone(repair.priority_data.color)}>
                                                {repair.priority_data.name}
                                            </Badge>
                                        ) : null}
                                    </td>
                                    <td className={td}>{formatDate(repair.created_at)}</td>
                                    <td className={`${td} text-right`}>
                                        <Link
                                            to={`/repairs/${repair.id}`}
                                            className="text-sm font-medium text-blue-700 hover:underline"
                                        >
                                            {t('common.view')}
                                        </Link>
                                    </td>
                                </tr>
                            ))
                        )}
                    </tbody>
                </table>
            </div>
        </Card>
    );
}

function PaymentsSection({ data }) {
    const { t } = useI18n();
    return (
        <Card className="overflow-hidden">
            <div className="border-b border-slate-100 px-5 py-4">
                <h3 className="text-sm font-semibold uppercase tracking-wide text-slate-900">{t('nav.payments')}</h3>
                <div className="mt-2 flex flex-wrap items-start justify-between gap-4 text-sm">
                    <div className="flex gap-6">
                        <span className="text-slate-600">
                            {t('reports.payments_count', { count: data.count })}
                        </span>
                        <span className="text-slate-600">
                            {t('reports.total_count', { amount: formatMoney(data.total) })}
                        </span>
                    </div>
                    <div className="text-right">
                        <p className="m-0 text-slate-500">{t('reports.cash', { amount: formatMoney(data.total_cash) })}</p>
                        <p className="m-0 text-slate-500">{t('reports.card', { amount: formatMoney(data.total_card) })}</p>
                        <p className="m-0 text-slate-500">{t('reports.check', { amount: formatMoney(data.total_check) })}</p>
                        <p className="m-0 text-slate-500">{t('reports.other', { amount: formatMoney(data.total_other) })}</p>
                    </div>
                </div>
            </div>
            <div className="overflow-x-auto">
                <table className="w-full text-left">
                    <thead>
                        <tr className="border-b border-slate-100 bg-slate-50/60 text-xs font-semibold uppercase tracking-wide text-slate-500">
                            <th className={th}>{t('common.id')}</th>
                            <th className={th}>{t('payments.invoice')}</th>
                            <th className={`${th} text-right`}>{t('common.amount')}</th>
                            <th className={th}>{t('common.method')}</th>
                            <th className={th}>{t('common.reference')}</th>
                            <th className={th}>{t('common.date')}</th>
                        </tr>
                    </thead>
                    <tbody>
                        {data.items.length === 0 ? (
                            <tr>
                                <td colSpan={6} className="px-6 py-12 text-center text-sm text-slate-400">
                                    {t('reports.no_info')}
                                </td>
                            </tr>
                        ) : (
                            data.items.map((payment) => (
                                <tr
                                    key={payment.id}
                                    className="border-b border-slate-100 transition-colors last:border-0 hover:bg-slate-50"
                                >
                                    <td className={td}>{payment.id}</td>
                                    <td className={td}>
                                        {payment.invoice ? (
                                            <Link
                                                to={`/invoices/${payment.invoice}`}
                                                className="font-medium text-blue-700 hover:underline"
                                            >
                                                #{payment.invoice}
                                            </Link>
                                        ) : (
                                            <span className="text-slate-400">—</span>
                                        )}
                                    </td>
                                    <td className={`${td} text-right`}>$ {formatMoney(payment.amount)}</td>
                                    <td className={`${td} uppercase`}>{payment.method}</td>
                                    <td className={td}>{payment.ref}</td>
                                    <td className={td}>{formatDate(payment.created_at)}</td>
                                </tr>
                            ))
                        )}
                    </tbody>
                </table>
            </div>
        </Card>
    );
}