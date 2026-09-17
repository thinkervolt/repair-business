import React, { useState } from 'react';
import { Link } from 'react-router-dom';
import { BarChart3, Printer } from 'lucide-react';
import api from '../../api/client';
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
    { key: 'invoices', label: 'Invoices' },
    { key: 'repairs', label: 'Repairs' },
    { key: 'payments', label: 'Payments' },
];

const statusTone = (color) => toneFor(color);

export default function ReportsPage() {
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
                <h1 className="text-2xl font-bold tracking-tight text-slate-900">Create Report</h1>
                <p className="mt-1 text-sm text-slate-500">
                    Generate a printable business report for a date range.
                </p>
            </div>

            {error && <Alert tone="error">{error}</Alert>}
            {message && <Alert tone="success">{message}</Alert>}

            <Card>
                <div className="border-b border-slate-100 px-5 py-4">
                    <div className="flex items-center gap-2">
                        <BarChart3 className="h-4 w-4 text-slate-400" />
                        <h2 className="text-sm font-semibold text-slate-900">Report options</h2>
                    </div>
                </div>
                <form onSubmit={handleGenerate} className="px-5 py-4">
                    <div className="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label className="mb-1 block text-xs font-medium text-slate-500">From</label>
                            <Input type="date" value={from} onChange={(e) => setFrom(e.target.value)} />
                        </div>
                        <div>
                            <label className="mb-1 block text-xs font-medium text-slate-500">To</label>
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
                                {mod.label}
                            </label>
                        ))}
                    </div>
                    <div className="mt-5 flex flex-wrap items-center gap-3">
                        <Button type="submit" loading={loading}>
                            Generate report
                        </Button>
                        {result && (
                            <Button type="button" variant="secondary" onClick={handlePrint} loading={printing}>
                                <Printer className="h-4 w-4" />
                                Print
                            </Button>
                        )}
                    </div>
                </form>
            </Card>

            {result && (
                <div className="space-y-6">
                    <div className="flex flex-wrap items-center justify-between gap-3">
                        <h2 className="text-lg font-semibold text-slate-900">
                            Report <span className="font-normal text-slate-500">— {result.report.from} to {result.report.to}</span>
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
    return (
        <Card className="overflow-hidden">
            <div className="border-b border-slate-100 px-5 py-4">
                <h3 className="text-sm font-semibold uppercase tracking-wide text-slate-900">Invoices</h3>
                <div className="mt-2 flex flex-wrap gap-6 text-sm">
                    <span className="text-slate-600">
                        Invoices: <strong>{data.count}</strong>
                    </span>
                    <span className="text-slate-600">
                        Unpaid amount: <strong>$ {formatMoney(data.balance)}</strong>
                    </span>
                    <span className="text-slate-600">
                        Earnings: <strong>$ {formatMoney(data.earnings)}</strong>
                    </span>
                </div>
            </div>
            <div className="overflow-x-auto">
                <table className="w-full text-left">
                    <thead>
                        <tr className="border-b border-slate-100 bg-slate-50/60 text-xs font-semibold uppercase tracking-wide text-slate-500">
                            <th className={th}>Id</th>
                            <th className={th}>Customer</th>
                            <th className={th}>Items</th>
                            <th className={th}>Status</th>
                            <th className={`${th} text-right`}>Balance</th>
                            <th className={`${th} text-right`}>Total</th>
                            <th className={th}>Date</th>
                            <th className={`${th} text-right`}>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        {data.items.length === 0 ? (
                            <tr>
                                <td colSpan={8} className="px-6 py-12 text-center text-sm text-slate-400">
                                    No information to show.
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
                                            View
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
    return (
        <Card className="overflow-hidden">
            <div className="border-b border-slate-100 px-5 py-4">
                <h3 className="text-sm font-semibold uppercase tracking-wide text-slate-900">Repairs</h3>
                <div className="mt-2 text-sm text-slate-600">
                    Repairs: <strong>{data.count}</strong>
                </div>
            </div>
            <div className="overflow-x-auto">
                <table className="w-full text-left">
                    <thead>
                        <tr className="border-b border-slate-100 bg-slate-50/60 text-xs font-semibold uppercase tracking-wide text-slate-500">
                            <th className={th}>Id</th>
                            <th className={th}>Customer</th>
                            <th className={th}>Target</th>
                            <th className={th}>Request</th>
                            <th className={th}>Status</th>
                            <th className={th}>Priority</th>
                            <th className={th}>Date</th>
                            <th className={`${th} text-right`}>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        {data.items.length === 0 ? (
                            <tr>
                                <td colSpan={8} className="px-6 py-12 text-center text-sm text-slate-400">
                                    No information to show.
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
                                            View
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
    return (
        <Card className="overflow-hidden">
            <div className="border-b border-slate-100 px-5 py-4">
                <h3 className="text-sm font-semibold uppercase tracking-wide text-slate-900">Payments</h3>
                <div className="mt-2 flex flex-wrap items-start justify-between gap-4 text-sm">
                    <div className="flex gap-6">
                        <span className="text-slate-600">
                            Payments: <strong>{data.count}</strong>
                        </span>
                        <span className="text-slate-600">
                            Total: <strong>$ {formatMoney(data.total)}</strong>
                        </span>
                    </div>
                    <div className="text-right">
                        <p className="m-0 text-slate-500">Cash: $ {formatMoney(data.total_cash)}</p>
                        <p className="m-0 text-slate-500">Card: $ {formatMoney(data.total_card)}</p>
                        <p className="m-0 text-slate-500">Check: $ {formatMoney(data.total_check)}</p>
                        <p className="m-0 text-slate-500">Other: $ {formatMoney(data.total_other)}</p>
                    </div>
                </div>
            </div>
            <div className="overflow-x-auto">
                <table className="w-full text-left">
                    <thead>
                        <tr className="border-b border-slate-100 bg-slate-50/60 text-xs font-semibold uppercase tracking-wide text-slate-500">
                            <th className={th}>Id</th>
                            <th className={th}>Invoice</th>
                            <th className={`${th} text-right`}>Amount</th>
                            <th className={th}>Method</th>
                            <th className={th}>Reference</th>
                            <th className={th}>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        {data.items.length === 0 ? (
                            <tr>
                                <td colSpan={6} className="px-6 py-12 text-center text-sm text-slate-400">
                                    No information to show.
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