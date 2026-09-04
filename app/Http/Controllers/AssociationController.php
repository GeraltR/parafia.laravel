<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateAssociationsRequest;
use App\Http\Requests\UploadAssociationImageRequest;
use App\Http\Resources\AssociationResource;
use App\Http\Resources\AssociationsConfigResource;
use App\Models\AssociationsConfig;
use App\Services\AssociationService;
use Illuminate\Http\JsonResponse;

class AssociationController extends Controller
{
    public function __construct(private readonly AssociationService $associationService) {}

    public function show(): JsonResponse
    {
        return response()->json(['data' => $this->payload()]);
    }

    public function update(UpdateAssociationsRequest $request): JsonResponse
    {
        $config = AssociationsConfig::firstOrCreate();
        $this->associationService->update($config, $request->validated());

        return response()->json(['data' => $this->payload()]);
    }

    public function uploadImage(UploadAssociationImageRequest $request): JsonResponse
    {
        $url = $this->associationService->storeImage(
            $request->file('image'),
            $request->getSchemeAndHttpHost(),
            $request->user()?->id
        );

        return response()->json(['url' => $url]);
    }

    private function payload(): array
    {
        $config = AssociationsConfig::firstOrCreate();
        $config->load('associations');

        return [
            'config' => AssociationsConfigResource::make($config),
            'items' => AssociationResource::collection($config->associations),
        ];
    }
}
