<?php

namespace Tests\Feature;

use Tests\TestCase;

class SeoMiddlewareTest extends TestCase
{
    public function test_page_shares_default_seo_props(): void
    {
        $response = $this->get('/privacy-policy');

        $response->assertInertia(fn ($page) => $page
            ->has('seo')
            ->where('seo.title', config('app.name'))
            ->where('seo.ogType', 'website')
            ->where('seo.robots', 'index, follow')
            ->has('seo.description')
            ->has('seo.canonical')
            ->has('seo.ogImage')
        );
    }

    public function test_canonical_url_matches_current_request(): void
    {
        $response = $this->get('/terms-of-service');

        $response->assertInertia(fn ($page) => $page
            ->where('seo.canonical', url('/terms-of-service'))
        );
    }

    public function test_seo_props_include_json_ld_as_null(): void
    {
        $response = $this->get('/privacy-policy');

        $response->assertInertia(fn ($page) => $page
            ->where('seo.jsonLd', null)
        );
    }
}
