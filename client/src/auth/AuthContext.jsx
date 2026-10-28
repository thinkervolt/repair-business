import React, { createContext, useContext, useEffect, useMemo, useState, useCallback } from 'react';
import api, { getToken, hasLocale, setLocale, setToken } from '../api/client';

const AuthContext = createContext(null);

const DEFAULT_APP_NAME = 'Repair Business';

export function AuthProvider({ children }) {
    const [user, setUser] = useState(null);
    const [appName, setAppName] = useState(DEFAULT_APP_NAME);
    const [loading, setLoading] = useState(() => Boolean(getToken()));

    const applyAppName = useCallback((payload) => {
        const name = payload && payload.business_profile && payload.business_profile.name;
        setAppName((name && name.trim()) || DEFAULT_APP_NAME);
    }, []);

    useEffect(() => {
        let active = true;

        async function hydrate() {
            try {
                const { data } = await api.get('/public/business-profile');
                if (active && data.data && data.data.business_profile) {
                    applyAppName(data.data);
                }
            } catch (e) {
                // keep default app name when the public endpoint is unreachable
            }

            if (!getToken()) {
                setLoading(false);
                return;
            }
            try {
                const { data } = await api.get('/auth/me');
                if (active) {
                    setUser(data.data.user);
                    applyAppName(data.data);
                    if (!hasLocale() && data.data.locale) {
                        setLocale(data.data.locale);
                    }
                }
            } catch (e) {
                if (active) {
                    setUser(null);
                    setToken(null);
                }
            } finally {
                if (active) {
                    setLoading(false);
                }
            }
        }

        hydrate();

        return () => {
            active = false;
        };
    }, [applyAppName]);

    const login = useCallback(async (email, password) => {
        const { data } = await api.post('/auth/login', { email, password });
        setToken(data.data.token);
        setUser(data.data.user);
        applyAppName(data.data);
        if (!hasLocale() && data.data.locale) {
            setLocale(data.data.locale);
        }
        return data.data.user;
    }, [applyAppName]);

    const refreshProfile = useCallback(async () => {
        if (!getToken()) {
            return;
        }
        try {
            const { data } = await api.get('/auth/me');
            setUser(data.data.user);
            applyAppName(data.data);
        } catch (e) {
            // keep current state on failure
        }
    }, [applyAppName]);

    const logout = useCallback(async () => {
        try {
            await api.post('/auth/logout');
        } catch (e) {
            // token clearing is what matters locally
        }
        setToken(null);
        setUser(null);
    }, []);

    const value = useMemo(
        () => ({
            user,
            appName,
            loading,
            isAdmin: Boolean(user && user.role === 'admin'),
            isVerified: Boolean(user && user.email_verified_at),
            login,
            logout,
            refreshProfile,
        }),
        [user, appName, loading, login, logout, refreshProfile]
    );

    return <AuthContext.Provider value={value}>{children}</AuthContext.Provider>;
}

export function useAuth() {
    return useContext(AuthContext);
}