import React from 'react';
import './i18n';
import { createInertiaApp, usePage } from '@inertiajs/react';
import { createRoot } from 'react-dom/client';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { Header } from './Components/Header';
import { Footer } from './Components/Footer';
import { DirectionProvider } from './Components/DirectionProvider';
import { LenisProvider } from './Components/LenisProvider';

const HIDE_SHELL_PAGES = [
    'Auth/',
    'Dashboard/',
    'Consultant/ProfileEdit',
];

function AppLayout({ children }: { children: React.ReactNode }) {
    const { component } = usePage();
    const isDashboardPage = HIDE_SHELL_PAGES.some(prefix => component.startsWith(prefix));

    return (
        <div className="min-h-screen bg-background-light text-text-main font-sans antialiased flex flex-col">
            {!isDashboardPage && <Header />}
            <main className="flex-1 w-full flex flex-col">
                {children}
            </main>
            {!isDashboardPage && <Footer />}
        </div>
    );
}

createInertiaApp({
    title: (title) => (title ? `${title} - Reality Venture` : 'Reality Venture'),
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.tsx`,
            import.meta.glob('./Pages/**/*.tsx')
        ).then((page: any) => {
            page.default.layout ??= (page: React.ReactNode) => <AppLayout>{page}</AppLayout>;
            return page;
        }),
    setup({ el, App, props }) {
        createRoot(el).render(
            <React.StrictMode>
                <DirectionProvider>
                    <LenisProvider>
                        <App {...props} />
                    </LenisProvider>
                </DirectionProvider>
            </React.StrictMode>
        );
    },
    progress: {
        color: '#4d3070',
    },
});
