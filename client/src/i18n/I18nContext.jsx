import React, { createContext, useContext, useEffect, useMemo, useState } from 'react';
import { getLocale } from '../api/client';
import { locales } from './locales';

const I18nContext = createContext(null);

export function I18nProvider({ children }) {
    const [locale, setLocaleState] = useState(() => (getLocale() === 'es' ? 'es' : 'en'));

    useEffect(() => {
        const onLocaleChange = () => {
            setLocaleState(getLocale() === 'es' ? 'es' : 'en');
        };
        window.addEventListener('rb:localechange', onLocaleChange);
        return () => window.removeEventListener('rb:localechange', onLocaleChange);
    }, []);

    const value = useMemo(() => {
        const dict = locales[locale] || locales.en;
        const t = (key, vars) => {
            let s = dict[key] !== undefined ? dict[key] : key;
            if (vars) {
                Object.entries(vars).forEach(([k, v]) => {
                    s = s.split(`{${k}}`).join(String(v));
                });
            }
            return s;
        };
        return { locale, t };
    }, [locale]);

    return <I18nContext.Provider value={value}>{children}</I18nContext.Provider>;
}

export function useI18n() {
    return useContext(I18nContext);
}