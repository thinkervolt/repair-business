import React, { useEffect, useState } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { Bell, LogOut, Menu, PanelLeftClose, PanelLeftOpen, UserRound } from 'lucide-react';
import api from '../../api/client';
import { useAuth } from '../../auth/AuthContext';
import { useI18n } from '../../i18n/I18nContext';
import Dropdown, { DropdownItem, DropdownLabel, DropdownSeparator } from '../ui/Dropdown';

function NotificationPill({ count }) {
    if (!count || count === 0) return null;
    return (
        <span className="absolute -right-0.5 -top-0.5 flex h-4 min-w-4 items-center justify-center rounded-full bg-red-500 px-1 text-[10px] font-bold text-white">
            {count}
        </span>
    );
}

const NOTIFICATION_ROUTES = {
    'view-customer': (ref) => `/customers/${ref}`,
    'view-repair': (ref) => `/repairs/${ref}`,
    'view-invoice': (ref) => `/invoices/${ref}`,
    'view-product': (ref) => `/inventory/products/${ref}`,
    'view-payment': (ref) => `/payments/${ref}`,
};

export default function Topbar({ collapsed, onToggleCollapsed, onToggleMobile }) {
    const { user, logout } = useAuth();
    const { t } = useI18n();
    const navigate = useNavigate();
    const [notifications, setNotifications] = useState({ notifications: [], count: 0 });

    const loadNotifications = () => {
        api.get('/notifications')
            .then(({ data }) => setNotifications(data.data))
            .catch(() => {});
    };

    useEffect(() => {
        loadNotifications();
        const timer = setInterval(loadNotifications, 60000);
        return () => clearInterval(timer);
    }, []);

    const handleNotificationClick = async (notification) => {
        setNotifications((prev) => ({
            notifications: prev.notifications.filter((n) => n.id !== notification.id),
            count: Math.max(0, prev.count - 1),
        }));
        try {
            const { data } = await api.delete(`/notifications/${notification.id}`);
            const { route, ref } = data.data;
            const path = NOTIFICATION_ROUTES[route] ? NOTIFICATION_ROUTES[route](ref) : null;
            if (path) {
                navigate(path);
            }
        } catch {
            loadNotifications();
        }
    };

    const handleLogout = async () => {
        await logout();
        window.location.assign('/login');
    };

    const initials = user
        ? user.name
              .split(' ')
              .map((p) => p[0])
              .slice(0, 2)
              .join('')
              .toUpperCase()
        : '';

    return (
        <header className="sticky top-0 z-30 flex h-16 items-center gap-3 border-b border-slate-200 bg-white/90 px-4 backdrop-blur lg:px-6">
            <button
                type="button"
                onClick={onToggleMobile}
                className="flex h-9 w-9 items-center justify-center rounded-lg text-slate-500 transition-colors hover:bg-slate-100 hover:text-slate-800 lg:hidden"
                aria-label="Open menu"
            >
                <Menu className="h-5 w-5" />
            </button>
            <button
                type="button"
                onClick={onToggleCollapsed}
                className="hidden h-9 w-9 items-center justify-center rounded-lg text-slate-500 transition-colors hover:bg-slate-100 hover:text-slate-800 lg:flex"
                aria-label={collapsed ? 'Expand sidebar' : 'Collapse sidebar'}
            >
                {collapsed ? <PanelLeftOpen className="h-5 w-5" /> : <PanelLeftClose className="h-5 w-5" />}
            </button>

            <div className="ml-auto flex items-center gap-1">
                <Dropdown
                    id="notifications-menu"
                    trigger={
                        <span className="relative flex h-9 w-9 items-center justify-center rounded-lg text-slate-500 transition-colors hover:bg-slate-100 hover:text-slate-800">
                            <Bell className="h-5 w-5" />
                            <NotificationPill count={notifications.count} />
                        </span>
                    }
                >
                    <DropdownLabel>{t('topbar.notifications')}</DropdownLabel>
                    <div className="max-h-72 overflow-y-auto scrollbar-thin">
                        {notifications.notifications.length > 0 ? (
                            notifications.notifications.map((n) => (
                                <DropdownItem key={n.id} onClick={() => handleNotificationClick(n)}>
                                    <div className="min-w-0">
                                        <p className="font-medium text-slate-700">{n.message}</p>
                                        {n.created_at && (
                                            <p className="text-xs text-slate-400">{n.created_at}</p>
                                        )}
                                    </div>
                                </DropdownItem>
                            ))
                        ) : (
                            <div className="px-4 py-3 text-sm text-slate-400">
                                {t('topbar.no_notifications')}
                            </div>
                        )}
                    </div>
                </Dropdown>

                <div className="mx-2 hidden h-6 w-px bg-slate-200 sm:block" />

                <Dropdown
                    id="user-menu"
                    trigger={
                        <span className="flex items-center gap-2.5 rounded-lg py-1.5 pl-1.5 pr-2.5 transition-colors hover:bg-slate-100">
                            <span className="flex h-8 w-8 items-center justify-center rounded-full bg-blue-600 text-xs font-bold text-white">
                                {initials}
                            </span>
                            <span className="hidden text-sm font-medium text-slate-700 sm:block">
                                {user ? user.name : ''}
                            </span>
                        </span>
                    }
                >
                    {user && (
                        <DropdownLabel>
                            {t('topbar.signed_in_as', { email: user.email })}
                        </DropdownLabel>
                    )}
                    <Link to="/profile">
                        <DropdownItem icon={UserRound}>{t('topbar.profile')}</DropdownItem>
                    </Link>
                    <DropdownSeparator />
                    <DropdownItem icon={LogOut} danger onClick={handleLogout}>
                        {t('topbar.logout')}
                    </DropdownItem>
                </Dropdown>
            </div>
        </header>
    );
}