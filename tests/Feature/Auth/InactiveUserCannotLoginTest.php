<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

it('prevents inactive users from logging in', function () {
    $user = User::factory()->create([
        'email' => 'inactive@example.com',
        'password' => Hash::make('password'),
        'role' => 'member',
        'status' => 'inactive',
    ]);

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $response->assertSessionHasErrors();

    $this->assertGuest();
});
