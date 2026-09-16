import React, { createContext, useContext, useEffect, useMemo, useState, useCallback } from 'react';
import api, { getToken, setToken } from '../api/client';

const AuthContext = createContext(null);

export function AuthProvider({ children }) {
    const [user, setUser] = useState(null);
    const [appName, setAppName] = useState('REPAIR-BUSINESS');
    const [loading, setLoading] = useState(() => Boolean(getToken()));

    useEffect(() => {
        let active = true;

        async function hydrate() {
            if (!getToken()) {
                setLoading(false);
                return;
            }
            try {
                const { data } = await api.get('/auth/me');
                if (active) {
                    setUser(data.data.user);
                    setAppName(data.data.app_name || 'REPAIR-BUSINESS');
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
    }, []);

    const login = useCallback(async (email, password) => {
        const { data } = await api.post('/auth/login', { email, password });
        setToken(data.data.token);
        setUser(data.data.user);
        setAppName(data.data.app_name || 'REPAIR-BUSINESS');
        return data.data.user;
    }, []);

    const register = useCallback(async (payload) => {
        await api.post('/auth/register', payload);
    }, []);

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
            register,
            logout,
        }),
        [user, appName, loading, login, register, logout]
    );

    return <AuthContext.Provider value={value}>{children}</AuthContext.Provider>;
}

export function useAuth() {
    return useContext(AuthContext);
}