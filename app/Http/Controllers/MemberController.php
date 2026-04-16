<?php

namespace App\Http\Controllers;

use App\Support\Members\MemberData;
use App\Support\Members\MemberListing;
use Inertia\Inertia;
use Inertia\Response;

class MemberController extends Controller
{
    public function __invoke(MemberListing $memberListing): Response
    {
        return Inertia::render('Members/Index', [
            'members' => $memberListing->paginate(10)
                ->through(fn ($member) => MemberData::fromModel($member)),
        ]);
    }
}