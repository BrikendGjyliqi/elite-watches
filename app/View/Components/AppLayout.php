<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class AppLayout extends Component
{
    public function __construct(
        public bool $transparentNav = false,
        public ?string $seoTitle = null,
        public ?string $seoDescription = null,
        public ?string $seoImage = null,
        public ?string $ogDescription = null,
    ) {}

    /**
     * Get the view / contents that represents the component.
     */
    public function render(): View
    {
        return view('layouts.app', [
            'transparentNav' => $this->transparentNav,
            'seoTitle' => $this->seoTitle,
            'seoDescription' => $this->seoDescription,
            'seoImage' => $this->seoImage,
            'ogDescription' => $this->ogDescription,
        ]);
    }
}
