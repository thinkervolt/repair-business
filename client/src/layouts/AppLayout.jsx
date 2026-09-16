import React, { useState } from 'react';
import { Navigate, Outlet } from 'react-router-dom';
import { MailCheck } from 'lucide-react';
import { useAuth } from '../auth/AuthContext';
import Sidebar from '../components/layout/Sidebar';
import Topbar from '../components/layout/Topbar';
import Footer from '../components/layout/Footer';
import Alert from '../components/ui/Alert';
import api from '../api/client';

function VerifyBanner({ email }) {
    const [sent, setSent] = useState(false);
    const [error, setError] = useState('');

    const resend = async () => {
        setError('');
        try {
            await api.post('/auth/email/resend');
            setSent(true);
        } catch (err) {
            setError('We could not resend the verification email.');
        }
    };

    return (
        <Alert tone="warning" className="mb-6">
            <div className="flex flex-wrap items-center gap-x-3 gap-y-2">
                <MailCheck className="h-5 w-5 shrink-0 text-amber-500" />
                <div>
                    <strong>Verify your email address.</strong>{' '}
                    <span>
                        Check <span className="font-medium">{email}</span> and open the verification
                        link we sent you.
                    </span>
                </div>
                <button
                    type="button"
                    onClick={resend}
                    className="ml-auto text-sm font-semibold text-amber-800 underline-offset-2 hover:underline"
                >
                    {sent ? 'Verification email sent!' : 'Resend verification email'}
                </button>
            </div>
            {error && <p className="mt-1">{error}</p>}
        </Alert>
    );
}

export default function AppLayout() {
    const { user, isVerified } = useAuth();
    const [mobileOpen, setMobileOpen] = useState(false);
    const [collapsed, setCollapsed] = useState(false);

    if (!user) {
        return <Navigate to="/login" replace />;
    }

    return (
        <div className="flex min-h-screen flex-col">
            <Sidebar
                collapsed={collapsed}
                mobileOpen={mobileOpen}
                onCloseMobile={() => setMobileOpen(false)}
            />
            <div
                className={[
                    'flex min-h-screen flex-col transition-[padding] duration-200 lg:pl-64',
                    collapsed && 'lg:pl-[72px]',
                ].join(' ')}
            >
                <Topbar
                    collapsed={collapsed}
                    onToggleCollapsed={() => setCollapsed((c) => !c)}
                    onToggleMobile={() => setMobileOpen((o) => !o)}
                />
                <main className="mx-auto w-full max-w-7xl flex-1 p-4 sm:p-6 lg:p-8">
                    {!isVerified && <VerifyBanner email={user.email} />}
                    <Outlet />
                </main>
                <Footer />
            </div>
        </div>
    );
}