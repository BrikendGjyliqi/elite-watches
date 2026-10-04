<?php

namespace Tests\Feature;

use App\Livewire\GlobalSearch;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Watch;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class GlobalSearchTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    public function test_drawer_is_mounted_on_every_storefront_page_and_wired_to_the_navbar(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSeeLivewire(GlobalSearch::class)
            ->assertSee("\$dispatch('open-search'", false)
            ->assertSee('role="dialog"', false)
            ->assertSee('aria-modal="true"', false);
    }

    public function test_empty_query_shows_recent_searches_and_suggestions(): void
    {
        Livewire::test(GlobalSearch::class)
            ->assertSee('Recent searches')
            ->assertSee('Your recent searches will appear here.')
            ->assertSee('Suggestions')
            ->assertSee('Rolex Daytona')
            ->assertSee('Dive watches under €10,000')
            ->assertSee('New arrivals');
    }

    public function test_one_character_asks_to_keep_typing(): void
    {
        Livewire::test(GlobalSearch::class)
            ->set('query', 'p')
            ->assertSee('Keep typing to search the maison...')
            ->assertDontSee('Not in the vault');
    }

    public function test_results_are_found_and_matches_highlighted(): void
    {
        $component = Livewire::test(GlobalSearch::class)->set('query', 'patek');

        $names = $component->instance()->results->pluck('name');
        $this->assertCount(2, $names);
        $this->assertTrue($names->every(fn ($name) => str_contains($name, 'Patek Philippe')));

        $component
            ->assertSee('<span class="text-accent-gold">Patek</span> Philippe Nautilus', false)
            ->assertSee('<span class="text-white">Patek</span> Philippe', false)
            ->assertSeeHtml(route('shop.show', Watch::where('name', 'like', '%Nautilus%')->value('slug')));
    }

    public function test_every_word_must_match_with_a_graceful_fallback(): void
    {
        $narrow = Livewire::test(GlobalSearch::class)->set('query', 'patek nautilus');
        $this->assertSame(['Patek Philippe Nautilus 5711/1A'], $narrow->instance()->results->pluck('name')->all());

        // No piece is both "pilot" and "chronograph" by every field → fall back to either word.
        $loose = Livewire::test(GlobalSearch::class)->set('query', 'pilot chronograph');
        $this->assertGreaterThan(0, $loose->instance()->results->count());
    }

    public function test_reference_numbers_brands_and_categories_are_searchable(): void
    {
        $byReference = Livewire::test(GlobalSearch::class)->set('query', '126610');
        $this->assertSame('Rolex Submariner Date 126610LN', $byReference->instance()->results->first()->name);

        $component = Livewire::test(GlobalSearch::class)->set('query', 'rolex');
        $this->assertSame(['Rolex'], $component->instance()->brandMatches->pluck('name')->all());
        $component->assertSee('By brand')
            ->assertSeeHtml(e(route('shop.index', ['brand' => [Brand::where('name', 'Rolex')->value('id')]])));

        $categories = Livewire::test(GlobalSearch::class)->set('query', 'diver');
        $this->assertContains('Diver', $categories->instance()->categoryMatches->pluck('name')->all());
        $categories->assertSee('By category')
            ->assertSeeHtml(e(route('shop.index', ['category' => [Category::where('name', 'Diver')->value('id')]])));
    }

    public function test_at_most_eight_inline_results_with_a_link_to_the_rest(): void
    {
        Watch::query()->update(['name' => \DB::raw("CONCAT('Vault ', name)")]);

        $component = Livewire::test(GlobalSearch::class)->set('query', 'vault');

        $this->assertCount(GlobalSearch::LIMIT, $component->instance()->results);
        $component->assertSee('View all '.Watch::count().' results')
            ->assertSeeHtml(e(route('search', ['q' => 'vault'])));
    }

    public function test_no_results_offers_to_source_the_piece(): void
    {
        Livewire::test(GlobalSearch::class)
            ->set('query', 'xyzabc')
            ->assertSee('Not in the vault')
            ->assertSee('No pieces match ‘xyzabc’.', false)
            ->assertSee('Request this piece')
            ->assertSeeHtml(e(route('contact.index', ['subject' => 'unlisted', 'prefill' => 'xyzabc'])));
    }

    public function test_search_input_cannot_inject_markup(): void
    {
        Livewire::test(GlobalSearch::class)
            ->set('query', '<img src=x onerror=alert(1)>')
            ->assertDontSeeHtml('<img src=x onerror=alert(1)>');
    }

    public function test_view_all_page_filters_the_shop_by_the_phrase(): void
    {
        $this->get(route('search', ['q' => 'patek']))
            ->assertOk()
            ->assertSee('‘patek’', false)
            ->assertSee('Patek Philippe Nautilus 5711/1A')
            ->assertDontSee('Rolex Daytona 116500LN')
            ->assertSee('name="q" value="patek"', false);
    }

    public function test_request_this_piece_prefills_the_contact_form(): void
    {
        $this->get(route('contact.index', ['subject' => 'A piece not listed', 'prefill' => 'Lange 1 Moonphase']))
            ->assertOk()
            ->assertSee('<option value="unlisted" selected>', false)
            ->assertSee('I am looking for: Lange 1 Moonphase.');
    }
}
