<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePlanRequest;
use App\Http\Requests\Admin\UpdatePlanRequest;
use App\Models\Plan;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class PlanController extends Controller
{
    public function index(): Response
    {
        $plans = Plan::query()
            ->orderBy('sort_order')
            ->orderBy('price')
            ->get()
            ->map(fn (Plan $plan) => [
                'id' => $plan->id,
                'name' => $plan->name,
                'slug' => $plan->slug,
                'description' => $plan->description,
                'price' => $plan->price,
                'formatted_price' => $plan->formatted_price,
                'currency' => $plan->currency,
                'billing_interval' => $plan->billing_interval,
                'stripe_product_id' => $plan->stripe_product_id,
                'stripe_price_id' => $plan->stripe_price_id,
                'is_active' => $plan->is_active,
                'sort_order' => $plan->sort_order,
            ]);
        
        return Inertia::render('Admin/Plans/Index', [
            'plans' => $plans,
        ]);
    }
    
    public function create(): Response
    {
        return Inertia::render('Admin/Plans/Create', [
            'defaults' => [
                'currency' => config('billing.currency', 'usd'),
            ],
        ]);
    }
    
    public function store(StorePlanRequest $request): RedirectResponse
    {
        Plan::create($request->validated());
        
        return redirect()
            ->route('admin.plans.index')
            ->with('success', 'Plan created successfully.');
    }
    
    public function edit(Plan $plan): Response
    {
        return Inertia::render('Admin/Plans/Edit', [
            'plan' => [
                'id' => $plan->id,
                'name' => $plan->name,
                'slug' => $plan->slug,
                'description' => $plan->description,
                'price' => $plan->price,
                'currency' => $plan->currency,
                'billing_interval' => $plan->billing_interval,
                'stripe_product_id' => $plan->stripe_product_id,
                'stripe_price_id' => $plan->stripe_price_id,
                'is_active' => $plan->is_active,
                'sort_order' => $plan->sort_order,
            ],
        ]);
    }
    
    public function update(UpdatePlanRequest $request, Plan $plan): RedirectResponse
    {
        $plan->update($request->validated());
        
        return redirect()
            ->route('admin.plans.index')
            ->with('success', 'Plan updated successfully.');
    }
    
    public function destroy(Plan $plan): RedirectResponse
    {
        if ($plan->subscriptions()->exists()) {
            return back()->with('error', 'This plan cannot be deleted because it has subscriptions.');
        }
        
        $plan->delete();
        
        return redirect()
            ->route('admin.plans.index')
            ->with('success', 'Plan deleted successfully.');
    }
}