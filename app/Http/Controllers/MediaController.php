<?php

namespace App\Http\Controllers;

use App\Http\Resources\MediaResource;
use App\Models\Media;
use App\Services\MediaService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class MediaController extends Controller
{
    public function __construct(private readonly MediaService $mediaService) {}

    public function index(): AnonymousResourceCollection
    {
        return MediaResource::collection(
            Media::with('author')->orderByDesc('id')->get()
        );
    }

    public function destroy(Media $media): Response
    {
        $this->mediaService->delete($media);

        return response()->noContent();
    }
}
