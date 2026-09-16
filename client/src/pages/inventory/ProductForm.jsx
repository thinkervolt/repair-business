import React, { useEffect, useState } from 'react';
import { Link, useNavigate, useParams } from 'react-router-dom';
import { Save } from 'lucide-react';
import api from '../../api/client';
import { getApiError } from '../../utils/format';
import Card from '../../components/ui/Card';
import Button from '../../components/ui/Button';
import Input from '../../components/ui/Input';
import Alert from '../../components/ui/Alert';

function FieldError({ error }) {
    return error ? <p className="mt-1 text-xs text-red-600">{error[0]}</p> : null;
}

const blank = {
    category: '',
    name: '',
    barcode: '',
    purchase_price: '',
    selling_price: '',
    quantity: '',
    supplier: '',
    min_stock: '',
    max_stock: '',
    email_alert: 'no',
};

export default function ProductForm() {
    const { id } = useParams();
    const navigate = useNavigate();
    const isEdit = Boolean(id);
    const [form, setForm] = useState(blank);
    const [categories, setCategories] = useState([]);
    const [errors, setErrors] = useState({});
    const [message, setMessage] = useState('');
    const [loading, setLoading] = useState(isEdit);
    const [saving, setSaving] = useState(false);

    useEffect(() => {
        api.get('/inventory/products')
            .then(({ data }) => setCategories(data.data.categories))
            .catch(() => setCategories([]));
    }, []);

    useEffect(() => {
        if (!isEdit) return;
        api.get(`/inventory/products/${id}`)
            .then(({ data }) => {
                const p = data.data.product;
                setForm({
                    category: p.category_id != null ? String(p.category_id) : '',
                    name: p.name,
                    barcode: p.barcode || '',
                    purchase_price: '',
                    selling_price: p.selling_price != null ? String(p.selling_price) : '',
                    quantity: '',
                    supplier: p.supplier || '',
                    min_stock: p.min_stock != null ? String(p.min_stock) : '',
                    max_stock: p.max_stock != null ? String(p.max_stock) : '',
                    email_alert: p.email_alert || 'no',
                });
            })
            .catch((err) => setMessage(getApiError(err)))
            .finally(() => setLoading(false));
    }, [id, isEdit]);

    const setField = (key) => (e) => setForm((f) => ({ ...f, [key]: e.target.value }));

    const handleSubmit = async (e) => {
        e.preventDefault();
        setErrors({});
        setSaving(true);
        try {
            const payload = { ...form };
            if (isEdit) {
                delete payload.purchase_price;
                delete payload.quantity;
                const { data } = await api.put(`/inventory/products/${id}`, payload);
                navigate(`/inventory/products/${data.data.id}`);
            } else {
                const { data } = await api.post('/inventory/products', payload);
                navigate(`/inventory/products/${data.data.id}`);
            }
        } catch (err) {
            const response = err.response;
            if (response && response.data && response.data.errors) {
                setErrors(response.data.errors);
            } else {
                setMessage(getApiError(err));
            }
        } finally {
            setSaving(false);
        }
    };

    if (loading) {
        return <div className="py-12 text-center text-sm text-slate-400">Loading...</div>;
    }

    return (
        <div className="mx-auto max-w-2xl space-y-6">
            <div>
                <Link
                    to="/inventory/products"
                    className="text-sm font-medium text-blue-700 hover:underline"
                >
                    &larr; Back to products
                </Link>
                <h1 className="mt-2 text-2xl font-bold tracking-tight text-slate-900">
                    {isEdit ? 'Edit product' : 'Create product'}
                </h1>
                <p className="mt-1 text-sm text-slate-500">
                    {isEdit
                        ? 'Update product information.'
                        : 'Register a new part and its initial stock.'}
                </p>
            </div>

            {message && <Alert tone="error">{message}</Alert>}

            <Card className="p-6">
                <form onSubmit={handleSubmit} className="space-y-5">
                    <div className="grid gap-5 sm:grid-cols-2">
                        <div>
                            <label className="mb-1.5 block text-sm font-medium text-slate-700" htmlFor="category">
                                Category *
                            </label>
                            <select
                                id="category"
                                className="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                                value={form.category}
                                onChange={setField('category')}
                            >
                                <option value="">Select a category</option>
                                {categories.map((c) => (
                                    <option key={c.id} value={c.id}>
                                        {c.name}
                                    </option>
                                ))}
                            </select>
                            <FieldError error={errors.category} />
                        </div>
                        <div>
                            <label className="mb-1.5 block text-sm font-medium text-slate-700" htmlFor="name">
                                Name *
                            </label>
                            <Input
                                id="name"
                                value={form.name}
                                onChange={setField('name')}
                                placeholder="e.g. Screen protector glass"
                                invalid={!!errors.name}
                            />
                            <FieldError error={errors.name} />
                        </div>
                        <div>
                            <label className="mb-1.5 block text-sm font-medium text-slate-700" htmlFor="barcode">
                                Barcode
                            </label>
                            <Input
                                id="barcode"
                                value={form.barcode}
                                onChange={setField('barcode')}
                                placeholder="Scan or type a code"
                                invalid={!!errors.barcode}
                            />
                            <FieldError error={errors.barcode} />
                        </div>
                        <div>
                            <label className="mb-1.5 block text-sm font-medium text-slate-700" htmlFor="supplier">
                                Supplier
                            </label>
                            <Input
                                id="supplier"
                                value={form.supplier}
                                onChange={setField('supplier')}
                                placeholder="e.g. ACME Distributors"
                                invalid={!!errors.supplier}
                            />
                            <FieldError error={errors.supplier} />
                        </div>
                        <div>
                            <label className="mb-1.5 block text-sm font-medium text-slate-700" htmlFor="selling_price">
                                Selling price ($) *
                            </label>
                            <Input
                                id="selling_price"
                                type="number"
                                step="0.01"
                                min="0"
                                value={form.selling_price}
                                onChange={setField('selling_price')}
                                placeholder="0.00"
                                invalid={!!errors.selling_price}
                            />
                            <FieldError error={errors.selling_price} />
                        </div>
                        <div>
                            <label className="mb-1.5 block text-sm font-medium text-slate-700" htmlFor="min_stock">
                                Min stock (alert)
                            </label>
                            <Input
                                id="min_stock"
                                type="number"
                                min="0"
                                value={form.min_stock}
                                onChange={setField('min_stock')}
                                placeholder="e.g. 5"
                                invalid={!!errors.min_stock}
                            />
                            <FieldError error={errors.min_stock} />
                        </div>
                        <div>
                            <label className="mb-1.5 block text-sm font-medium text-slate-700" htmlFor="max_stock">
                                Max stock
                            </label>
                            <Input
                                id="max_stock"
                                type="number"
                                min="0"
                                value={form.max_stock}
                                onChange={setField('max_stock')}
                                placeholder="e.g. 50"
                                invalid={!!errors.max_stock}
                            />
                            <FieldError error={errors.max_stock} />
                        </div>
                        <div>
                            <label className="mb-1.5 block text-sm font-medium text-slate-700" htmlFor="email_alert">
                                Email alert *
                            </label>
                            <select
                                id="email_alert"
                                className="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                                value={form.email_alert}
                                onChange={setField('email_alert')}
                            >
                                <option value="no">No</option>
                                <option value="yes">Yes</option>
                            </select>
                            <FieldError error={errors.email_alert} />
                        </div>
                        {!isEdit && (
                            <>
                                <div>
                                    <label className="mb-1.5 block text-sm font-medium text-slate-700" htmlFor="purchase_price">
                                        Purchase price ($) *
                                    </label>
                                    <Input
                                        id="purchase_price"
                                        type="number"
                                        step="0.01"
                                        min="0"
                                        value={form.purchase_price}
                                        onChange={setField('purchase_price')}
                                        placeholder="0.00"
                                        invalid={!!errors.purchase_price}
                                    />
                                    <FieldError error={errors.purchase_price} />
                                </div>
                                <div>
                                    <label className="mb-1.5 block text-sm font-medium text-slate-700" htmlFor="quantity">
                                        Initial quantity *
                                    </label>
                                    <Input
                                        id="quantity"
                                        type="number"
                                        min="1"
                                        value={form.quantity}
                                        onChange={setField('quantity')}
                                        placeholder="e.g. 10"
                                        invalid={!!errors.quantity}
                                    />
                                    <FieldError error={errors.quantity} />
                                </div>
                            </>
                        )}
                    </div>

                    <div className="flex justify-end gap-3 border-t border-slate-100 pt-5">
                        <Link to="/inventory/products">
                            <Button type="button" variant="secondary">
                                Cancel
                            </Button>
                        </Link>
                        <Button type="submit" loading={saving}>
                            <Save className="h-4 w-4" />
                            {isEdit ? 'Save changes' : 'Create product'}
                        </Button>
                    </div>
                </form>
            </Card>
        </div>
    );
}