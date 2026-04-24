<?php

namespace App\Http\Controllers\Web\Member;

use App\Http\Controllers\Controller;
use App\Support\Web\Member\MemberPlansData;
use Inertia\Inertia;
use Inertia\Response;

class PlanController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Member/Plans/Index', [
            ...MemberPlansData::make(),
        ]);
    }
}