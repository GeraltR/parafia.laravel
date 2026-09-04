<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLiturgiaTopicRequest;
use App\Http\Requests\UpdateLiturgiaTopicRequest;
use App\Http\Requests\UploadContentImageRequest;
use App\Http\Resources\LiturgiaTopicResource;
use App\Models\LiturgiaTopic;
use App\Services\LiturgiaTopicService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class LiturgiaTopicController extends Controller
{
    public function __construct(private readonly LiturgiaTopicService $liturgiaTopicService) {}

    public function index(): AnonymousResourceCollection
    {
        $topics = LiturgiaTopic::with('author')->visible()->orderBy('order')->get();

        return LiturgiaTopicResource::collection($topics);
    }

    public function manage(): AnonymousResourceCollection
    {
        $topics = LiturgiaTopic::with('author')->orderBy('order')->get();

        return LiturgiaTopicResource::collection($topics);
    }

    public function store(StoreLiturgiaTopicRequest $request): JsonResponse
    {
        $topic = $this->liturgiaTopicService->create($request->validated());

        return LiturgiaTopicResource::make($topic->load('author'))->response()->setStatusCode(201);
    }

    public function update(UpdateLiturgiaTopicRequest $request, LiturgiaTopic $liturgiaTopic): LiturgiaTopicResource
    {
        $topic = $this->liturgiaTopicService->update($liturgiaTopic, $request->validated());

        return LiturgiaTopicResource::make($topic->load('author'));
    }

    public function destroy(LiturgiaTopic $liturgiaTopic): Response
    {
        $liturgiaTopic->delete();

        return response()->noContent();
    }

    public function uploadImage(UploadContentImageRequest $request): JsonResponse
    {
        $url = $this->liturgiaTopicService->storeImage(
            $request->file('image'),
            $request->getSchemeAndHttpHost(),
            $request->user()?->id
        );

        return response()->json(['url' => $url]);
    }
}
