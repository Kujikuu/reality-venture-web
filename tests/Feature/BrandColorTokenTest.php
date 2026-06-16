<?php

namespace Tests\Feature;

use Tests\TestCase;

class BrandColorTokenTest extends TestCase
{
    public function test_brand_color_tokens_are_updated_across_active_surfaces(): void
    {
        $files = [
            base_path('tailwind.config.js'),
            resource_path('css/app.css'),
            resource_path('js/app.tsx'),
            resource_path('js/Components/animations/HeroAnimations.ts'),
            app_path('Providers/Filament/AdminPanelProvider.php'),
            resource_path('views/vendor/mail/html/themes/default.css'),
            resource_path('views/mail/demo-day-invitation.blade.php'),
            resource_path('views/mail/general-application-confirmation.blade.php'),
            resource_path('views/mail/new-application-submitted.blade.php'),
            resource_path('views/mail/stage-applying.blade.php'),
            resource_path('views/mail/stage-decision.blade.php'),
            resource_path('views/mail/stage-demo-day-welcome.blade.php'),
            resource_path('views/mail/stage-evaluation.blade.php'),
            resource_path('views/mail/startup-application-confirmation.blade.php'),
            resource_path('views/mail/welcome-to-club.blade.php'),
        ];

        $contents = collect($files)
            ->map(fn (string $file): string => file_get_contents($file))
            ->implode(PHP_EOL);

        $this->assertStringContainsString('#062D2D', $contents);
        $this->assertStringContainsString('#EAF3F2', $contents);
        $this->assertStringContainsString('#8F6B32', $contents);
        $this->assertStringContainsString('rgba(6, 45, 45, 0.04)', $contents);
        $this->assertStringContainsString('rgba(143, 107, 50, 0.08)', $contents);
        $this->assertStringContainsString('rgba(6, 45, 45, 0.35)', $contents);

        foreach ([
            '#4d3070',
            '#f3edf8',
            '#e4d7f0',
            '#cbb2e1',
            '#b08bd2',
            '#9564c3',
            '#623194',
            '#4d2574',
            '#391a55',
            '#261035',
            '#c88b00',
            '#fff8e8',
            '#ffeebb',
            '#ffd980',
            '#ffc24d',
            '#e6a319',
            '#a66f00',
            '#855700',
            '#664400',
            '#4a3100',
            '#e8e5ef',
            '#b0adc5',
        ] as $oldBrandColor) {
            $this->assertStringNotContainsStringIgnoringCase($oldBrandColor, $contents);
        }

        $this->assertStringNotContainsString('rgba(223, 104, 55', $contents);
        $this->assertStringNotContainsString('rgba(255, 237, 213', $contents);
    }
}
