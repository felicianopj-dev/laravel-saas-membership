<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use App\Support\Members\MemberProfileData;

class ProfileController extends Controller
{
    public function show(Request $request): Response
    {
        return Inertia::render('Member/Profile/Show', [
            ...MemberProfileData::make($request->user()),
        ]);
    }
}