<?php

use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Inertia\Testing\AssertableInertia as Assert;

function unverifiedMember(): User
{
    return User::factory()->unverified()->create([
        'role' => 'member',
        'status' => 'active',
    ]);
}

it('redirects an unverified member away from member routes to the verification notice', function () {
    $this->actingAs(unverifiedMember())
        ->get(route('member.dashboard'))
        ->assertRedirect(route('verification.notice'));
});

it('shows the verification notice to an unverified member', function () {
    $this->actingAs(unverifiedMember())
        ->get(route('verification.notice'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('Auth/VerifyEmail'));
});

it('sends the member to the dashboard when they are already verified', function () {
    $user = User::factory()->create(['role' => 'member', 'status' => 'active']);

    $this->actingAs($user)
        ->get(route('verification.notice'))
        ->assertRedirect(route('member.dashboard'));
});

it('verifies the email from a valid signed link', function () {
    $user = unverifiedMember();

    $url = URL::temporarySignedRoute('verification.verify', now()->addMinutes(60), [
        'id' => $user->id,
        'hash' => sha1($user->email),
    ]);

    $this->actingAs($user)
        ->get($url)
        ->assertRedirect(route('member.dashboard').'?verified=1');

    expect($user->fresh()->hasVerifiedEmail())->toBeTrue();
});

it('rejects a verification link with a tampered hash', function () {
    $user = unverifiedMember();

    $url = URL::temporarySignedRoute('verification.verify', now()->addMinutes(60), [
        'id' => $user->id,
        'hash' => sha1('wrong@example.com'),
    ]);

    $this->actingAs($user)->get($url)->assertForbidden();

    expect($user->fresh()->hasVerifiedEmail())->toBeFalse();
});

it('resends the verification email on request', function () {
    Notification::fake();

    $user = unverifiedMember();

    $this->actingAs($user)
        ->post(route('verification.send'))
        ->assertRedirect()
        ->assertSessionHas('status', 'verification-link-sent');

    Notification::assertSentTo($user, VerifyEmail::class);
});
