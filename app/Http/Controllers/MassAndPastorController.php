<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateMassAndPastorRequest;
use App\Http\Requests\UploadPastorPhotoRequest;
use App\Http\Resources\MassAndPastorSectionResource;
use App\Http\Resources\MassTimeResource;
use App\Http\Resources\PastorResource;
use App\Models\MassAndPastorSection;
use App\Services\MassAndPastorService;
use Illuminate\Http\JsonResponse;

class MassAndPastorController extends Controller
{
    public function __construct(private readonly MassAndPastorService $massAndPastorService) {}

    public function show(): JsonResponse
    {
        return response()->json(['data' => $this->payload()]);
    }

    public function update(UpdateMassAndPastorRequest $request): JsonResponse
    {
        $section = MassAndPastorSection::firstOrCreate();
        $this->massAndPastorService->update($section, $request->validated());

        return response()->json(['data' => $this->payload()]);
    }

    public function uploadPhoto(UploadPastorPhotoRequest $request): JsonResponse
    {
        $url = $this->massAndPastorService->storePhoto(
            $request->file('photo'),
            $request->getSchemeAndHttpHost()
        );

        return response()->json(['url' => $url]);
    }

    private function payload(): array
    {
        $section = MassAndPastorSection::firstOrCreate();
        $section->load(['massTimes', 'pastors']);

        return [
            'config' => MassAndPastorSectionResource::make($section),
            'massTimes' => MassTimeResource::collection($section->massTimes),
            'pastors' => PastorResource::collection($section->pastors),
        ];
    }
}
