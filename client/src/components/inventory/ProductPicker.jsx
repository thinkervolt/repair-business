import React, { useEffect, useRef, useState } from 'react';
import { Check, Search, X } from 'lucide-react';
import api from '../../api/client';
import { useI18n } from '../../i18n/I18nContext';
import { formatMoney, getApiError } from '../../utils/format';
import Input from '../ui/Input';
import Button from '../ui/Button';

export default function ProductPicker({ open, onClose, onSelect, quantityEnabled = false }) {
    const { t } = useI18n();
    const [search, setSearch] = useState('');
    const [debounced, setDebounced] = useState('');
    const [products, setProducts] = useState([]);
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState('');
    const [selected, setSelected] = useState(null);
    const [quantity, setQuantity] = useState('1');
    const timer = useRef(null);

    useEffect(() => {
        if (!open) {
            setSearch('');
            setDebounced('');
            setProducts([]);
            setSelected(null);
            setQuantity('1');
            return;
        }
    }, [open]);

    const choose = (product) => {
        if (!quantityEnabled) {
            onSelect(product);
            return;
        }
        setSelected(product);
        setQuantity('1');
    };

    const back = () => {
        setSelected(null);
        setQuantity('1');
    };

    const confirmAdd = () => {
        const value = Number(quantity);
        if (value < 1) {
            setQuantity('1');
            return;
        }
        onSelect(selected, value);
    };

    useEffect(() => {
        clearTimeout(timer.current);
        timer.current = setTimeout(() => setDebounced(search), 300);
        return () => clearTimeout(timer.current);
    }, [search]);

    useEffect(() => {
        if (!open) return;
        setLoading(true);
        api.get('/inventory/products', { params: { search: debounced } })
            .then(({ data }) => setProducts(data.data.products.data))
            .catch((err) => setError(getApiError(err)))
            .finally(() => setLoading(false));
    }, [open, debounced]);

    if (!open) return null;

    return (
        <div
            className="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 p-4"
            onClick={onClose}
        >
            <div
                className="flex max-h-[80vh] w-full max-w-lg flex-col overflow-hidden rounded-2xl bg-white shadow-xl"
                onClick={(e) => e.stopPropagation()}
            >
                <div className="flex items-center justify-between border-b border-slate-100 px-5 py-4">
                    <h3 className="text-sm font-semibold text-slate-900">{t('picker.select_product')}</h3>
                    <button
                        type="button"
                        onClick={onClose}
                        className="rounded-lg p-1.5 text-slate-400 transition-colors hover:bg-slate-100 hover:text-slate-600"
                        aria-label="Close"
                    >
                        <X className="h-4 w-4" />
                    </button>
                </div>

                <div className="border-b border-slate-100 px-5 py-3">
                    <div className="relative">
                        <Search className="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                        <Input
                            type="search"
                            value={search}
                            onChange={(e) => setSearch(e.target.value)}
                            placeholder={t('picker.search_product')}
                            className="pl-9"
                            autoFocus
                        />
                    </div>
                </div>

                <div className="flex-1 overflow-y-auto">
                    {loading ? (
                        <div className="px-5 py-10 text-center text-sm text-slate-400">{t('common.loading')}</div>
                    ) : error ? (
                        <div className="px-5 py-10 text-center text-sm text-red-600">{error}</div>
                    ) : products.length === 0 ? (
                        <div className="px-5 py-10 text-center text-sm text-slate-400">
                            {t('common.nothing_found')}
                        </div>
                    ) : (
                        products.map((product) => {
                            const isSelected = quantityEnabled && selected && selected.id === product.id;
                            return (
                                <div
                                    key={product.id}
                                    role={isSelected ? undefined : 'button'}
                                    tabIndex={isSelected ? undefined : 0}
                                    onClick={() => {
                                        if (!isSelected) choose(product);
                                    }}
                                    onKeyDown={(e) => {
                                        if (!isSelected && (e.key === 'Enter' || e.key === ' ')) {
                                            e.preventDefault();
                                            choose(product);
                                        }
                                    }}
                                    className="flex w-full items-center justify-between gap-3 border-b border-slate-50 px-5 py-3 text-left transition-colors hover:bg-blue-50/60"
                                >
                                    <span className="min-w-0">
                                        <span className="block truncate text-sm font-medium text-slate-800">
                                            {product.name}
                                        </span>
                                        <span className="block truncate text-xs text-slate-500">
                                            {product.barcode ? `${product.barcode} · ` : ''}
                                            {product.category ? product.category.name : ''}
                                        </span>
                                    </span>
                                    {isSelected ? (
                                        <div
                                            className="flex shrink-0 items-center gap-1.5"
                                            onClick={(e) => e.stopPropagation()}
                                        >
                                            <Input
                                                type="number"
                                                step="1"
                                                min="1"
                                                value={quantity}
                                                onChange={(e) => setQuantity(e.target.value)}
                                                onKeyDown={(e) => {
                                                    if (e.key === 'Enter') {
                                                        e.preventDefault();
                                                        confirmAdd();
                                                    }
                                                    if (e.key === 'Escape') {
                                                        e.preventDefault();
                                                        back();
                                                    }
                                                }}
                                                className="w-16 text-center"
                                                autoFocus
                                            />
                                            <button
                                                type="button"
                                                onClick={confirmAdd}
                                                className="rounded-lg border border-slate-300 p-1.5 text-slate-500 transition-colors hover:bg-blue-50 hover:text-blue-700"
                                                aria-label={t('common.add')}
                                                title={t('common.add')}
                                            >
                                                <Check className="h-3.5 w-3.5" />
                                            </button>
                                        </div>
                                    ) : (
                                        <span className="shrink-0 text-xs text-slate-500">
                                            {t('picker.stock', { stock: product.stock })}
                                        </span>
                                    )}
                                </div>
                            );
                        })
                    )}
                </div>

                <div className="flex justify-end border-t border-slate-100 px-5 py-3">
                    <Button type="button" variant="secondary" className="w-full" onClick={onClose}>
                        {t('common.cancel')}
                    </Button>
                </div>
            </div>
        </div>
    );
}
