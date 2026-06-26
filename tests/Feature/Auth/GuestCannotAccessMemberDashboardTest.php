<?php

it('prevents guests from accessing the member dashboard', function () {
    $response = $this->get(route('member.dashboard'));

    $response->assertRedirect(route('login'));
});
