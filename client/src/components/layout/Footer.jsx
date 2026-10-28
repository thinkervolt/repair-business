import React from 'react';
import { useAuth } from '../../auth/AuthContext';
import { useI18n } from '../../i18n/I18nContext';

export default function Footer() {
    const { appName } = useAuth();
    const { t } = useI18n();

    return (
        <footer className="border-t border-slate-200 bg-white px-6 py-5">
            <p className="text-center text-xs text-slate-400">
                {t('footer.copyright', { name: appName, year: new Date().getFullYear() })}
            </p>
        </footer>
    );
}