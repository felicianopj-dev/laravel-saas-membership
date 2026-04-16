<?php

namespace App\Http\Controllers\Admin;

use Inertia\Inertia;
use Inertia\Response;
use App\Http\Controllers\Controller;
use App\Support\Members\AdminMembersData;

class MemberController extends Controller
{
    public function __invoke(): Response
    {
        return Inertia::render('Admin/Members/Index', [
            ...AdminMembersData::make(),
        ]);
    }
}