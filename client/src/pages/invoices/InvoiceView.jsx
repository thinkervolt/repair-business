import React, { useEffect, useState } from 'react';
import { Link, useNavigate, useParams } from 'react-router-dom';
import {
    ChevronDown,
    DollarSign,
    FileText,
    Mail,
    Pencil,
    Plus,
    Printer,
    Save,
    ScanLine,
    Trash2,
    UserRound,
    Wrench,
    X,
} from 'lucide-react';
import api from '../../api/client';
import { formatMoney, formatDate, formatDateTime, getApiError } from '../../utils/format';
import { toneFor } from '../../utils/colors';
import Card from '../../components/ui/Card';
import Button from '../../components/ui/Button';
import Input from '../../components/ui/Input';
import Badge from '../../components/ui/Badge';
import Alert from '../../components/ui/Alert';
import Pagination from '../../components/ui/Pagination';
import CustomerPicker from '../../components/customers/CustomerPicker';
import ProductPicker from '../../components/inventory/ProductPicker';

function FieldError({ error }) {
    return error ? <p className="mt-1 text-xs text-red-600">{error[0]}</p> : null;
}

const fieldClasses =
    'block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20';

const iconButton =
    'rounded-lg p-1.5 text-slate-400 transition-colors hover:bg-slate-100 hover:text-slate-700';

function EmptyBlock({ label }) {
    return (
        <div className="px-6 py-10 text-center text-sm text-slate-400">
            No {label} have been found.
        </div>
    );
}

function ItemRow({ invoiceId, item, onChanged, notify }) {
    const [editing, setEditing] = useState(false);
    const [form, setForm] = useState({
        name: item.name,
        description: item.description,
        sub_description: item.sub_description || '',
        unit_cost: item.unit_cost != null ? String(item.unit_cost) : '',
        quantity: item.quantity != null ? String(item.quantity) : '',
    });
    const [errors, setErrors] = useState({});
    const [saving, setSaving] = useState(false);

    const handleUpdate = async () => {
        setErrors({});
        setSaving(true);
        try {
            const { data } = await api.put(`/invoices/${invoiceId}/items/${item.id}`, {
                ...form,
                unit_cost: Number(form.unit_cost),
                quantity: Number(form.quantity),
            });
            notify(data.message);
            setEditing(false);
            onChanged();
        } catch (err) {
            const response = err.response;
            if (response && response.data && response.data.errors) {
                setErrors(response.data.errors);
            } else {
                notify(getApiError(err), 'error');
            }
        } finally {
            setSaving(false);
        }
    };

    const handleDelete = async () => {
        if (!window.confirm('Delete this invoice item?')) return;
        try {
            const { data } = await api.delete(`/invoices/${invoiceId}/items/${item.id}`);
            notify(data.message);
            onChanged();
        } catch (err) {
            notify(getApiError(err), 'error');
        }
    };

    if (editing) {
        return (
            <tr className="border-b border-slate-100 bg-slate-50/60 last:border-0 align-top">
                <td className="px-6 py-4" colSpan={5}>
                    <div className="grid gap-3 lg:grid-cols-[2fr_3fr_1fr_1fr]">
                        <div>
                            <Input
                                value={form.name}
                                onChange={(e) => setForm((f) => ({ ...f, name: e.target.value }))}
                                placeholder="Item name"
                                invalid={!!errors.name}
                            />
                            <FieldError error={errors.name} />
                        </div>
                        <div>
                            <Input
                                value={form.description}
                                onChange={(e) => setForm((f) => ({ ...f, description: e.target.value }))}
                                placeholder="Description"
                                invalid={!!errors.description}
                            />
                            <Input
                                className="mt-1.5"
                                value={form.sub_description}
                                onChange={(e) => setForm((f) => ({ ...f, sub_description: e.target.value }))}
                                placeholder="Sub description"
                            />
                        </div>
                        <div>
                            <Input
                                type="number"
                                step="0.01"
                                min="0"
                                value={form.unit_cost}
                                onChange={(e) => setForm((f) => ({ ...f, unit_cost: e.target.value }))}
                                placeholder="Unit cost"
                                invalid={!!errors.unit_cost}
                            />
                            <FieldError error={errors.unit_cost} />
                        </div>
                        <div>
                            <Input
                                type="number"
                                step="1"
                                min="1"
                                value={form.quantity}
                                onChange={(e) => setForm((f) => ({ ...f, quantity: e.target.value }))}
                                placeholder="Qty"
                                invalid={!!errors.quantity}
                            />
                            <FieldError error={errors.quantity} />
                        </div>
                    </div>
                    <div className="mt-3 flex justify-end gap-2">
                        <Button type="button" variant="secondary" size="sm" onClick={() => setEditing(false)}>
                            Cancel
                        </Button>
                        <Button type="button" size="sm" loading={saving} onClick={handleUpdate}>
                            Save
                        </Button>
                    </div>
                </td>
            </tr>
        );
    }

    return (
        <tr className="group border-b border-slate-100 last:border-0">
            <td className="px-6 py-3">
                <p className="text-sm font-medium text-slate-800">{item.name}</p>
                {item.group === 'repair' && item.ref && (
                    <Link
                        to={`/repairs/${item.ref}`}
                        className="text-xs font-medium text-blue-700 hover:underline"
                    >
                        Repair #{item.ref}
                    </Link>
                )}
            </td>
            <td className="max-w-[280px] px-6 py-3">
                <p className="text-sm text-slate-600 line-clamp-2">{item.description}</p>
                {item.sub_description && (
                    <p className="mt-0.5 text-xs text-slate-400 line-clamp-1">{item.sub_description}</p>
                )}
            </td>
            <td className="px-6 py-3 text-sm text-slate-600">$ {formatMoney(item.unit_cost)}</td>
            <td className="px-6 py-3 text-sm text-slate-600">{item.quantity}</td>
            <td className="px-6 py-3 text-right">
                <p className="text-sm font-medium text-slate-800">$ {formatMoney(item.total)}</p>
                <div className="mt-0.5 flex justify-end gap-0.5 opacity-0 transition-opacity group-hover:opacity-100 focus-within:opacity-100">
                    <button
                        type="button"
                        onClick={() => setEditing(true)}
                        className={iconButton}
                        aria-label="Edit item"
                    >
                        <Pencil className="h-3.5 w-3.5" />
                    </button>
                    <button
                        type="button"
                        onClick={handleDelete}
                        className="rounded-lg p-1.5 text-slate-400 transition-colors hover:bg-red-50 hover:text-red-600"
                        aria-label="Delete item"
                    >
                        <Trash2 className="h-3.5 w-3.5" />
                    </button>
                </div>
            </td>
        </tr>
    );
}

function NewItemForm({ invoiceId, onChanged, notify, onDone }) {
    const [form, setForm] = useState({
        name: '',
        description: '',
        sub_description: '',
        unit_cost: '',
        quantity: '1',
    });
    const [errors, setErrors] = useState({});
    const [saving, setSaving] = useState(false);

    const handleCreate = async (e) => {
        e.preventDefault();
        setErrors({});
        setSaving(true);
        try {
            const { data } = await api.post(`/invoices/${invoiceId}/items`, {
                ...form,
                unit_cost: Number(form.unit_cost),
                quantity: Number(form.quantity),
            });
            setForm({ name: '', description: '', sub_description: '', unit_cost: '', quantity: '1' });
            notify(data.message);
            onDone();
            onChanged();
        } catch (err) {
            const response = err.response;
            if (response && response.data && response.data.errors) {
                setErrors(response.data.errors);
            } else {
                notify(getApiError(err), 'error');
            }
        } finally {
            setSaving(false);
        }
    };

    return (
        <tr className="border-b border-slate-100 bg-slate-50/60 align-top">
            <td className="px-6 py-4" colSpan={5}>
                <form onSubmit={handleCreate} className="grid gap-3 lg:grid-cols-[2fr_3fr_1fr_1fr_auto]">
                    <Input
                        value={form.name}
                        onChange={(e) => setForm((f) => ({ ...f, name: e.target.value }))}
                        placeholder="Item name"
                        invalid={!!errors.name}
                        autoFocus
                    />
                    <div>
                        <Input
                            value={form.description}
                            onChange={(e) => setForm((f) => ({ ...f, description: e.target.value }))}
                            placeholder="Description"
                            invalid={!!errors.description}
                        />
                        <Input
                            className="mt-1.5"
                            value={form.sub_description}
                            onChange={(e) => setForm((f) => ({ ...f, sub_description: e.target.value }))}
                            placeholder="Sub description"
                        />
                    </div>
                    <Input
                        type="number"
                        step="0.01"
                        min="0"
                        value={form.unit_cost}
                        onChange={(e) => setForm((f) => ({ ...f, unit_cost: e.target.value }))}
                        placeholder="Unit cost"
                        invalid={!!errors.unit_cost}
                    />
                    <Input
                        type="number"
                        step="1"
                        min="1"
                        value={form.quantity}
                        onChange={(e) => setForm((f) => ({ ...f, quantity: e.target.value }))}
                        placeholder="Qty"
                        invalid={!!errors.quantity}
                    />
                    <div className="flex gap-2">
                        <Button type="submit" loading={saving} disabled={!form.name.trim()}>
                            Add
                        </Button>
                        <Button type="button" variant="ghost" onClick={onDone}>
                            Cancel
                        </Button>
                    </div>
                </form>
            </td>
        </tr>
    );
}

function RepairPicker({ open, onClose, onSelect }) {
    const [repairs, setRepairs] = useState([]);
    const [search, setSearch] = useState('');
    const [debounced, setDebounced] = useState('');
    const [loading, setLoading] = useState(false);
    const timer = React.useRef(null);

    useEffect(() => {
        if (!open) {
            setSearch('');
            setDebounced('');
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
        api.get('/repairs', { params: { search: debounced } })
            .then(({ data }) => setRepairs(data.data.repairs.data))
            .catch(() => setRepairs([]))
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
                    <h3 className="text-sm font-semibold text-slate-900">Select a repair</h3>
                    <button type="button" onClick={onClose} className={iconButton} aria-label="Close">
                        <X className="h-4 w-4" />
                    </button>
                </div>

                <div className="border-b border-slate-100 px-5 py-3">
                    <Input
                        type="search"
                        value={search}
                        onChange={(e) => setSearch(e.target.value)}
                        placeholder="Search target, request, id..."
                        autoFocus
                    />
                </div>

                <div className="flex-1 overflow-y-auto">
                    {loading ? (
                        <div className="px-5 py-10 text-center text-sm text-slate-400">Loading...</div>
                    ) : repairs.length === 0 ? (
                        <div className="px-5 py-10 text-center text-sm text-slate-400">
                            Nothing has been found.
                        </div>
                    ) : (
                        repairs.map((repair) => (
                            <button
                                key={repair.id}
                                type="button"
                                onClick={() => onSelect(repair)}
                                className="flex w-full items-center justify-between gap-3 border-b border-slate-50 px-5 py-3 text-left transition-colors hover:bg-blue-50/60"
                            >
                                <span className="min-w-0">
                                    <span className="block truncate text-sm font-medium text-slate-800">
                                        #{repair.id} · {repair.target}
                                    </span>
                                    <span className="block truncate text-xs text-slate-500">
                                        {repair.customer_data
                                            ? `${repair.customer_data.first_name} ${repair.customer_data.last_name || ''}`
                                            : ''}
                                    </span>
                                </span>
                                <span className="shrink-0 text-xs text-slate-500">
                                    {repair.estimate != null ? `$ ${formatMoney(repair.estimate)}` : '—'}
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

export default function InvoiceView() {
    const { id } = useParams();
    const navigate = useNavigate();
    const [data, setData] = useState(null);
    const [form, setForm] = useState(null);
    const [errors, setErrors] = useState({});
    const [saving, setSaving] = useState(false);
    const [deleting, setDeleting] = useState(false);
    const [mailing, setMailing] = useState(false);
    const [editing, setEditing] = useState(false);
    const [addingItem, setAddingItem] = useState(false);
    const [scanOpen, setScanOpen] = useState(false);
    const [message, setMessage] = useState('');
    const [messageTone, setMessageTone] = useState('success');
    const [pickerOpen, setPickerOpen] = useState(false);
    const [partsOpen, setPartsOpen] = useState(false);
    const [repairsOpen, setRepairsOpen] = useState(false);
    const [payOpen, setPayOpen] = useState(false);
    const [payForm, setPayForm] = useState({ amount: '', method: 'cash', ref: '' });
    const [payErrors, setPayErrors] = useState({});
    const [paying, setPaying] = useState(false);
    const [logsOpen, setLogsOpen] = useState(false);
    const [logsPage, setLogsPage] = useState(1);
    const [barcode, setBarcode] = useState('');
    const [scanning, setScanning] = useState(false);

    const load = (page = logsPage) => {
        api.get(`/invoices/${id}`, { params: { page } })
            .then(({ data }) => {
                setData(data.data);
                const i = data.data.invoice;
                setForm({
                    company_name: i.company_name || '',
                    company_phone: i.company_phone || '',
                    company_email: i.company_email || '',
                    company_address: i.company_address || '',
                    customer_name: i.customer_name || '',
                    customer_phone: i.customer_phone || '',
                    customer_email: i.customer_email || '',
                    customer_address: i.customer_address || '',
                    customer_company: i.customer_company || '',
                    status: i.status != null ? String(i.status) : '',
                    tax_porcentage: i.tax_porcentage != null ? String(i.tax_porcentage) : '',
                });
            })
            .catch((err) => notify(getApiError(err, 'Could not load this invoice.'), 'error'));
    };

    useEffect(() => {
        setMessage('');
        setEditing(false);
        setLogsPage(1);
        load(1);
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [id]);

    const notify = (text, tone = 'success') => {
        setMessage(text);
        setMessageTone(tone);
        window.scrollTo({ top: 0, behavior: 'smooth' });
    };

    const handleUpdate = async (e) => {
        e.preventDefault();
        setErrors({});
        setSaving(true);
        try {
            const { data } = await api.put(`/invoices/${id}`, {
                ...form,
                status: form.status || null,
                tax_porcentage: form.tax_porcentage === '' ? 0 : Number(form.tax_porcentage),
            });
            notify(data.message);
            setEditing(false);
            load();
        } catch (err) {
            const response = err.response;
            if (response && response.data && response.data.errors) {
                setErrors(response.data.errors);
                setMessage(response.data.message);
                setMessageTone('error');
            } else {
                notify(getApiError(err), 'error');
            }
        } finally {
            setSaving(false);
        }
    };

    const handleAssignCustomer = async (customer) => {
        setPickerOpen(false);
        try {
            const { data } = await api.put(`/invoices/${id}/update-customer/${customer.id}`);
            notify(data.message);
            load();
        } catch (err) {
            notify(getApiError(err), 'error');
        }
    };

    const handleAddRepair = async (repair) => {
        setRepairsOpen(false);
        try {
            const { data } = await api.post(`/invoices/items/create-repair/${repair.id}/${id}`);
            notify(data.message);
            load();
        } catch (err) {
            notify(getApiError(err), 'error');
        }
    };

    const handleAddPart = async (product) => {
        setPartsOpen(false);
        try {
            const { data } = await api.post(`/inventory/invoices/${id}/products/${product.id}/sell`);
            notify(data.message);
            load();
        } catch (err) {
            notify(getApiError(err), 'error');
        }
    };

    const handleScanBarcode = async (e) => {
        e.preventDefault();
        if (!barcode.trim()) return;
        setScanning(true);
        try {
            const { data } = await api.post('/barcode/invoice', { invoice: id, barcode: barcode.trim() });
            const response = data.data ? data.data.response : null;
            notify(response || 'Product added to the invoice.');
            setBarcode('');
            setScanOpen(false);
            load();
        } catch (err) {
            notify(getApiError(err), 'error');
        } finally {
            setScanning(false);
        }
    };

    const handleCancelTransaction = async (transaction) => {
        if (!window.confirm('Remove this product line from the invoice?')) return;
        try {
            const { data } = await api.delete(`/inventory/invoice/${id}/transactions/${transaction.id}`);
            notify(data.message);
            load();
        } catch (err) {
            notify(getApiError(err), 'error');
        }
    };

    const handleCreatePayment = async (e) => {
        e.preventDefault();
        setPayErrors({});
        setPaying(true);
        try {
            const { data } = await api.post(`/payments/${id}`, {
                amount: Number(payForm.amount),
                method: payForm.method,
                ref: payForm.ref || null,
            });
            notify(data.message);
            setPayOpen(false);
            setPayForm({ amount: '', method: 'cash', ref: '' });
            load();
        } catch (err) {
            const response = err.response;
            if (response && response.data && response.data.errors) {
                setPayErrors(response.data.errors);
                notify(response.data.message || getApiError(err), 'error');
            } else {
                notify(getApiError(err), 'error');
            }
        } finally {
            setPaying(false);
        }
    };

    const handlePrint = async (task) => {
        try {
            const { data } = await api.get(`/invoices/${id}/print/${task}`, { responseType: 'blob' });
            const url = window.URL.createObjectURL(data);
            window.open(url, '_blank');
            setTimeout(() => window.URL.revokeObjectURL(url), 30000);
        } catch (err) {
            notify(getApiError(err, 'Could not generate the document.'), 'error');
        }
    };

    const handleMail = async () => {
        setMailing(true);
        try {
            const { data } = await api.post(`/invoices/${id}/email`);
            notify(data.message);
        } catch (err) {
            notify(getApiError(err, 'Could not send the receipt.'), 'error');
        } finally {
            setMailing(false);
        }
    };

    const handleDelete = async () => {
        if (!window.confirm('Delete this invoice? It can be restored later from the trash.')) return;
        setDeleting(true);
        try {
            await api.put(`/invoices/${id}/delete`);
            navigate('/invoices');
        } catch (err) {
            notify(getApiError(err), 'error');
            setDeleting(false);
        }
    };

    if (!data || !form) {
        return <div className="py-12 text-center text-sm text-slate-400">Loading...</div>;
    }

    const { invoice, items, transactions, payments, repairs, logs, statuses } = data;

    const balanceTone =
        invoice.balance < 0 ? 'text-emerald-600' : invoice.balance > 0 ? 'text-red-600' : 'text-slate-700';

    const fromBlock = editing ? (
        <div className="space-y-2">
            <Input
                value={form.company_name}
                onChange={(e) => setForm((f) => ({ ...f, company_name: e.target.value }))}
                placeholder="Company name"
            />
            <Input
                value={form.company_phone}
                onChange={(e) => setForm((f) => ({ ...f, company_phone: e.target.value }))}
                placeholder="Company phone"
            />
            <Input
                value={form.company_email}
                onChange={(e) => setForm((f) => ({ ...f, company_email: e.target.value }))}
                placeholder="Company email"
            />
            <Input
                value={form.company_address}
                onChange={(e) => setForm((f) => ({ ...f, company_address: e.target.value }))}
                placeholder="Company address"
            />
        </div>
    ) : (
        <div className="mt-2 space-y-0.5">
            <p className="text-sm font-semibold text-slate-900">{invoice.company_name || '—'}</p>
            <p className="text-sm text-slate-500">{invoice.company_phone || '—'}</p>
            <p className="text-sm text-slate-500">{invoice.company_email || '—'}</p>
            <p className="text-sm text-slate-500">{invoice.company_address || '—'}</p>
        </div>
    );

    const toBlock = editing ? (
        <div className="space-y-2">
            <Input
                value={form.customer_name}
                onChange={(e) => setForm((f) => ({ ...f, customer_name: e.target.value }))}
                placeholder="Customer name"
            />
            <Input
                value={form.customer_phone}
                onChange={(e) => setForm((f) => ({ ...f, customer_phone: e.target.value }))}
                placeholder="Customer phone"
            />
            <Input
                value={form.customer_email}
                onChange={(e) => setForm((f) => ({ ...f, customer_email: e.target.value }))}
                placeholder="Customer email"
            />
            <Input
                value={form.customer_address}
                onChange={(e) => setForm((f) => ({ ...f, customer_address: e.target.value }))}
                placeholder="Customer address"
            />
            <Input
                value={form.customer_company}
                onChange={(e) => setForm((f) => ({ ...f, customer_company: e.target.value }))}
                placeholder="Customer company"
            />
        </div>
    ) : (
        <div className="mt-2 space-y-0.5">
            <p className="text-sm font-semibold text-slate-900">{invoice.customer_name || '—'}</p>
            <p className="text-sm text-slate-500">{invoice.customer_phone || '—'}</p>
            <p className="text-sm text-slate-500">{invoice.customer_email || '—'}</p>
            <p className="text-sm text-slate-500">{invoice.customer_address || '—'}</p>
            <p className="text-sm text-slate-500">{invoice.customer_company || '—'}</p>
        </div>
    );

    return (
        <div className="mx-auto max-w-4xl space-y-6">
            {message && <Alert tone={messageTone}>{message}</Alert>}

            <div className="flex flex-wrap items-start justify-between gap-3">
                <div>
                    <Link to="/invoices" className="text-sm font-medium text-blue-700 hover:underline">
                        &larr; Back to invoices
                    </Link>
                    <div className="mt-2 flex items-center gap-3">
                        <h1 className="text-2xl font-bold tracking-tight text-slate-900">
                            Invoice #{invoice.id}
                        </h1>
                        {invoice.status_data && (
                            <Badge solid tone={toneFor(invoice.status_data.color)}>
                                {invoice.status_data.name}
                            </Badge>
                        )}
                    </div>
                    <p className="mt-1 text-sm text-slate-500">Created {formatDate(invoice.created_at)}</p>
                </div>
                <div className="flex flex-wrap items-center gap-2">
                    <Button type="button" variant="secondary" onClick={() => handlePrint('print')}>
                        <Printer className="h-4 w-4" />
                        Print
                    </Button>
                    <Button type="button" variant="secondary" onClick={() => handlePrint('receipt')}>
                        <FileText className="h-4 w-4" />
                        Receipt
                    </Button>
                    <Button type="button" variant="secondary" onClick={handleMail} loading={mailing}>
                        <Mail className="h-4 w-4" />
                        Email
                    </Button>
                    <button
                        type="button"
                        onClick={handleDelete}
                        disabled={deleting}
                        className="rounded-lg p-2 text-slate-400 transition-colors hover:bg-red-50 hover:text-red-600 disabled:opacity-50"
                        aria-label="Delete invoice"
                        title="Delete invoice"
                    >
                        <Trash2 className="h-4 w-4" />
                    </button>
                </div>
            </div>

            <Card className="overflow-hidden">
                <form onSubmit={handleUpdate}>
                    <div className="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 px-6 py-4">
                        <h2 className="text-sm font-semibold text-slate-900">Invoice details</h2>
                        {editing ? (
                            <div className="flex gap-2">
                                <Button
                                    type="button"
                                    variant="secondary"
                                    size="sm"
                                    onClick={() => {
                                        setEditing(false);
                                        setErrors({});
                                        load();
                                    }}
                                >
                                    Cancel
                                </Button>
                                <Button type="submit" size="sm" loading={saving}>
                                    <Save className="h-3.5 w-3.5" />
                                    Save
                                </Button>
                            </div>
                        ) : (
                            <Button
                                type="button"
                                variant="secondary"
                                size="sm"
                                onClick={() => setEditing(true)}
                            >
                                <Pencil className="h-3.5 w-3.5" />
                                Edit
                            </Button>
                        )}
                    </div>

                    <div className="grid gap-6 px-6 py-5 sm:grid-cols-2">
                        <div>
                            <p className="text-xs font-semibold uppercase tracking-wide text-slate-400">From</p>
                            {fromBlock}
                        </div>
                        <div>
                            <div className="flex items-center justify-between">
                                <p className="text-xs font-semibold uppercase tracking-wide text-slate-400">To</p>
                                {!editing && (
                                    <button
                                        type="button"
                                        onClick={() => setPickerOpen(true)}
                                        className="inline-flex items-center gap-1 text-xs font-medium text-blue-700 hover:underline"
                                    >
                                        <UserRound className="h-3.5 w-3.5" />
                                        {invoice.customer_id ? 'Reassign' : 'Assign'}
                                    </button>
                                )}
                            </div>
                            {toBlock}
                        </div>
                    </div>

                    <div className="flex flex-wrap items-center gap-x-8 gap-y-2 border-t border-slate-100 px-6 py-4">
                        <div className="flex items-center gap-2 text-sm">
                            <span className="text-slate-400">Status</span>
                            {editing ? (
                                <select
                                    className={`${fieldClasses} py-1`}
                                    value={form.status}
                                    onChange={(e) => setForm((f) => ({ ...f, status: e.target.value }))}
                                >
                                    <option value="">—</option>
                                    {statuses.map((s) => (
                                        <option key={s.id} value={s.id}>
                                            {s.name}
                                        </option>
                                    ))}
                                </select>
                            ) : invoice.status_data ? (
                                <Badge solid tone={toneFor(invoice.status_data.color)}>
                                    {invoice.status_data.name}
                                </Badge>
                            ) : (
                                <span className="text-slate-500">—</span>
                            )}
                        </div>
                        <div className="flex items-center gap-2 text-sm">
                            <span className="text-slate-400">Tax</span>
                            {editing ? (
                                <div className="w-24">
                                    <Input
                                        type="number"
                                        step="0.01"
                                        min="0"
                                        max="100"
                                        className="py-1"
                                        value={form.tax_porcentage}
                                        onChange={(e) =>
                                            setForm((f) => ({ ...f, tax_porcentage: e.target.value }))
                                        }
                                        invalid={!!errors.tax_porcentage}
                                    />
                                    <FieldError error={errors.tax_porcentage} />
                                </div>
                            ) : (
                                <span className="font-medium text-slate-700">{invoice.tax_porcentage}%</span>
                            )}
                        </div>
                    </div>
                </form>

                <div className="flex flex-wrap items-center justify-between gap-2 border-t border-slate-100 px-6 py-4">
                    <h2 className="text-sm font-semibold text-slate-900">Items</h2>
                    <div className="flex flex-wrap gap-2">
                        <Button
                            type="button"
                            variant="secondary"
                            size="sm"
                            onClick={() => setAddingItem(true)}
                        >
                            <Plus className="h-3.5 w-3.5" />
                            Add item
                        </Button>
                        <Button
                            type="button"
                            variant="secondary"
                            size="sm"
                            onClick={() => setRepairsOpen(true)}
                        >
                            <Wrench className="h-3.5 w-3.5" />
                            Add repair
                        </Button>
                        <Button
                            type="button"
                            variant="secondary"
                            size="sm"
                            onClick={() => setPartsOpen(true)}
                        >
                            <Plus className="h-3.5 w-3.5" />
                            Add product
                        </Button>
                        <Button
                            type="button"
                            variant="ghost"
                            size="sm"
                            onClick={() => setScanOpen((o) => !o)}
                        >
                            <ScanLine className="h-3.5 w-3.5" />
                            Scan
                        </Button>
                    </div>
                </div>

                {scanOpen && (
                    <form
                        onSubmit={handleScanBarcode}
                        className="flex gap-2 border-t border-slate-100 px-6 py-3"
                    >
                        <Input
                            value={barcode}
                            onChange={(e) => setBarcode(e.target.value)}
                            placeholder="Scan product barcode..."
                            className="min-w-0 flex-1"
                            autoFocus
                        />
                        <Button type="submit" variant="secondary" className="shrink-0" loading={scanning}>
                            Add
                        </Button>
                    </form>
                )}

                {items.length === 0 && transactions.length === 0 && !addingItem ? (
                    <EmptyBlock label="items" />
                ) : (
                    <div className="overflow-x-auto border-t border-slate-100">
                        <table className="w-full text-left">
                            <thead>
                                <tr className="bg-slate-50/60 text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    <th className="px-6 py-3">Item</th>
                                    <th className="px-6 py-3">Description</th>
                                    <th className="px-6 py-3">Unit</th>
                                    <th className="px-6 py-3">Qty</th>
                                    <th className="px-6 py-3 text-right">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                {items.map((item) => (
                                    <ItemRow
                                        key={item.id}
                                        invoiceId={invoice.id}
                                        item={item}
                                        onChanged={load}
                                        notify={notify}
                                    />
                                ))}
                                {transactions.map((t) => (
                                    <tr key={t.id} className="group border-b border-slate-100 last:border-0">
                                        <td className="px-6 py-3">
                                            <p className="text-sm font-medium text-slate-800">
                                                {t.product ? t.product.name : `#${t.product_id}`}
                                            </p>
                                            <span className="text-xs text-slate-400">Product</span>
                                        </td>
                                        <td className="px-6 py-3 text-xs text-slate-400">
                                            {t.product ? t.product.barcode : ''}
                                        </td>
                                        <td className="px-6 py-3 text-sm text-slate-600">
                                            {t.selling_price != null
                                                ? `$ ${formatMoney(t.selling_price)}`
                                                : '—'}
                                        </td>
                                        <td className="px-6 py-3 text-sm text-slate-600">{t.quantity}</td>
                                        <td className="px-6 py-3 text-right">
                                            <p className="text-sm font-medium text-slate-800">
                                                $ {formatMoney(t.selling_price * t.quantity)}
                                            </p>
                                            <button
                                                type="button"
                                                onClick={() => handleCancelTransaction(t)}
                                                className="mt-0.5 rounded-lg p-1.5 text-slate-400 opacity-0 transition-opacity hover:bg-red-50 hover:text-red-600 group-hover:opacity-100 focus:opacity-100"
                                                aria-label="Remove product"
                                            >
                                                <Trash2 className="h-3.5 w-3.5" />
                                            </button>
                                        </td>
                                    </tr>
                                ))}
                                {addingItem && (
                                    <NewItemForm
                                        invoiceId={invoice.id}
                                        onChanged={load}
                                        notify={notify}
                                        onDone={() => setAddingItem(false)}
                                    />
                                )}
                            </tbody>
                        </table>
                    </div>
                )}

                <div className="flex justify-end border-t border-slate-100 px-6 py-5">
                    <dl className="w-full max-w-xs space-y-2 text-sm">
                            <div className="flex justify-between gap-3">
                                <dt className="text-slate-400">Subtotal</dt>
                                <dd className="font-medium text-slate-700">$ {formatMoney(invoice.subtotal)}</dd>
                            </div>
                            <div className="flex justify-between gap-3">
                                <dt className="text-slate-400">Tax ({invoice.tax_porcentage}%)</dt>
                                <dd className="font-medium text-slate-700">$ {formatMoney(invoice.tax)}</dd>
                            </div>
                            <div className="flex justify-between gap-3">
                                <dt className="text-slate-400">Total</dt>
                                <dd className="font-semibold text-slate-900">$ {formatMoney(invoice.total)}</dd>
                            </div>
                            {payments.map((payment) => (
                                <div
                                    key={payment.id}
                                    className="flex justify-between gap-3 border-t border-slate-100 pt-2"
                                >
                                    <dt className="text-slate-400">
                                        <span className="uppercase">{payment.method}</span>
                                        {payment.ref ? ` · ${payment.ref}` : ''}
                                        <span className="mt-0.5 block text-[10px] text-slate-400">
                                            {formatDateTime(payment.created_at)}
                                        </span>
                                    </dt>
                                    <dd
                                        className={`font-medium ${
                                            payment.amount > 0 ? 'text-emerald-600' : 'text-red-600'
                                        }`}
                                    >
                                        $ {formatMoney(payment.amount)}
                                    </dd>
                                </div>
                            ))}
                            <div className="flex justify-between gap-3 border-t border-slate-100 pt-2">
                                <dt className="font-medium text-slate-500">Balance</dt>
                                <dd className={`text-base font-semibold ${balanceTone}`}>
                                    $ {formatMoney(invoice.balance)}
                                </dd>
                            </div>
                            {payOpen && (
                                <form
                                    onSubmit={handleCreatePayment}
                                    className="space-y-2 border-t border-slate-100 pt-3"
                                >
                                    <div className="flex gap-2">
                                        <Input
                                            type="number"
                                            step="0.01"
                                            value={payForm.amount}
                                            onChange={(e) =>
                                                setPayForm((f) => ({ ...f, amount: e.target.value }))
                                            }
                                            placeholder="Amount"
                                            invalid={!!payErrors.amount}
                                            autoFocus
                                        />
                                        <select
                                            value={payForm.method}
                                            onChange={(e) =>
                                                setPayForm((f) => ({ ...f, method: e.target.value }))
                                            }
                                            className="shrink-0 rounded-lg border border-slate-300 bg-white px-2 py-2 text-xs text-slate-800 shadow-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                                        >
                                            {['cash', 'card', 'check', 'other'].map((method) => (
                                                <option key={method} value={method}>
                                                    {method.toUpperCase()}
                                                </option>
                                            ))}
                                        </select>
                                    </div>
                                    {payErrors.amount && (
                                        <p className="text-xs text-red-600">{payErrors.amount[0]}</p>
                                    )}
                                    <Input
                                        value={payForm.ref}
                                        onChange={(e) =>
                                            setPayForm((f) => ({ ...f, ref: e.target.value }))
                                        }
                                        placeholder="Reference (optional)"
                                        invalid={!!payErrors.ref}
                                    />
                                    {payErrors.ref && (
                                        <p className="text-xs text-red-600">{payErrors.ref[0]}</p>
                                    )}
                                    <div className="flex justify-end gap-2">
                                        <Button
                                            type="button"
                                            variant="ghost"
                                            size="sm"
                                            onClick={() => setPayOpen(false)}
                                        >
                                            Cancel
                                        </Button>
                                        <Button
                                            type="submit"
                                            size="sm"
                                            loading={paying}
                                            disabled={!payForm.amount}
                                        >
                                            Record
                                        </Button>
                                    </div>
                                </form>
                            )}
                            {!payOpen && (
                                <div className="flex justify-end border-t border-slate-100 pt-2">
                                    <Button type="button" variant="secondary" size="sm" onClick={() => setPayOpen(true)}>
                                        <DollarSign className="h-3.5 w-3.5" />
                                        Record payment
                                    </Button>
                                </div>
                            )}
                        </dl>
                    </div>

                {repairs.length > 0 && (
                    <div className="flex flex-wrap items-center gap-x-4 gap-y-1 border-t border-slate-100 px-6 py-3">
                        <span className="text-xs font-semibold uppercase tracking-wide text-slate-400">
                            Linked repairs
                        </span>
                        {repairs.map((repair) => (
                            <Link
                                key={repair.id}
                                to={`/repairs/${repair.id}`}
                                className="text-xs font-medium text-blue-700 hover:underline"
                            >
                                #{repair.id} {repair.target}
                            </Link>
                        ))}
                    </div>
                )}
            </Card>

            <Card className="overflow-hidden">
                <button
                    type="button"
                    onClick={() => setLogsOpen((o) => !o)}
                    className="flex w-full items-center justify-between px-6 py-3 text-left transition-colors hover:bg-slate-50"
                >
                    <span className="text-xs font-semibold uppercase tracking-wide text-slate-500">
                        Activity log
                        <span className="ml-1.5 font-normal normal-case text-slate-400">({logs.total})</span>
                    </span>
                    <ChevronDown
                        className={`h-4 w-4 text-slate-400 transition-transform ${logsOpen ? 'rotate-180' : ''}`}
                    />
                </button>
                {logsOpen &&
                    (logs.data.length === 0 ? (
                        <EmptyBlock label="entries" />
                    ) : (
                        <>
                            <ul className="divide-y divide-slate-100 border-t border-slate-100">
                                {logs.data.map((log) => (
                                    <li key={log.id} className="px-6 py-2">
                                        <p className="text-xs leading-relaxed text-slate-600">{log.data}</p>
                                        <p className="mt-0.5 text-[10px] text-slate-400">
                                            {log.user_data ? log.user_data.name : '—'} &middot;{' '}
                                            {formatDate(log.created_at)}
                                        </p>
                                    </li>
                                ))}
                            </ul>
                            {logs.last_page > 1 && (
                                <Pagination
                                    paginator={logs}
                                    onChangePage={(page) => {
                                        setLogsPage(page);
                                        load(page);
                                    }}
                                />
                            )}
                        </>
                    ))}
            </Card>

            <ProductPicker open={partsOpen} onClose={() => setPartsOpen(false)} onSelect={handleAddPart} />
            <CustomerPicker
                open={pickerOpen}
                onClose={() => setPickerOpen(false)}
                onSelect={handleAssignCustomer}
            />
            <RepairPicker open={repairsOpen} onClose={() => setRepairsOpen(false)} onSelect={handleAddRepair} />
        </div>
    );
}