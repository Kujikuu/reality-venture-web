import React from 'react';
import './i18n';
import { createInertiaApp } from '@inertiajs/react';
import { createRoot } from 'react-dom/client';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { Header } from './Components/Header';
import { Footer } from './Components/Footer';
import { DirectionProvider } from './Components/DirectionProvider';
import { LenisProvider } from './Components/LenisProvider';

createInertiaApp({
    title: (title) => `${title} - Reality Venture`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.tsx`,
            import.meta.glob('./Pages/**/*.tsx')
        ),
    setup({ el, App, props }) {
        createRoot(el).render(
            <React.StrictMode>
                <DirectionProvider>
                    <LenisProvider>
                        <div className="min-h-screen bg-background-light text-text-main font-sans antialiased flex flex-col">
                            <Header />
                            <main className="flex-1 w-full flex flex-col">
                                <App {...props} />
                            </main>
                            <Footer />
                        </div>
                    </LenisProvider>
                </DirectionProvider>
            </React.StrictMode>
        );
    },
    progress: {
        color: '#4d3070',
    },
});
