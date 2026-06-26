<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(
    TestCase::class,
    RefreshDatabase::class,
)->beforeEach(function () {
    // Inertia renders the root Blade template (which pulls in @vite) on the
    // first page load, so feature tests don't need a compiled asset bundle.
    $this->withoutVite();

    // Assert on component name + props, not on-disk page resolution (which
    // depends on the JS build config and is irrelevant to backend behavior).
    config(['inertia.testing.ensure_pages_exist' => false]);
})->in('Feature');
