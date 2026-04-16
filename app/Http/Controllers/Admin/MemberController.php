<?php

namespace App\Http\Controllers\Admin;

use Inertia\Inertia;
use Inertia\Response;
use App\Http\Controllers\Controller;

class MemberController extends Controller
{
    public function __invoke(): Response
    {
        return Inertia::render('Admin/Members/Index', [
            'title' => 'Members',
            'description' => 'View, search, and manage the people subscribed to your SaaS platform.',
        ]);
    }
}