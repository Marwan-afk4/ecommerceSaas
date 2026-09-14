<?php

it('renders the create-your-store homepage', function () {
    $this->get(route('home'))
        ->assertOk()
        ->assertSee('Make your own ecommerce for your business', false)
        ->assertSee('Shop admin login', false)
        ->assertSee('Superadmin', false)
        ->assertSee('Switch to dark mode', false)
        ->assertDontSee('Documentation', false);
});
