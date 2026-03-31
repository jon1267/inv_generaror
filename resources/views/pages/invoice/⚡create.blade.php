<?php

use Livewire\Component;
use Livewire\Attributes\Layout;

new #[Layout('layouts.public')] class extends Component
{
    public function with(): array
    {
        return [
            'title' => 'Create Invoice',
        ];
    }
};
?>

<div>
    <h1 class="text-2xl font-bold">Create Invoice</h1>
    <p class="text-gray-600 mt-2">Fill in your invoice details below</p>
</div>