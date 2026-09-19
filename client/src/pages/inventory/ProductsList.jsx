import React, { useEffect, useRef, useState } from 'react';
import { Link } from 'react-router-dom';
import { CirclePlus, Search, Zap } from 'lucide-react';
import api from '../../api/client';
import { useI18n } from '../../i18n/I18nContext';
import { formatMoney, getApiError } from '../../utils/format';
import Card from '../../components/ui/Card';
import Button from '../../components/ui/Button';
import Input from '../../components/ui/Input';
import Badge from '../../components/ui/Badge';
import Alert from '../../components/ui/Alert';
import Pagination from '../../components/ui/Pagination';

function stockTone(stock, product) {
    const min = Number(product.min_stock);
    if (min > 0 && stock <= min) return 'red';
    if (min > 0 && stock <= min * 2) return 'amber';
    return 'emerald';
}

export default function ProductsList() {
    const { t } = useI18n();
    const [data, setData] = useState(null);
    const [search, setSearch] = useState('');
    const [debounced, setDebounced] = useState('');
    const [page, setPage] = useState(1);
    const [error, setError] = useState('');
    const [selling, setSelling] = useState(null);
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
        api.get('/inventory/products', { params: { search: debounced, page } })
            .then(({ data }) => setData(data.data))
            .catch((err) => setError(getApiError(err)));
    }, [debounced, page]);

    const quickSell = async (product) => {
        setSelling(product.id);
        try {
            const { data } = await api.post(`/inventory/products/${product.id}/quick-sell`);
            alert(data.message);
            setPage(1);
            const res = await api.get('/inventory/products', { params: { search: debounced, page: 1 } });
            setData(res.data.data);
        } catch (err) {
            setError(getApiError(err));
        } finally {
            setSelling(null);
        }
    };

    return (
        <div className="space-y-6">
            <div className="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h1 className="text-2xl font-bold tracking-tight text-slate-900">{t('nav.products')}</h1>
                    <p className="mt-1 text-sm text-slate-500">
                        {t('inventory.subtitle')}
                    </p>
                </div>
                <Link to="/inventory/products/create">
                    <Button>
                        <CirclePlus className="h-4 w-4" />
                        {t('inventory.new_product')}
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
                            placeholder={t('inventory.search_placeholder')}
                            className="pl-9"
                        />
                    </div>
                </div>

                {!data ? (
                    <div className="px-6 py-12 text-center text-sm text-slate-400">{t('common.loading')}</div>
                ) : data.products.data.length > 0 ? (
                    <>
                        <div className="overflow-x-auto">
                            <table className="w-full text-left">
                                <thead>
                                    <tr className="border-b border-slate-100 bg-slate-50/60 text-xs font-semibold uppercase tracking-wide text-slate-500">
                                        <th className="px-6 py-3">{t('common.name')}</th>
                                        <th className="px-6 py-3">{t('inventory.category')}</th>
                                        <th className="px-6 py-3">{t('inventory.barcode')}</th>
                                        <th className="px-6 py-3">{t('inventory.sell_price')}</th>
                                        <th className="px-6 py-3">{t('inventory.stock')}</th>
                                        <th className="px-6 py-3 text-right">{t('common.actions')}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {data.products.data.map((product) => (
                                        <tr
                                            key={product.id}
                                            className="border-b border-slate-100 transition-colors last:border-0 hover:bg-slate-50"
                                        >
                                            <td className="px-6 py-3">
                                                <Link
                                                    to={`/inventory/products/${product.id}`}
                                                    className="text-sm font-medium text-slate-800 hover:text-blue-700"
                                                >
                                                    {product.name}
                                                </Link>
                                                {product.supplier && (
                                                    <span className="block text-xs text-slate-400">
                                                        {product.supplier}
                                                    </span>
                                                )}
                                            </td>
                                            <td className="px-6 py-3 text-sm text-slate-600">
                                                {data.categories.find((c) => c.id === Number(product.category_id))?.name ||
                                                    '—'}
                                            </td>
                                            <td className="px-6 py-3 font-mono text-xs text-slate-500">
                                                {product.barcode || '—'}
                                            </td>
                                            <td className="px-6 py-3 text-sm text-slate-700">
                                                ${formatMoney(product.selling_price)}
                                            </td>
                                            <td className="px-6 py-3">
                                                <Badge solid tone={stockTone(product.stock, product)}>
                                                    {product.stock}
                                                </Badge>
                                            </td>
                                            <td className="px-6 py-3 text-right">
                                                <div className="flex items-center justify-end gap-2">
                                                    <Button
                                                        type="button"
                                                        variant="secondary"
                                                        size="sm"
                                                        loading={selling === product.id}
                                                        title={t('inventory.quick_sell')}
                                                        onClick={() => quickSell(product)}
                                                    >
                                                        <Zap className="h-3.5 w-3.5" />
                                                        {t('inventory.quick_sell')}
                                                    </Button>
                                                    <Link
                                                        to={`/inventory/products/${product.id}`}
                                                        className="text-sm font-medium text-blue-700 hover:underline"
                                                    >
                                                        {t('common.view')}
                                                    </Link>
                                                </div>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                        <Pagination paginator={data.products} onChangePage={setPage} />
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