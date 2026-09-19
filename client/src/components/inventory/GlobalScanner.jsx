import React, { useEffect, useRef, useState } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { ScanBarcode, PackagePlus, X, Zap } from 'lucide-react';
import api from '../../api/client';
import { useI18n } from '../../i18n/I18nContext';
import Button from '../ui/Button';

const SCAN_GAP_MS = 80;

export default function GlobalScanner() {
    const navigate = useNavigate();
    const { t } = useI18n();
    const [result, setResult] = useState(null);
    const bufferRef = useRef({ code: '', lastTime: 0 });

    const submit = async (code) => {
        try {
            const { data } = await api.post('/barcode', { barcode: code });
            setResult(data.data);
        } catch {
            setResult({ response: 'barcode-not-found', data: null, data_response: code });
        }
    };

    useEffect(() => {
        const handleKeyDown = (e) => {
            const el = document.activeElement;
            const editable =
                el &&
                (el.tagName === 'INPUT' ||
                    el.tagName === 'TEXTAREA' ||
                    el.tagName === 'SELECT' ||
                    el.isContentEditable);
            if (editable) return;

            if (e.key === 'Enter') {
                e.preventDefault();
                const code = bufferRef.current.code.trim();
                bufferRef.current.code = '';
                bufferRef.current.lastTime = 0;
                if (code.length >= 2) {
                    submit(code);
                }
                return;
            }

            if (e.key.length !== 1 || e.ctrlKey || e.metaKey || e.altKey) return;

            const now = Date.now();
            if (now - bufferRef.current.lastTime > SCAN_GAP_MS) {
                bufferRef.current.code = e.key;
            } else {
                bufferRef.current.code += e.key;
            }
            bufferRef.current.lastTime = now;
        };

        const handleKeyUp = (e) => {
            if (e.key === 'Escape' && result) {
                setResult(null);
            }
        };

        window.addEventListener('keydown', handleKeyDown);
        window.addEventListener('keyup', handleKeyUp);
        return () => {
            window.removeEventListener('keydown', handleKeyDown);
            window.removeEventListener('keyup', handleKeyUp);
        };
    }, [result]);

    if (!result) return null;

    const { response, data, data_response } = result;

    const handleQuickSell = async (product) => {
        try {
            await api.post(`/inventory/products/${product.id}/quick-sell`);
        } finally {
            setResult(null);
            navigate(`/inventory/products/${product.id}`);
        }
    };

    return (
        <div
            className="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 p-4"
            onClick={() => setResult(null)}
        >
            <div
                className="w-full max-w-sm rounded-2xl bg-white p-6 shadow-2xl"
                onClick={(e) => e.stopPropagation()}
            >
                <div className="flex items-start justify-between">
                    <div className="flex items-center gap-2">
                        <ScanBarcode className="h-5 w-5 text-blue-700" />
                        <h3 className="text-lg font-bold text-slate-900">{t('scanner.title')}</h3>
                    </div>
                    <button
                        type="button"
                        onClick={() => setResult(null)}
                        className="rounded-lg p-1 text-slate-400 hover:bg-slate-100 hover:text-slate-600"
                    >
                        <X className="h-5 w-5" />
                    </button>
                </div>

                {response === 'product-found' && data && (
                    <div className="mt-4 space-y-4">
                        <div className="rounded-xl border border-slate-200 p-4">
                            <p className="font-mono text-xs uppercase tracking-wide text-blue-700">
                                {t('scanner.product_found')}
                            </p>
                            <p className="mt-1 font-semibold text-slate-900">{data.name}</p>
                            <p className="font-mono text-xs text-slate-400">
                                {t('scanner.barcode_label', { barcode: data.barcode })}
                            </p>
                            <div className="mt-3 flex items-center justify-between text-sm">
                                <span className="text-slate-500">{t('scanner.price')}</span>
                                <span className="font-semibold text-slate-900">
                                    ${parseFloat(data.selling_price || 0).toFixed(2)}
                                </span>
                            </div>
                            <div className="flex items-center justify-between text-sm">
                                <span className="text-slate-500">{t('scanner.available')}</span>
                                <span
                                    className={`font-semibold ${
                                        (data_response || 0) > 0
                                            ? 'text-emerald-600'
                                            : 'text-red-600'
                                    }`}
                                >
                                    {data_response || 0}
                                </span>
                            </div>
                        </div>
                        <div className="flex gap-2">
                            <Link
                                to={`/inventory/products/${data.id}`}
                                onClick={() => setResult(null)}
                                className="flex-1"
                            >
                                <Button className="w-full">{t('scanner.open_product')}</Button>
                            </Link>
                            <Button variant="secondary" type="button" onClick={() => handleQuickSell(data)}>
                                <Zap className="h-4 w-4" />
                                {t('inventory.quick_sell')}
                            </Button>
                        </div>
                    </div>
                )}

                {response === 'invoice-found' && data && (
                    <div className="mt-4 space-y-4">
                        <div className="rounded-xl border border-slate-200 p-4">
                            <p className="font-mono text-xs uppercase tracking-wide text-blue-700">
                                {t('scanner.invoice_found')}
                            </p>
                            <p className="mt-1 font-semibold text-slate-900">
                                {t('inventory.invoice_source', { id: data.id })} {data.customer_name ? `- ${data.customer_name}` : ''}
                            </p>
                            <p className="text-sm text-slate-500">{t('scanner.total', { amount: `$${parseFloat(data.total || 0).toFixed(2)}` })}</p>
                        </div>
                        <Link to={`/invoices/${data.id}`} onClick={() => setResult(null)}>
                            <Button className="w-full">{t('scanner.open_invoice')}</Button>
                        </Link>
                    </div>
                )}

                {response === 'repair-found' && data && (
                    <div className="mt-4 space-y-4">
                        <div className="rounded-xl border border-slate-200 p-4">
                            <p className="font-mono text-xs uppercase tracking-wide text-blue-700">
                                {t('scanner.repair_found')}
                            </p>
                            <p className="mt-1 font-semibold text-slate-900">{t('inventory.repair_source', { id: data.id })}</p>
                            {data.device_model && (
                                <p className="text-sm text-slate-500">{data.device_model}</p>
                            )}
                        </div>
                        <Link to={`/repairs/${data.id}`} onClick={() => setResult(null)}>
                            <Button className="w-full">{t('scanner.open_repair')}</Button>
                        </Link>
                    </div>
                )}

                {response === 'barcode-not-found' && (
                    <div className="mt-4 space-y-4">
                        <div className="rounded-xl border border-red-200 bg-red-50 p-4">
                            <p className="font-mono text-xs uppercase tracking-wide text-red-700">
                                {t('scanner.barcode_not_found')}
                            </p>
                            <p className="mt-1 font-mono text-sm text-slate-700">{data_response}</p>
                        </div>
                        <Button
                            className="w-full"
                            onClick={() => {
                                setResult(null);
                                navigate(
                                    `/inventory/products/create?barcode=${encodeURIComponent(data_response || '')}`
                                );
                            }}
                        >
                            <PackagePlus className="h-4 w-4" />
                            {t('scanner.create_with_barcode')}
                        </Button>
                    </div>
                )}
            </div>
        </div>
    );
}