<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

class FrontendOrdersDashboardVisibilityTest extends TestCase
{
    public function test_orders_dashboard_hides_empty_summary_cards_filters_and_sections(): void
    {
        $template = file_get_contents(resource_path('views/frontend/home/orders/index.blade.php'));

        $this->assertStringContainsString("\$visibleSummaryCards = collect(\$summaryCards)", $template);
        $this->assertStringContainsString("->filter(fn (\$summaryCard) => (int) \$summaryCard['value'] > 0)", $template);
        $this->assertStringContainsString("\$visibleSections = \$sections", $template);
        $this->assertStringContainsString("->filter(fn (\$section) => \$section['items']->isNotEmpty())", $template);
        $this->assertStringContainsString('@foreach ($visibleSummaryCards as $summaryCard)', $template);
        $this->assertStringContainsString('@foreach ($visibleSections as $section)', $template);
        $this->assertStringContainsString('@forelse ($visibleSections as $section)', $template);
        $this->assertStringNotContainsString('@foreach ($summaryCards as $summaryCard)', $template);
        $this->assertStringNotContainsString('@foreach ($sections as $section)', $template);
        $this->assertNotSame('', trim(Blade::compileString($template)));
    }
}
