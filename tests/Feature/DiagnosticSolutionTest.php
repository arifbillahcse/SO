<?php

namespace Tests\Feature;

use DOMDocument;
use DOMXPath;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class DiagnosticSolutionTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_diagnostic_solution_describes_all_phases_and_the_design_approval_gate(): void
    {
        $response = $this->get(route('diagnostic-centers-solution'))->assertOk()
            ->assertSee('This is a development roadmap, not a ready-to-use product.')
            ->assertSee('Phase 0 produces design documents only')
            ->assertSee('500 patients and 1,000 tests per day')
            ->assertSee('test targets, not verified production capacity')
            ->assertSee('PACS image viewing')->assertSee('Prescriptions, OPD and pharmacy')
            ->assertSee('Free-tier logic, ads and watermarks')
            ->assertSee('href="'.route('contact').'"', false);

        $document = new DOMDocument;
        libxml_use_internal_errors(true);
        $document->loadHTML('<?xml encoding="UTF-8">'.$response->getContent());
        libxml_clear_errors();
        $xpath = new DOMXPath($document);
        $this->assertSame(1, $xpath->query('//h1')->length);
        $this->assertSame(13, $xpath->query('//details[@class="dc-phase"]')->length);
        $this->assertSame(13, $xpath->query('//div[@class="dc-phase-content"]')->length);
        $this->assertSame(0, $xpath->query('//a[@href="#"]')->length);
    }

    public function test_solutions_page_and_shared_navigation_link_to_the_diagnostic_solution(): void
    {
        $this->get(route('solutions'))->assertOk()->assertSee('Diagnostic centers')
            ->assertSee('Planned development')
            ->assertSee('href="'.route('diagnostic-centers-solution').'"', false)
            ->assertSee('href="'.route('ecommerce-solution').'"', false)
            ->assertSee('href="'.route('cloud-solution').'"', false)
            ->assertSee('href="'.route('automation-solution').'"', false);
        $this->get('/')->assertOk()->assertSee('href="'.route('solutions').'"', false)
            ->assertSee('href="'.route('diagnostic-centers-solution').'"', false);
        $this->get(route('diagnostic-management-system'))->assertOk()->assertSee('View the solution roadmap');
    }

    public function test_both_pages_are_discoverable_in_the_sitemap(): void
    {
        $this->get('/sitemap.xml')->assertOk()
            ->assertSee('<loc>https://softorio.com/solutions</loc>', false)
            ->assertSee('<loc>https://softorio.com/diagnostic-centers-solution</loc>', false);
    }
}
