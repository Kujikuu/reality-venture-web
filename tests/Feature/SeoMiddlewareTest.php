<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class SeoMiddlewareTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_page_shares_default_seo_props(): void
    {
        $response = $this->get('/privacy-policy');

        $response->assertInertia(fn ($page) => $page
            ->has('seo')
            ->where('seo.title', 'Privacy Policy')
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

    public function test_home_page_overrides_seo_props(): void
    {
        $response = $this->get('/');

        $response->assertInertia(fn ($page) => $page
            ->where('seo.title', 'Accelerator & Incubator')
            ->has('seo.description')
            ->where('seo.ogType', 'website')
            ->has('seo.jsonLd')
        );
    }

    public function test_home_page_json_ld_is_organization(): void
    {
        $response = $this->get('/');

        $response->assertInertia(fn ($page) => $page
            ->where('seo.jsonLd.@type', 'Organization')
            ->where('seo.jsonLd.name', 'Reality Venture')
            ->has('seo.jsonLd.url')
        );
    }
}
