import React, { useEffect, useRef, useState } from 'react';
import { Search, X } from 'lucide-react';
import api from '../../api/client';
import { formatMoney, getApiError } from '../../utils/format';
import Input from '../ui/Input';
import Button from '../ui/Button';

export default function ProductPicker({ open, onClose, onSelect }) {
    const [search, setSearch] = useState('');
    const [debounced, setDebounced] = useState('');
    const [products, setProducts] = useState([]);
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState('');
    const timer = useRef(null);

    useEffect(() => {
        if (!open) {
            setSearch('');
            setDebounced('');
            setProducts([]);
            return;
        }
    }, [open]);

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
                    <h3 className="text-sm font-semibold text-slate-900">Select a product</h3>
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
                            placeholder="Search name, barcode, supplier..."
                            className="pl-9"
                            autoFocus
                        />
                    </div>
                </div>

                <div className="flex-1 overflow-y-auto">
                    {loading ? (
                        <div className="px-5 py-10 text-center text-sm text-slate-400">Loading...</div>
                    ) : error ? (
                        <div className="px-5 py-10 text-center text-sm text-red-600">{error}</div>
                    ) : products.length === 0 ? (
                        <div className="px-5 py-10 text-center text-sm text-slate-400">
                            Nothing has been found.
                        </div>
                    ) : (
                        products.map((product) => (
                            <button
                                key={product.id}
                                type="button"
                                onClick={() => onSelect(product)}
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
                                <span className="shrink-0 text-xs text-slate-500">
                                    Stock: {product.stock}
                                </span>
                            </button>
                        ))
                    )}
                </div>

                <div className="flex justify-end border-t border-slate-100 px-5 py-3">
                    <Button type="button" variant="secondary" className="w-full" onClick={onClose}>
                        Cancel
                    </Button>
                </div>
            </div>
        </div>
    );
}
