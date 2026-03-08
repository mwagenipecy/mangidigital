<?php

namespace App\Livewire\Landing;

use Livewire\Component;

class Navbar extends Component
{
    public bool $mobileMenuOpen = false;

    public function toggleMenu(): void
    {
        $this->mobileMenuOpen = !$this->mobileMenuOpen;
    }

    public function render()
    {
        return view('livewire.landing.navbar');
    }
}
