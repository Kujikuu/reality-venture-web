import { useCallback } from 'react';
import { useTranslation } from 'react-i18next';

export function useDesksApi() {
    const { i18n } = useTranslation();

    const fetchApi = useCallback(async (path: string, options: RequestInit = {}) => {
        const config = window.desksConfig;
        const token = localStorage.getItem('desks_token');

        const headers: Record<string, string> = {
            'Accept': 'application/json',
            'X-Site-Key': config.siteKey,
            'Accept-Language': i18n.language || config.locale,
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
    }, [i18n.language]);

    return { fetchApi };
}
