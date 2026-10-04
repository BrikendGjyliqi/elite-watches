<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Watch;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BrandPagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    public function test_index_presents_the_registry_with_real_counts(): void
    {
        $brandCount = Brand::count();
        $watchCount = Watch::count();

        $response = $this->get(route('brands.index'))
            ->assertOk()
            ->assertSee('<title>The Maisons · ÉLITE</title>', false)
            ->assertSee('The great houses, under one roof.')
            ->assertSee("{$brandCount} maisons · {$watchCount} pieces curated")
            ->assertSee('Maison of the month')
            ->assertSee('Each house. A different language of time.')
            ->assertSee('We do not represent every brand.');

        Brand::withCount('watches')->get()->each(function (Brand $brand) use ($response) {
            $response->assertSee(route('brands.show', $brand->slug), false);
            if ($brand->watches_count > 0) {
                $response->assertSee($brand->watches_count === 1 ? '1 piece in the vault' : "{$brand->watches_count} pieces in the vault");
            }
        });
    }

    public function test_maison_of_the_month_is_the_featured_house_with_most_pieces(): void
    {
        $expected = Brand::where('is_featured', true)->withCount('watches')
            ->orderByDesc('watches_count')->orderBy('name')->first();

        $this->get(route('brands.index'))
            ->assertSeeInOrder(['Maison of the month', $expected->name, 'Discover '.$expected->name]);
    }

    public function test_every_brand_detail_page_renders_its_chapter(): void
    {
        Brand::withCount('watches')->get()->each(function (Brand $brand) {
            $response = $this->get(route('brands.show', $brand->slug))
                ->assertOk()
                ->assertSee("<title>{$brand->name} · ÉLITE Maison Horlogère</title>", false)
                ->assertSee($brand->name.' · ')
                ->assertSee('Heritage')
                ->assertSee('Every piece currently in the vault.')
                ->assertSee('Our atelier can source.');

            if ($brand->watches_count > 0) {
                $response->assertSee('to know.')
                    ->assertSee(e(route('shop.index', ['brand' => $brand->slug])), false);
            }
        });
    }

    public function test_signature_pieces_are_the_top_three_and_related_brands_prefer_the_same_country(): void
    {
        $rolex = Brand::where('slug', 'rolex')->firstOrFail();

        $signature = $rolex->signaturePieces();
        $this->assertLessThanOrEqual(3, $signature->count());
        $this->assertSame(
            $rolex->watches()->orderByDesc('is_featured')->orderByDesc('is_bestseller')->orderByDesc('price')->limit(3)->pluck('id')->all(),
            $signature->pluck('id')->all(),
        );

        $related = $rolex->relatedBrands(5);
        $this->assertCount(5, $related);
        $this->assertNotContains($rolex->id, $related->pluck('id'));
        $this->assertTrue($related->every(fn (Brand $b) => $b->country === $rolex->country), 'Swiss houses come first for a Swiss maison.');

        $cartier = Brand::where('slug', 'cartier')->firstOrFail();
        $this->assertSame('Cartier', $cartier->name);
        $this->assertCount(5, $cartier->relatedBrands(5)); // no other French house: falls back by era
    }

    public function test_story_splits_description_and_reads_at_length(): void
    {
        $brand = Brand::where('slug', 'omega')->firstOrFail();
        $brand->update(['description' => "First paragraph.\n\nSecond paragraph."]);

        $story = $brand->fresh()->storyParagraphs();
        $this->assertSame(['First paragraph.', 'Second paragraph.'], array_slice($story, 0, 2));
        $this->assertGreaterThanOrEqual(4, count($story));
    }

    public function test_shop_accepts_brand_slugs(): void
    {
        $rolex = Brand::where('slug', 'rolex')->firstOrFail();
        $other = Watch::where('brand_id', '!=', $rolex->id)->firstOrFail();

        $this->get(route('shop.index', ['brand' => 'rolex']))
            ->assertOk()
            ->assertSee($rolex->watches()->first()->name)
            ->assertDontSee($other->name)
            ->assertSee('value="'.$rolex->id.'"', false);
    }

    public function test_collection_filters_by_category_on_the_brand_page(): void
    {
        $rolex = Brand::where('slug', 'rolex')->firstOrFail();
        $watch = $rolex->watches()->with('category')->firstOrFail();

        $this->get(route('brands.show', ['brand' => 'rolex', 'category' => $watch->category_id]))
            ->assertOk()
            ->assertSee($watch->name);
    }

    public function test_request_a_piece_names_the_maison_on_the_contact_form(): void
    {
        $this->get(route('contact.index', ['subject' => 'unlisted', 'brand' => 'Patek Philippe']))
            ->assertSee('I am searching for a Patek Philippe reference:');
    }

    public function test_unknown_brand_is_a_404(): void
    {
        $this->get('/brands/not-a-maison')->assertNotFound();
    }
}
