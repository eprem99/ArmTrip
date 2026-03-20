/**
 * Use translations injected from Laravel (window.__translations).
 * Keys are dot-separated, e.g. 'admin.sidebar.dashboard'.
 */
export function useI18n() {
    function t(key) {
        if (typeof window === 'undefined' || !window.__translations) {
            return key;
        }
        const value = key.split('.').reduce((obj, k) => obj?.[k], window.__translations);
        return value ?? key;
    }

    return {
        t,
        locale: typeof window !== 'undefined' ? window.__locale : 'en',
    };
}
