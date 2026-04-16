<?php

namespace App\Http\Controllers\Web\Member;

use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Support\Web\Member\MemberProfileData;

class ProfileController extends Controller
{
    public function show(Request $request): Response
    {
        return Inertia::render('Member/Profile/Show', [
            ...MemberProfileData::make($request->user()),
        ]);
    }
}