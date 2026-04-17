<?php

namespace App\Http\Controllers\Admin;

use Inertia\Inertia;
use Inertia\Response;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Support\Web\Admin\Users\AdminUsersData;

class UserController extends Controller
{
    public function __invoke(Request $request): Response
    {
        return Inertia::render('Admin/Users/Index', [
            ...AdminUsersData::make(
                $request->string('search')->toString()
            ),
        ]);
    }
}