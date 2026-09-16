import React, { useEffect, useState } from 'react';
import { useNavigate, useParams, Link } from 'react-router-dom';
import { Save } from 'lucide-react';
import api from '../../api/client';
import { getApiError } from '../../utils/format';
import Card from '../../components/ui/Card';
import Button from '../../components/ui/Button';
import Alert from '../../components/ui/Alert';
import CustomerFields from '../../components/customers/CustomerFields';

const EMPTY = {
    first_name: '',
    last_name: '',
    phone: '',
    email: '',
    address: '',
    city: '',
    state: '',
    zip: '',
    company: '',
};

export default function CustomerForm() {
    const { id } = useParams();
    const navigate = useNavigate();
    const isEdit = Boolean(id);

    const [form, setForm] = useState(EMPTY);
    const [errors, setErrors] = useState({});
    const [error, setError] = useState('');
    const [success, setSuccess] = useState('');
    const [submitting, setSubmitting] = useState(false);
    const [loading, setLoading] = useState(isEdit);

    useEffect(() => {
        if (!isEdit) return;
        api.get(`/customers/${id}`)
            .then(({ data }) => {
                const { first_name, last_name, phone, email, address, city, state, zip, company } =
                    data.data.customer;
                setForm({ first_name, last_name, phone, email, address, city, state, zip, company });
            })
            .catch((err) => setError(getApiError(err, 'Could not load this customer.')))
            .finally(() => setLoading(false));
    }, [id, isEdit]);

    const handleSubmit = async (e) => {
        e.preventDefault();
        setError('');
        setSuccess('');
        setErrors({});
        setSubmitting(true);
        try {
            const { data } = isEdit
                ? await api.put(`/customers/${id}`, form)
                : await api.post('/customers', form);
            if (isEdit) {
                setSuccess(data.message);
            } else {
                navigate(`/customers/${data.data.id}`);
            }
        } catch (err) {
            const response = err.response;
            if (response && response.data && response.data.errors) {
                setErrors(response.data.errors);
                setError(response.data.message);
            } else {
                setError(getApiError(err));
            }
        } finally {
            setSubmitting(false);
        }
    };

    return (
        <div className="mx-auto max-w-3xl space-y-6">
            <Link to="/customers" className="text-sm font-medium text-blue-700 hover:underline">
                &larr; Back to customers
            </Link>
            <div>
                <h1 className="text-2xl font-bold tracking-tight text-slate-900">
                    {isEdit ? 'Edit customer' : 'Create customer'}
                </h1>
                <p className="mt-1 text-sm text-slate-500">
                    {isEdit
                        ? 'Update the details of this customer.'
                        : 'Add a new customer to your business.'}
                </p>
            </div>

            {error && (
                <Alert tone="error">{error}</Alert>
            )}
            {success && (
                <Alert tone="success">{success}</Alert>
            )}

            <Card className="p-6">
                {loading ? (
                    <div className="py-8 text-center text-sm text-slate-400">Loading...</div>
                ) : (
                    <form onSubmit={handleSubmit} className="space-y-6">
                        <CustomerFields value={form} onChange={setForm} errors={errors} />
                        <div className="flex justify-end gap-3 border-t border-slate-100 pt-5">
                            <Link to="/customers">
                                <Button type="button" variant="secondary">
                                    Cancel
                                </Button>
                            </Link>
                            <Button type="submit" loading={submitting}>
                                <Save className="h-4 w-4" />
                                {isEdit ? 'Update customer' : 'Create customer'}
                            </Button>
                        </div>
                    </form>
                )}
            </Card>
        </div>
    );
}