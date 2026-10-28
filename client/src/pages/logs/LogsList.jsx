import React, { useEffect, useRef, useState } from 'react';
import { ScrollText, Search } from 'lucide-react';
import api from '../../api/client';
import { useI18n } from '../../i18n/I18nContext';
import { formatDateTime, getApiError } from '../../utils/format';
import Card from '../../components/ui/Card';
import Input from '../../components/ui/Input';
import Alert from '../../components/ui/Alert';
import Pagination from '../../components/ui/Pagination';

export default function LogsList() {
    const { t } = useI18n();
    const [data, setData] = useState(null);
    const [search, setSearch] = useState('');
    const [debounced, setDebounced] = useState('');
    const [page, setPage] = useState(1);
    const [error, setError] = useState('');
    const timer = useRef(null);

    useEffect(() => {
        clearTimeout(timer.current);
        timer.current = setTimeout(() => {
            setDebounced(search);
            setPage(1);
        }, 350);
        return () => clearTimeout(timer.current);
    }, [search]);

    useEffect(() => {
        api.get('/logs', { params: { search: debounced, page } })
            .then(({ data }) => setData(data.data))
            .catch((err) => setError(getApiError(err)));
    }, [debounced, page]);

    return (
        <div className="space-y-6">
            <div>
                <h1 className="text-2xl font-bold tracking-tight text-slate-900">{t('nav.activity_log')}</h1>
                <p className="mt-1 text-sm text-slate-500">
                    {t('logs.subtitle')}
                </p>
            </div>

            {error && <Alert tone="error">{error}</Alert>}

            <Card className="overflow-hidden">
                <div className="border-b border-slate-100 p-4 sm:p-5">
                    <div className="relative max-w-sm">
                        <Search className="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                        <Input
                            type="search"
                            value={search}
                            onChange={(e) => setSearch(e.target.value)}
                            placeholder={t('logs.search_placeholder')}
                            className="pl-9"
                        />
                    </div>
                </div>

                {!data ? (
                    <div className="px-6 py-12 text-center text-sm text-slate-400">{t('common.loading')}</div>
                ) : data.data.length > 0 ? (
                    <>
                        <div className="overflow-x-auto">
                            <table className="w-full text-left">
                                <thead>
                                    <tr className="border-b border-slate-100 bg-slate-50/60 text-xs font-semibold uppercase tracking-wide text-slate-500">
                                        <th className="px-6 py-3">{t('logs.activity')}</th>
                                        <th className="px-6 py-3">{t('logs.user')}</th>
                                        <th className="px-6 py-3">{t('logs.when')}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {data.data.map((log) => (
                                        <tr
                                            key={log.id}
                                            className="border-b border-slate-100 transition-colors last:border-0 hover:bg-slate-50"
                                        >
                                            <td className="px-6 py-3 text-sm text-slate-600">
                                                {log.data}
                                            </td>
                                            <td className="px-6 py-3 text-sm font-medium text-slate-700">
                                                {log.user_data ? log.user_data.name : '—'}
                                            </td>
                                            <td className="px-6 py-3 text-sm text-slate-500">
                                                {formatDateTime(log.created_at)}
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                        <Pagination paginator={data} onChangePage={setPage} />
                    </>
                ) : (
                    <div className="px-6 py-12 text-center text-sm text-slate-400">
                        {t('common.nothing_found')}
                    </div>
                )}
            </Card>
        </div>
    );
}