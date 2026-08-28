<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateFooterRequest;
use App\Http\Resources\FooterResource;
use App\Models\FooterConfig;
use App\Services\FooterService;

class FooterController extends Controller
{
    public function __construct(private readonly FooterService $footerService) {}

    public function show(): FooterResource
    {
        return FooterResource::make($this->payload());
    }

    public function update(UpdateFooterRequest $request): FooterResource
    {
        $footer = FooterConfig::firstOrCreate([], ['office_title' => 'Godziny otwarcia kancelarii']);
        $this->footerService->update($footer, $request->validated());

        return FooterResource::make($this->payload());
    }

    private function payload(): FooterConfig
    {
        $footer = FooterConfig::firstOrCreate([], ['office_title' => 'Godziny otwarcia kancelarii']);
        $footer->wasRecentlyCreated = false;
        $footer->load(['officeHours', 'legalLinks']);

        return $footer;
    }
}
