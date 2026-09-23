import axios from 'axios';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';
import Alpine from 'alpinejs';

window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.headers.common['Accept'] = 'application/json';

const TOKEN_KEY = 'lpju_auth_token';
const USER_KEY = 'lpju_auth_user';

const readToken = () => {
    try { return window.localStorage.getItem(TOKEN_KEY); } catch { return null; }
};

const readUser = () => {
    try { return JSON.parse(window.localStorage.getItem(USER_KEY) || 'null'); } catch { return null; }
};

const clearAuth = () => {
    try {
        window.localStorage.removeItem(TOKEN_KEY);
        window.localStorage.removeItem(USER_KEY);
    } catch { /* storage can be unavailable in private browsing */ }
};

axios.interceptors.request.use((config) => {
    const token = readToken();
    config.headers = config.headers || {};
    config.headers.Accept = 'application/json';
    if (token) config.headers.Authorization = `Bearer ${token}`;
    return config;
});

axios.interceptors.response.use(
    (response) => response,
    (error) => {
        if (error.response?.status === 401) {
            console.warn('[auth] unauthorized response', { url: error.config?.url, origin: window.location.origin });
        }
        return Promise.reject(error);
    },
);

Alpine.store('auth', {
    user: readUser(),
    token: readToken(),
    get isAuthenticated() { return Boolean(this.token); },
    get role() { return this.user?.role ?? null; },
    setSession(user, token) {
        const sessionToken = String(token || '').trim();
        if (!sessionToken) throw new Error('Token login tidak diterima dari server.');
        this.user = user;
        this.token = sessionToken;
        try {
            window.localStorage.setItem(TOKEN_KEY, sessionToken);
            window.localStorage.setItem(USER_KEY, JSON.stringify(user));
            const storedToken = window.localStorage.getItem(TOKEN_KEY);
            if (storedToken !== sessionToken) throw new Error('Token login tidak dapat disimpan pada origin ini.');
        } catch (error) {
            this.user = null;
            this.token = null;
            clearAuth();
            console.error('[auth] session storage failed', { key: TOKEN_KEY, origin: window.location.origin });
            throw error;
        }
    },
    async logout() {
        try {
            if (this.token) await axios.post('/api/logout');
        } catch { /* the local session must still be cleared */ }
        this.user = null;
        this.token = null;
        clearAuth();
        window.location.href = '/login';
    },
});

window.Alpine = Alpine;
window.Leaflet = L;
Alpine.start();
