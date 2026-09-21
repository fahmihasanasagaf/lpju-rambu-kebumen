import axios from 'axios';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';
import Alpine from 'alpinejs';

window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

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
    if (token) config.headers.Authorization = `Bearer ${token}`;
    return config;
});

Alpine.store('auth', {
    user: readUser(),
    token: readToken(),
    get isAuthenticated() { return Boolean(this.token); },
    get role() { return this.user?.role ?? null; },
    setSession(user, token) {
        this.user = user;
        this.token = token;
        try {
            window.localStorage.setItem(TOKEN_KEY, token);
            window.localStorage.setItem(USER_KEY, JSON.stringify(user));
        } catch { /* login remains active for this page */ }
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
