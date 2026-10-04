<?php

namespace Tests\Feature;

use App\Filament\Resources\BrandResource\Pages\EditBrand;
use App\Filament\Resources\BrandResource\Pages\ListBrands;
use App\Models\Brand;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class BrandLogoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
        $this->artisan('brands:assign-logos')->assertSuccessful();
        $this->actingAs(User::where('role', 'admin')->firstOrFail(), 'admin');
    }

    public function test_command_assigns_every_seeded_brand_a_logo_in_public_images(): void
    {
        Brand::all()->each(function (Brand $brand) {
            $this->assertStringStartsWith('images/brands/brand-', $brand->logo_path);
            $this->assertFileExists(public_path($brand->logo_path));
            $this->assertSame(asset($brand->logo_path), $brand->logoUrl());
        });
    }

    public function test_slug_variations_are_matched(): void
    {
        Brand::where('slug', 'iwc-schaffhausen')->update(['slug' => 'iwc', 'logo_path' => null]);
        Brand::where('slug', 'tag-heuer')->update(['slug' => 'tag_heuer', 'logo_path' => null]);

        $this->artisan('brands:assign-logos')->expectsOutputToContain('Assigned 10 of 10 brand logos');

        $this->assertSame('images/brands/brand-iwc.svg', Brand::where('slug', 'iwc')->value('logo_path'));
        $this->assertSame('images/brands/brand-tag-heuer.svg', Brand::where('slug', 'tag_heuer')->value('logo_path'));
    }

    public function test_admin_list_shows_the_public_logo_urls(): void
    {
        $this->get('/admin/brands')
            ->assertOk()
            ->assertSee(asset('images/brands/brand-rolex.svg'), false);
    }

    public function test_saving_a_brand_in_admin_keeps_its_bundled_logo(): void
    {
        $rolex = Brand::where('slug', 'rolex')->firstOrFail();

        Livewire::test(EditBrand::class, ['record' => $rolex->getKey()])
            ->assertSet('data.logo_path', fn ($state) => in_array('images/brands/brand-rolex.svg', (array) $state, true))
            ->set('data.description', 'Updated in the admin.')
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame('images/brands/brand-rolex.svg', $rolex->fresh()->logo_path);
        $this->assertSame('Updated in the admin.', $rolex->fresh()->description);
    }
}
