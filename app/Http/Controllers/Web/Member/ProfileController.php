<?php

namespace App\Http\Controllers\Web\Member;

use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use App\Support\Web\Member\MemberProfileData;
use App\Http\Requests\Member\UpdateProfileRequest;

class ProfileController extends Controller
{
    public function show(Request $request): Response
    {
        return Inertia::render('Member/Profile/Show', [
            ...MemberProfileData::make(auth()->user()),
        ]);
        
    }
        
    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        $user = $request->user();
        
        $user->update($request->validated());
        
        return redirect()
            ->route('member.profile.show')
            ->with('success', 'Profile updated successfully.');
    }
}