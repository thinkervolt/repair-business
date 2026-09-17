import React, { useState } from 'react';
import { Link } from 'react-router-dom';
import { ClipboardList, Save } from 'lucide-react';
import api from '../../api/client';
import { formatMoney, getApiError } from '../../utils/format';
import Card from '../../components/ui/Card';
import Button from '../../components/ui/Button';
import Input from '../../components/ui/Input';
import Alert from '../../components/ui/Alert';

function today() {
    const now = new Date();
    const mm = String(now.getMonth() + 1).padStart(2, '0');
    const dd = String(now.getDate()).padStart(2, '0');
    return `${now.getFullYear()}-${mm}-${dd}`;
}

const td = 'px-6 py-3 text-sm text-slate-600 align-top';

export default function RegisterReport() {
    const [date, setDate] = useState(today());
    const [cash, setCash] = useState('');
    const [card, setCard] = useState('');
    const [result, setResult] = useState(null);
    const [loading, setLoading] = useState(false);
    const [inserting, setInserting] = useState(false);
    const [error, setError] = useState('');
    const [message, setMessage] = useState('');

    const handleGenerate = async (e) => {
        e.preventDefault();
        setError('');
        setMessage('');
        setLoading(true);
        try {
            const { data } = await api.post('/reports/register', {
                date,
                cash: cash === '' ? 0 : Number(cash),
                card: card === '' ? 0 : Number(card),
            });
            setResult(data.data);
        } catch (err) {
            setError(getApiError(err, 'Could not generate the register report.'));
            setResult(null);
        } finally {
            setLoading(false);
        }
    };

    const handleInsert = async () => {
        setInserting(true);
        try {
            const { data } = await api.post('/reports/register/insert', {
                date: result.date,
                cash: result.cash_diff,
                card: result.card_diff,
            });
            setMessage(data.message);
            setResult(null);
            setCash('');
            setCard('');
        } catch (err) {
            setError(getApiError(err, 'Could not insert the transactions.'));
        } finally {
            setInserting(false);
        }
    };

    return (
        <div className="space-y-6">
            <div>
                <h1 className="text-2xl font-bold tracking-tight text-slate-900">Register Report</h1>
                <p className="mt-1 text-sm text-slate-500">
                    Compare the cash and credit/debit totals recorded in the day's register against the
                    payments in the system, then insert the difference as non-invoiced transactions.
                </p>
            </div>

            {error && <Alert tone="error">{error}</Alert>}
            {message && <Alert tone="success">{message}</Alert>}

            <Card>
                <div className="border-b border-slate-100 px-5 py-4">
                    <div className="flex items-center gap-2">
                        <ClipboardList className="h-4 w-4 text-slate-400" />
                        <h2 className="text-sm font-semibold text-slate-900">Register totals</h2>
                    </div>
                </div>
                <form onSubmit={handleGenerate} className="grid gap-4 px-5 py-4 sm:grid-cols-[1fr_200px_200px_auto] sm:items-end">
                    <div>
                        <label className="mb-1 block text-xs font-medium text-slate-500">Date</label>
                        <Input type="date" value={date} onChange={(e) => setDate(e.target.value)} />
                    </div>
                    <div>
                        <label className="mb-1 block text-xs font-medium text-slate-500">Cash register total</label>
                        <Input
                            type="number"
                            step="0.01"
                            value={cash}
                            onChange={(e) => setCash(e.target.value)}
                            placeholder="$ 0.00"
                        />
                    </div>
                    <div>
                        <label className="mb-1 block text-xs font-medium text-slate-500">Credit / debit total</label>
                        <Input
                            type="number"
                            step="0.01"
                            value={card}
                            onChange={(e) => setCard(e.target.value)}
                            placeholder="$ 0.00"
                        />
                    </div>
                    <div className="flex items-end">
                        <Button type="submit" loading={loading}>
                            Generate
                        </Button>
                    </div>
                </form>
            </Card>

            {result && (
                <div className="space-y-6">
                    <h2 className="text-lg font-semibold text-slate-900">
                        Register Report <span className="font-normal text-slate-500">— {result.date}</span>
                    </h2>

                    <Card className="overflow-hidden">
                        <div className="border-b border-slate-100 px-5 py-4">
                            <h3 className="text-sm font-semibold uppercase tracking-wide text-slate-900">Payments</h3>
                            <div className="mt-2 flex flex-wrap items-start justify-between gap-4 text-sm">
                                <div className="flex gap-6">
                                    <span className="text-slate-600">
                                        Payments: <strong>{result.count}</strong>
                                    </span>
                                    <span className="text-slate-600">
                                        Total: <strong>$ {formatMoney(result.total)}</strong>
                                    </span>
                                </div>
                                <div className="text-right">
                                    <p className="m-0 text-slate-500">Cash: $ {formatMoney(result.total_cash)}</p>
                                    <p className="m-0 text-slate-500">Card: $ {formatMoney(result.total_card)}</p>
                                    <p className="m-0 text-slate-500">Check: $ {formatMoney(result.total_check)}</p>
                                    <p className="m-0 text-slate-500">Other: $ {formatMoney(result.total_other)}</p>
                                </div>
                            </div>
                        </div>
                        <div className="overflow-x-auto">
                            <table className="w-full text-left">
                                <thead>
                                    <tr className="border-b border-slate-100 bg-slate-50/60 text-xs font-semibold uppercase tracking-wide text-slate-500">
                                        <th className="px-6 py-3">Id</th>
                                        <th className="px-6 py-3">Invoice</th>
                                        <th className="px-6 py-3 text-right">Amount</th>
                                        <th className="px-6 py-3">Method</th>
                                        <th className="px-6 py-3">Reference</th>
                                        <th className="px-6 py-3">Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {result.items.length === 0 ? (
                                        <tr>
                                            <td colSpan={6} className="px-6 py-12 text-center text-sm text-slate-400">
                                                No information to show.
                                            </td>
                                        </tr>
                                    ) : (
                                        result.items.map((payment) => (
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

                    <div className="grid gap-4 sm:grid-cols-2">
                        <Card className="p-5">
                            <p className="mb-0 text-sm text-slate-600">
                                Total cash register: <strong>$ {formatMoney(result.cash_register)}</strong>
                            </p>
                            <p className="mt-1 text-sm text-slate-600">
                                Total cash invoices: <strong>$ {formatMoney(result.total_cash)}</strong>
                            </p>
                            <hr className="my-3 border-slate-200" />
                            <p className="font-semibold text-slate-900">
                                No-invoice cash transactions: $ {formatMoney(result.cash_diff)}
                            </p>
                        </Card>
                        <Card className="p-5">
                            <p className="mb-0 text-sm text-slate-600">
                                Total card register: <strong>$ {formatMoney(result.card_register)}</strong>
                            </p>
                            <p className="mt-1 text-sm text-slate-600">
                                Total card invoices: <strong>$ {formatMoney(result.total_card)}</strong>
                            </p>
                            <hr className="my-3 border-slate-200" />
                            <p className="font-semibold text-slate-900">
                                No-invoice card transactions: $ {formatMoney(result.card_diff)}
                            </p>
                        </Card>
                    </div>

                    <Button onClick={handleInsert} loading={inserting} disabled={!result.cash_diff && !result.card_diff}>
                        <Save className="h-4 w-4" />
                        Insert transactions
                    </Button>
                </div>
            )}
        </div>
    );
}