<?php

namespace Tests\Feature;

use App\Models\Watch;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FrontendContentTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_renders_expected_content(): void
    {
        $this->seed();

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('Time, Refined.');
        $response->assertSee('Curated for Connoisseurs');
        $response->assertSee('New Arrivals');
        $response->assertSee('By Category');
        $response->assertSee('Enter the ÉLITE circle');

        // At least one featured watch and one brand name should be rendered.
        $featured = Watch::where('is_featured', true)->firstOrFail();
        $response->assertSee($featured->name);
        $response->assertSee($featured->brand->name);
    }

    public function test_shop_page_renders_filters_and_results(): void
    {
        $this->seed();

        $response = $this->get('/shop');

        $response->assertOk();
        $response->assertSee('Shop All Watches');
        $response->assertSee('Brand');
        $response->assertSee('Category');
        $response->assertSee('Price Range');
        $response->assertSee('In stock only');

        $watch = Watch::firstOrFail();
        $response->assertSee($watch->name);
    }

    public function test_shop_page_filters_by_brand(): void
    {
        $this->seed();

        $watch = Watch::firstOrFail();
        $otherBrandWatch = Watch::where('brand_id', '!=', $watch->brand_id)->firstOrFail();

        $response = $this->get('/shop?'.http_build_query(['brand' => [$watch->brand_id]]));

        $response->assertOk();
        $response->assertSee($watch->name);
        $response->assertDontSee($otherBrandWatch->name);
    }

    public function test_product_page_renders_details_specs_and_reviews(): void
    {
        $this->seed();

        $watch = Watch::whereHas('reviews')->with('spec')->firstOrFail();

        $response = $this->get("/shop/{$watch->slug}");

        $response->assertOk();
        $response->assertSee($watch->name);
        $response->assertSee($watch->brand->name);
        $response->assertSee('Add to Cart');
        $response->assertSee('Add to Wishlist');
        $response->assertSee('Description');
        $response->assertSee('Specifications');
        $response->assertSee('Reviews');
        $response->assertSee($watch->spec->movement);
        $response->assertSee('You May Also Like');
    }

    public function test_product_page_shows_login_prompt_for_guests_and_form_for_users(): void
    {
        $this->seed();

        $watch = Watch::firstOrFail();

        $this->get("/shop/{$watch->slug}")->assertSee('to write a review');

        $user = \App\Models\User::where('role', 'customer')->firstOrFail();

        $this->actingAs($user)
            ->get("/shop/{$watch->slug}")
            ->assertSee('Write a review')
            ->assertSee('Submit Review');
    }

    public function test_about_page_tells_the_maison_story_with_its_own_meta(): void
    {
        $this->get(route('about'))
            ->assertOk()
            ->assertSee('<title>About · ÉLITE Maison Horlogère</title>', false)
            ->assertSee('<meta property="og:description" content="Time, held in trust.">', false)
            ->assertSee('rel="preload" as="image"', false)
            ->assertSeeInOrder([
                'Time, held in trust.',
                'A letter from the founder',
                'A short history of our conviction.',
                'Every piece. Verified by hand.',
                'Trust, measured.',
                'Not a store. A quiet room.',
                'Begin a conversation with the atelier.',
            ])
            ->assertSee(route('contact.index'))
            ->assertSee(route('shop.index'));
    }
}
