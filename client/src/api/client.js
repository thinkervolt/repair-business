import axios from 'axios';

const TOKEN_KEY = 'rb_token';

export function getToken() {
    return localStorage.getItem(TOKEN_KEY);
}

export function setToken(token) {
    if (token) {
        localStorage.setItem(TOKEN_KEY, token);
    } else {
        localStorage.removeItem(TOKEN_KEY);
    }
}

export function getLocale() {
    return localStorage.getItem('rb_locale') || 'en';
}

export function setLocale(locale) {
    localStorage.setItem('rb_locale', locale);
}

const api = axios.create({
    baseURL: '/api/v1',
    headers: {
        Accept: 'application/json',
        'Content-Type': 'application/json',
    },
});

api.interceptors.request.use((config) => {
    const token = getToken();
    if (token) {
        config.headers.Authorization = `Bearer ${token}`;
    }
    config.headers['Accept-Language'] = getLocale();
    return config;
});

api.interceptors.response.use(
    (response) => response,
    (error) => {
        if (error.response && error.response.status === 401) {
            const isLoginAttempt =
                error.config && error.config.url && error.config.url.includes('/auth/login');
            if (!isLoginAttempt) {
                setToken(null);
                if (window.location.pathname !== '/login') {
                    window.location.assign('/login');
                }
            }
        }
        return Promise.reject(error);
    }
);

export default api;