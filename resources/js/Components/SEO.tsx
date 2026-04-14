import { Head, usePage } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';

interface SeoProps {
    title?: string;
    description?: string;
    canonical?: string;
    ogImage?: string;
    ogType?: string;
    robots?: string;
    jsonLd?: Record<string, unknown> | null;
}

interface PageProps {
    seo: SeoProps;
    [key: string]: unknown;
}

export function SEO(overrides: Partial<SeoProps> = {}) {
    const { seo } = usePage<PageProps>().props;
    const { t } = useTranslation('common');

    const title = overrides.title ?? seo.title;
    const description = overrides.description ?? seo.description;
    const canonical = overrides.canonical ?? seo.canonical;
    const ogImage = overrides.ogImage ?? seo.ogImage;
    const ogType = overrides.ogType ?? seo.ogType;
    const robots = overrides.robots ?? seo.robots;

    return (
        <Head>
            {title && <title>{title}</title>}
            {description && <meta name="description" content={description} />}
            {robots && <meta name="robots" content={robots} />}
            {canonical && <link rel="canonical" href={canonical} />}
            {title && <meta property="og:title" content={title} />}
            {description && <meta property="og:description" content={description} />}
            {ogImage && <meta property="og:image" content={ogImage} />}
            {canonical && <meta property="og:url" content={canonical} />}
            {ogType && <meta property="og:type" content={ogType} />}
            <meta property="og:site_name" content={t('company.name')} />
            <meta name="twitter:card" content="summary_large_image" />
            {title && <meta name="twitter:title" content={title} />}
            {description && <meta name="twitter:description" content={description} />}
            {ogImage && <meta name="twitter:image" content={ogImage} />}
        </Head>
    );
}
