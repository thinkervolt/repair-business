import React, { useEffect, useRef, useState } from 'react';
import { History, Search, Trash2 } from 'lucide-react';
import api from '../../api/client';
import { useI18n } from '../../i18n/I18nContext';
import { formatDateTime, formatMoney, formatPhone, getApiError } from '../../utils/format';
import Card from '../../components/ui/Card';
import Button from '../../components/ui/Button';
import Input from '../../components/ui/Input';
import Alert from '../../components/ui/Alert';

const ACTIONS = {
    customer: { restore: '/customers', destroy: '/customers' },
    repair: { restore: '/repairs', destroy: '/repairs' },
    invoice: { restore: '/invoices', destroy: '/invoices' },
};

function customerText(c) {
    return [
        c.first_name ? `[${c.first_name} ${c.last_name || ''}]` : '',
        c.phone ? `[${formatPhone(c.phone)}]` : '',
        c.email ? `[${c.email}]` : '',
    ]
        .filter(Boolean)
        .join(' ');
}

function repairText(r) {
    const c = r.customer_data;
    const customerPart = c
        ? [
              c.first_name,
              c.last_name,
              c.email,
              c.phone ? formatPhone(c.phone) : null,
          ]
              .filter(Boolean)
              .join(' ')
        : '';
    return [`[${r.target}]`, `[${r.request}]`, customerPart ? `[${customerPart}]` : '']
        .filter(Boolean)
        .join(' ');
}

function invoiceText(i) {
    return [
        `[Total: ${formatMoney(i.total)}]`,
        `[Balance: ${formatMoney(i.balance)}]`,
        i.customer_name ? `[${i.customer_name} ${i.customer_email || ''} ${formatPhone(i.customer_phone)}]` : '',
    ]
        .filter(Boolean)
        .join(' ');
}

function ActionButtons({ kind, id, busy, onRestore, onDestroy }) {
    const { t } = useI18n();
    const base = ACTIONS[kind];

    return (
        <div className="flex items-center gap-1.5">
            <Button
                variant="secondary"
                size="sm"
                loading={busy === `${kind}-${id}-restore`}
                onClick={() => onRestore(kind, id)}
            >
                <History className="h-3.5 w-3.5" />
                {t('trash.restore')}
            </Button>
            <Button
                variant="danger"
                size="sm"
                loading={busy === `${kind}-${id}-destroy`}
                onClick={() => onDestroy(kind, id)}
            >
                <Trash2 className="h-3.5 w-3.5" />
                {t('trash.destroy')}
            </Button>
        </div>
    );
}

export default function TrashList() {
    const { t } = useI18n();
    const [customers, setCustomers] = useState(null);
    const [repairs, setRepairs] = useState(null);
    const [invoices, setInvoices] = useState(null);
    const [search, setSearch] = useState('');
    const [debounced, setDebounced] = useState('');
    const [error, setError] = useState('');
    const [message, setMessage] = useState('');
    const [busy, setBusy] = useState('');
    const timer = useRef(null);

    useEffect(() => {
        clearTimeout(timer.current);
        timer.current = setTimeout(() => setDebounced(search), 350);
        return () => clearTimeout(timer.current);
    }, [search]);

    useEffect(() => {
        api.get('/trash', { params: { search: debounced } })
            .then(({ data }) => {
                setCustomers(data.data.customers);
                setRepairs(data.data.repairs);
                setInvoices(data.data.invoices);
                setError('');
            })
            .catch((err) => setError(getApiError(err)));
    }, [debounced]);

    const notify = (text, tone = 'success') => {
        setMessage(tone === 'success' ? text : '');
        setError(tone === 'error' ? text : '');
        window.scrollTo({ top: 0, behavior: 'smooth' });
    };

    const load = () => {
        api.get('/trash', { params: { search: debounced } })
            .then(({ data }) => {
                setCustomers(data.data.customers);
                setRepairs(data.data.repairs);
                setInvoices(data.data.invoices);
            })
            .catch((err) => setError(getApiError(err)));
    };

    const act = (kind, id, verb, method) => {
        setBusy(`${kind}-${id}-${verb}`);
        api({
            method,
            url: `${ACTIONS[kind][verb]}/${id}${verb === 'restore' ? '/restore' : ''}`,
        })
            .then(({ data }) => {
                notify(data.message);
                load();
            })
            .catch((err) => notify(getApiError(err), 'error'))
            .finally(() => setBusy(''));
    };

    const restore = (kind, id) => act(kind, id, 'restore', 'put');
    const destroy = (kind, id) => {
        if (window.confirm(t('trash.destroy_confirm'))) {
            act(kind, id, 'destroy', 'delete');
        }
    };

    const loaded = customers !== null;
    const total = (customers?.length || 0) + (repairs?.length || 0) + (invoices?.length || 0);

    return (
        <div className="space-y-6">
            <div>
                <h1 className="text-2xl font-bold tracking-tight text-slate-900">{t('nav.trash')}</h1>
                <p className="mt-1 text-sm text-slate-500">
                    {t('trash.subtitle')}
                </p>
            </div>

            {error && <Alert tone="error">{error}</Alert>}
            {message && <Alert tone="success">{message}</Alert>}

            <Card className="overflow-hidden">
                <div className="border-b border-slate-100 p-4 sm:p-5">
                    <div className="relative max-w-sm">
                        <Search className="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                        <Input
                            type="search"
                            value={search}
                            onChange={(e) => setSearch(e.target.value)}
                            placeholder={t('trash.search_placeholder')}
                            className="pl-9"
                        />
                    </div>
                </div>

                {!loaded ? (
                    <div className="px-6 py-12 text-center text-sm text-slate-400">{t('common.loading')}</div>
                ) : total > 0 ? (
                    <div className="overflow-x-auto">
                        <table className="w-full text-left">
                            <thead>
                                <tr className="border-b border-slate-100 bg-slate-50/60 text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    <th className="px-6 py-3">{t('common.id')}</th>
                                    <th className="px-6 py-3">{t('trash.item')}</th>
                                    <th className="px-6 py-3">{t('common.data')}</th>
                                    <th className="px-6 py-3">{t('trash.deleted')}</th>
                                    <th className="px-6 py-3 text-right">{t('common.actions')}</th>
                                </tr>
                            </thead>
                            <tbody>
                                {customers.map((c) => (
                                    <tr
                                        key={`c-${c.id}`}
                                        className="border-b border-slate-100 transition-colors last:border-0 hover:bg-slate-50"
                                    >
                                        <td className="px-6 py-3 text-sm text-slate-500">{c.id}</td>
                                        <td className="px-6 py-3 text-sm font-medium text-slate-700">{t('trash.customer')}</td>
                                        <td className="px-6 py-3 text-sm text-slate-600">{customerText(c)}</td>
                                        <td className="px-6 py-3 text-sm text-slate-500">{formatDateTime(c.updated_at)}</td>
                                        <td className="px-6 py-3 text-right">
                                            <ActionButtons
                                                kind="customer"
                                                id={c.id}
                                                busy={busy}
                                                onRestore={restore}
                                                onDestroy={destroy}
                                            />
                                        </td>
                                    </tr>
                                ))}
                                {repairs.map((r) => (
                                    <tr
                                        key={`r-${r.id}`}
                                        className="border-b border-slate-100 transition-colors last:border-0 hover:bg-slate-50"
                                    >
                                        <td className="px-6 py-3 text-sm text-slate-500">{r.id}</td>
                                        <td className="px-6 py-3 text-sm font-medium text-slate-700">{t('trash.repair')}</td>
                                        <td className="px-6 py-3 text-sm text-slate-600">{repairText(r)}</td>
                                        <td className="px-6 py-3 text-sm text-slate-500">{formatDateTime(r.updated_at)}</td>
                                        <td className="px-6 py-3 text-right">
                                            <ActionButtons
                                                kind="repair"
                                                id={r.id}
                                                busy={busy}
                                                onRestore={restore}
                                                onDestroy={destroy}
                                            />
                                        </td>
                                    </tr>
                                ))}
                                {invoices.map((i) => (
                                    <tr
                                        key={`i-${i.id}`}
                                        className="border-b border-slate-100 transition-colors last:border-0 hover:bg-slate-50"
                                    >
                                        <td className="px-6 py-3 text-sm text-slate-500">{i.id}</td>
                                        <td className="px-6 py-3 text-sm font-medium text-slate-700">{t('trash.invoice')}</td>
                                        <td className="px-6 py-3 text-sm text-slate-600">{invoiceText(i)}</td>
                                        <td className="px-6 py-3 text-sm text-slate-500">{formatDateTime(i.updated_at)}</td>
                                        <td className="px-6 py-3 text-right">
                                            <ActionButtons
                                                kind="invoice"
                                                id={i.id}
                                                busy={busy}
                                                onRestore={restore}
                                                onDestroy={destroy}
                                            />
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                ) : (
                    <div className="px-6 py-12 text-center text-sm text-slate-400">
                        {t('common.nothing_found')}
                    </div>
                )}
            </Card>
        </div>
    );
}