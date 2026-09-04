<?php

namespace App\Http\Controllers;

use App\Http\Requests\UploadContentImageRequest;
use App\Services\ContentImageService;
use Illuminate\Http\JsonResponse;

class ContentImageController extends Controller
{
    public function __construct(private readonly ContentImageService $contentImageService) {}

    public function upload(UploadContentImageRequest $request): JsonResponse
    {
        $url = $this->contentImageService->storeImage(
            $request->file('image'),
            $request->getSchemeAndHttpHost(),
            $request->user()?->id
        );

        return response()->json(['url' => $url]);
    }
}
