<?php

namespace App\Http\Controllers\Web;

use Inertia\Inertia;
use Inertia\Response;
use App\Support\Members\MemberData;
use App\Http\Controllers\Controller;
use App\Support\Members\MemberListing;

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