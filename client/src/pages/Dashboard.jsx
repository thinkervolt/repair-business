import React, { useEffect, useState } from 'react';
import { Link } from 'react-router-dom';
import {
    CartesianGrid,
    Line,
    LineChart,
    ResponsiveContainer,
    Tooltip,
    XAxis,
    YAxis,
} from 'recharts';
import { CirclePlus, DollarSign, Receipt, Wrench } from 'lucide-react';
import api from '../api/client';
import { formatMoney, getApiError } from '../utils/format';
import Card from '../components/ui/Card';
import Button from '../components/ui/Button';
import Alert from '../components/ui/Alert';
import Spinner from '../components/ui/Spinner';

const MONTHS = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

function StatCard({ label, value, prefix, icon: Icon, tone, to }) {
    const tones = {
        emerald: 'bg-emerald-50 text-emerald-600',
        slate: 'bg-slate-100 text-slate-600',
        blue: 'bg-blue-50 text-blue-600',
    };
    const body = (
        <>
            <div className="flex items-start justify-between gap-3">
                <div className="min-w-0">
                    <p className="text-[13px] font-medium text-slate-500">{label}</p>
                    <p className="mt-1.5 text-2xl font-bold tracking-tight text-slate-900">
                        {prefix && <span className="mr-1 text-slate-400">{prefix}</span>}
                        {value}
                    </p>
                </div>
                <span className={`flex h-11 w-11 shrink-0 items-center justify-center rounded-xl ${tones[tone]}`}>
                    <Icon className="h-5 w-5" />
                </span>
            </div>
        </>
    );

    const wrap = (
        <Card className="p-5 transition-shadow hover:shadow-md">
            {body}
        </Card>
    );

    if (to) {
        return <Link to={to} className="group">{wrap}</Link>;
    }
    return wrap;
}

function EarningsChart({ current, past, year, lastYear }) {
    const data = MONTHS.map((month, i) => ({
        month,
        [year]: Number(current[MONTHS[i].toLowerCase()] || 0),
        [lastYear]: Number(past[MONTHS[i].toLowerCase()] || 0),
    }));

    return (
        <Card>
            <div className="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 px-6 py-4">
                <h3 className="text-sm font-semibold text-slate-900">Earnings overview</h3>
                <div className="flex items-center gap-4 text-xs text-slate-500">
                    <span className="flex items-center gap-1.5">
                        <span className="h-2 w-2 rounded-full bg-blue-600" /> {year}
                    </span>
                    <span className="flex items-center gap-1.5">
                        <span className="h-2 w-2 rounded-full bg-slate-300" /> {lastYear}
                    </span>
                </div>
            </div>
            <div className="h-72 p-4">
                <ResponsiveContainer width="100%" height="100%">
                    <LineChart data={data} margin={{ top: 10, right: 10, left: 0, bottom: 0 }}>
                        <CartesianGrid strokeDasharray="3 3" stroke="#e2e8f0" vertical={false} />
                        <XAxis
                            dataKey="month"
                            tick={{ fontSize: 12, fill: '#64748b' }}
                            axisLine={{ stroke: '#e2e8f0' }}
                            tickLine={false}
                            interval="preserveStartEnd"
                        />
                        <YAxis
                            tick={{ fontSize: 12, fill: '#64748b' }}
                            axisLine={false}
                            tickLine={false}
                            tickFormatter={(v) => `$${v}`}
                            width={60}
                        />
                        <Tooltip
                            formatter={(value, name) => [`$${formatMoney(value)}`, name]}
                            contentStyle={{
                                borderRadius: 12,
                                border: '1px solid #e2e8f0',
                                fontSize: 12,
                                boxShadow: '0 8px 16px rgb(15 23 42 / 0.08)',
                            }}
                        />
                        <Line
                            type="monotone"
                            dataKey={String(year)}
                            stroke="#4f46e5"
                            strokeWidth={2}
                            dot={false}
                            activeDot={{ r: 4 }}
                        />
                        <Line
                            type="monotone"
                            dataKey={String(lastYear)}
                            stroke="#cbd5e1"
                            strokeWidth={2}
                            dot={false}
                            activeDot={{ r: 4 }}
                        />
                    </LineChart>
                </ResponsiveContainer>
            </div>
        </Card>
    );
}

export default function Dashboard() {
    const [stats, setStats] = useState(null);
    const [error, setError] = useState('');

    useEffect(() => {
        api.get('/dashboard')
            .then(({ data }) => setStats(data.data))
            .catch((err) => setError(getApiError(err)));
    }, []);

    const year = new Date().getFullYear();
    const lastYear = year - 1;
    const monthLabel = new Date().toLocaleString('en-US', { month: 'long' });

    return (
        <div className="space-y-6">
            <div className="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h1 className="text-2xl font-bold tracking-tight text-slate-900">Dashboard</h1>
                    <p className="mt-1 text-sm text-slate-500">
                        Here's what's happening in your business today.
                    </p>
                </div>
                <Link to="/repairs/create">
                    <Button>
                        <CirclePlus className="h-4 w-4" />
                        New repair
                    </Button>
                </Link>
            </div>

            {error && (
                <Alert tone="error">{error}</Alert>
            )}

            {!stats ? (
                <div className="flex items-center justify-center py-20 text-slate-400">
                    <Spinner className="h-6 w-6 animate-spin text-blue-600" />
                </div>
            ) : (
                <>
                    <div className="grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
                        <StatCard
                            label={`Earnings · today`}
                            value={formatMoney(stats.current_year_income.current_day)}
                            prefix="$"
                            icon={DollarSign}
                            tone="emerald"
                        />
                        <StatCard
                            label={`Earnings · ${monthLabel}`}
                            value={formatMoney(stats.current_year_income.current_month)}
                            prefix="$"
                            icon={DollarSign}
                            tone="emerald"
                        />
                        <StatCard
                            label={`Earnings · ${year}`}
                            value={formatMoney(stats.current_year_income.total)}
                            prefix="$"
                            icon={DollarSign}
                            tone="emerald"
                        />
                        <StatCard
                            label="Repairs not invoiced"
                            value={stats.repairs_no_invoice}
                            icon={Wrench}
                            tone="blue"
                            to="/repairs"
                        />
                        <StatCard
                            label="Unpaid invoices"
                            value={stats.unpaid_invoices}
                            icon={Receipt}
                            tone="slate"
                            to="/invoices"
                        />
                    </div>

                    <EarningsChart
                        current={stats.current_year_income}
                        past={stats.past_year_income}
                        year={year}
                        lastYear={lastYear}
                    />
                </>
            )}
        </div>
    );
}