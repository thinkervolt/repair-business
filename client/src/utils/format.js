export function formatMoney(value) {
    return Number(value || 0).toLocaleString('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    });
}

export function formatPhone(phone) {
    const s = String(phone || '');
    return s.replace(/^(\d{3})(\d{3})(\d{4})$/, '$1-$2-$3');
}

export function formatDate(value, options = {}) {
    if (!value) return '';
    const defaults = { year: 'numeric', month: 'short', day: 'numeric' };
    return new Date(value).toLocaleDateString('en-US', options.year ? options : defaults);
}

export function formatDateTime(value) {
    if (!value) return '';
    return new Date(value).toLocaleString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: 'numeric',
        minute: '2-digit',
    });
}

export function getApiError(error, fallback = 'Something went wrong.') {
    if (error.response) {
        const { data } = error.response;
        if (data && data.errors) {
            const first = Object.values(data.errors)[0];
            return Array.isArray(first) ? first[0] : first;
        }
        if (data && data.message) {
            return data.message;
        }
    }
    return error.message || fallback;
}