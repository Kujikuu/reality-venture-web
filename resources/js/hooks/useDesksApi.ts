import { useCallback } from 'react';

export function useDesksApi() {
    const fetchApi = useCallback(async (path: string, options: RequestInit = {}) => {
        const config = window.desksConfig;
        const token = localStorage.getItem('desks_token');

        const headers: Record<string, string> = {
            'Accept': 'application/json',
            'X-Site-Key': config.siteKey,
            'Accept-Language': config.locale,
            ...(options.headers as Record<string, string> || {}),
        };

        if (token) {
            headers['Authorization'] = `Bearer ${token}`;
        }

        if (options.body && typeof options.body === 'string') {
            headers['Content-Type'] = 'application/json';
        }

        return fetch(`${config.apiUrl}${path}`, {
            ...options,
            headers,
        });
    }, []);

    return { fetchApi };
}
