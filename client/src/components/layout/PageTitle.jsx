import { useEffect } from 'react';
import { useLocation } from 'react-router-dom';
import { useAuth } from '../../auth/AuthContext';
import { useI18n } from '../../i18n/I18nContext';

const ROUTE_TITLES = [
    { path: '/login', key: 'auth.sign_in' },
    { path: '/forgot-password', key: 'auth.forgot_title' },
    { path: '/reset-password', key: 'auth.reset_title' },
    { path: '/verify-email', key: 'auth.email_verification' },
    { path: '/dashboard', key: 'nav.dashboard' },
    { path: '/customers/create', key: 'customers.create' },
    { path: '/customers/:id/edit', key: 'customers.edit' },
    { path: '/customers/:id', key: 'customers.view.details' },
    { path: '/customers', key: 'nav.all_customers' },
    { path: '/repairs/create', key: 'repairs.create' },
    { path: '/repairs/settings', key: 'repairs.settings.title' },
    { path: '/repairs/:id', key: 'repairs.details' },
    { path: '/repairs', key: 'nav.all_repairs' },
    { path: '/invoices/create', key: 'invoices.create' },
    { path: '/invoices/settings', key: 'invoices.settings.title' },
    { path: '/invoices/:id', key: 'invoices.details' },
    { path: '/invoices', key: 'nav.all_invoices' },
    { path: '/inventory/products/create', key: 'inventory.create_product' },
    { path: '/inventory/products/:id/edit', key: 'inventory.edit_product' },
    { path: '/inventory/products/:id', key: 'inventory.product_info' },
    { path: '/inventory/products', key: 'nav.products' },
    { path: '/inventory/transactions', key: 'nav.transactions' },
    { path: '/inventory/categories', key: 'nav.categories' },
    { path: '/payments/:id', key: 'payments.details' },
    { path: '/payments', key: 'nav.payments' },
    { path: '/reports/register', key: 'nav.register_report' },
    { path: '/reports', key: 'nav.create_report' },
    { path: '/settings', key: 'nav.settings' },
    { path: '/users', key: 'nav.users' },
    { path: '/logs', key: 'nav.activity_log' },
    { path: '/trash', key: 'nav.trash' },
    { path: '/profile', key: 'profile.title' },
];

function matchRoute(pathname) {
    const segments = pathname.split('/').filter(Boolean);

    for (const entry of ROUTE_TITLES) {
        const parts = entry.path.split('/').filter(Boolean);
        if (parts.length !== segments.length) {
            continue;
        }
        let matched = true;
        for (let i = 0; i < parts.length; i++) {
            if (!parts[i].startsWith(':') && parts[i] !== segments[i]) {
                matched = false;
                break;
            }
        }
        if (matched) {
            return entry;
        }
    }

    return null;
}

export default function PageTitle() {
    const { pathname } = useLocation();
    const { appName } = useAuth();
    const { t } = useI18n();

    useEffect(() => {
        const route = matchRoute(pathname);
        const name = route ? t(route.key) : '';
        document.title = [appName, name].filter(Boolean).join(' | ');
    }, [pathname, appName, t]);

    return null;
}