import { useState, useEffect, useCallback } from 'react';
import { useDesksApi } from './useDesksApi';

interface DesksUser {
    id: number;
    name: string;
    email: string;
    phone: string | null;
    locale: string;
    roles: string[];
}

interface AuthErrors {
    [key: string]: string[];
}

export function useDesksAuth() {
    const { fetchApi } = useDesksApi();
    const [user, setUser] = useState<DesksUser | null>(null);
    const [loading, setLoading] = useState(true);
    const [showModal, setShowModal] = useState(false);
    const [authError, setAuthError] = useState('');
    const [authErrors, setAuthErrors] = useState<AuthErrors>({});
    const [authLoading, setAuthLoading] = useState(false);

    const isAuthenticated = !!user;

    const fetchProfile = useCallback(async () => {
        const token = localStorage.getItem('desks_token');
        if (!token) {
            setLoading(false);
            return;
        }

        try {
            const res = await fetchApi('/api/v1/me');
            if (res.ok) {
                const json = await res.json();
                setUser(json.data || json);
            } else {
                localStorage.removeItem('desks_token');
            }
        } catch {
            // silently fail
        } finally {
            setLoading(false);
        }
    }, [fetchApi]);

    useEffect(() => {
        fetchProfile();
    }, [fetchProfile]);

    const login = useCallback(async (email: string, password: string): Promise<boolean> => {
        setAuthLoading(true);
        setAuthError('');
        setAuthErrors({});

        try {
            const res = await fetchApi('/api/v1/auth/login', {
                method: 'POST',
                body: JSON.stringify({ email, password }),
            });

            const json = await res.json();

            if (!res.ok) {
                if (json.errors && typeof json.errors === 'object') {
                    setAuthErrors(json.errors);
                } else {
                    setAuthError(json.error || json.message || 'Login failed');
                }
                return false;
            }

            const token = json.data?.token || json.token;
            localStorage.setItem('desks_token', token);
            await fetchProfile();
            setShowModal(false);
            return true;
        } catch {
            setAuthError('Login failed');
            return false;
        } finally {
            setAuthLoading(false);
        }
    }, [fetchApi, fetchProfile]);

    const register = useCallback(async (data: {
        name: string;
        email: string;
        phone: string;
        password: string;
        password_confirmation: string;
    }): Promise<boolean> => {
        setAuthLoading(true);
        setAuthError('');
        setAuthErrors({});

        try {
            const res = await fetchApi('/api/v1/auth/register', {
                method: 'POST',
                body: JSON.stringify(data),
            });

            const json = await res.json();

            if (!res.ok) {
                if (json.errors && typeof json.errors === 'object') {
                    setAuthErrors(json.errors);
                } else {
                    setAuthError(json.error || json.message || 'Registration failed');
                }
                return false;
            }

            const token = json.data?.token || json.token;
            localStorage.setItem('desks_token', token);
            await fetchProfile();
            setShowModal(false);
            return true;
        } catch {
            setAuthError('Registration failed');
            return false;
        } finally {
            setAuthLoading(false);
        }
    }, [fetchApi, fetchProfile]);

    const logout = useCallback(async () => {
        const token = localStorage.getItem('desks_token');
        localStorage.removeItem('desks_token');
        setUser(null);

        if (token) {
            fetchApi('/api/v1/auth/logout', { method: 'POST' }).catch(() => {});
        }
    }, [fetchApi]);

    const clearErrors = useCallback(() => {
        setAuthError('');
        setAuthErrors({});
    }, []);

    return {
        user,
        isAuthenticated,
        loading,
        showModal,
        setShowModal,
        login,
        register,
        logout,
        authError,
        authErrors,
        authLoading,
        clearErrors,
    };
}
