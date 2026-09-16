import React, { useEffect, useRef, useState } from 'react';
import { Link } from 'react-router-dom';
import { Search, UserPlus } from 'lucide-react';
import api from '../../api/client';
import { formatPhone, getApiError } from '../../utils/format';
import Card from '../../components/ui/Card';
import Button from '../../components/ui/Button';
import Input from '../../components/ui/Input';
import Alert from '../../components/ui/Alert';
import Pagination from '../../components/ui/Pagination';

function CustomerRow({ customer }) {
    return (
        <tr className="border-b border-slate-100 transition-colors last:border-0 hover:bg-slate-50">
            <td className="px-6 py-3">
                <Link
                    to={`/customers/${customer.id}`}
                    className="font-medium text-blue-700 hover:underline"
                >
                    {customer.first_name} {customer.last_name || ''}
                </Link>
            </td>
            <td className="px-6 py-3 text-sm text-slate-600">{formatPhone(customer.phone)}</td>
            <td className="px-6 py-3 text-sm text-slate-600">{customer.email || '—'}</td>
            <td className="px-6 py-3 text-sm text-slate-600">{customer.company || '—'}</td>
            <td className="px-6 py-3 text-right">
                <Link
                    to={`/customers/${customer.id}`}
                    className="text-sm font-medium text-blue-700 hover:underline"
                >
                    View
                </Link>
            </td>
        </tr>
    );
}

export default function CustomersList() {
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
        api.get('/customers', { params: { search: debounced, page } })
            .then(({ data }) => setData(data.data))
            .catch((err) => setError(getApiError(err)));
    }, [debounced, page]);

    return (
        <div className="space-y-6">
            <div className="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h1 className="text-2xl font-bold tracking-tight text-slate-900">Customers</h1>
                    <p className="mt-1 text-sm text-slate-500">
                        Manage the people and companies you work with.
                    </p>
                </div>
                <Link to="/customers/create">
                    <Button>
                        <UserPlus className="h-4 w-4" />
                        New customer
                    </Button>
                </Link>
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
                            placeholder="Search name, phone, email, company..."
                            className="pl-9"
                        />
                    </div>
                </div>

                {!data ? (
                    <div className="px-6 py-12 text-center text-sm text-slate-400">Loading...</div>
                ) : data.data.length > 0 ? (
                    <>
                        <div className="overflow-x-auto">
                            <table className="w-full text-left">
                                <thead>
                                    <tr className="border-b border-slate-100 bg-slate-50/60 text-xs font-semibold uppercase tracking-wide text-slate-500">
                                        <th className="px-6 py-3">Name</th>
                                        <th className="px-6 py-3">Phone</th>
                                        <th className="px-6 py-3">Email</th>
                                        <th className="px-6 py-3">Company</th>
                                        <th className="px-6 py-3 text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {data.data.map((customer) => (
                                        <CustomerRow key={customer.id} customer={customer} />
                                    ))}
                                </tbody>
                            </table>
                        </div>
                        <Pagination paginator={data} onChangePage={setPage} />
                    </>
                ) : (
                    <div className="px-6 py-12 text-center text-sm text-slate-400">
                        Nothing has been found.
                    </div>
                )}
            </Card>
        </div>
    );
}