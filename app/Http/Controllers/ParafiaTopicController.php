<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreParafiaTopicRequest;
use App\Http\Requests\UpdateParafiaTopicRequest;
use App\Http\Requests\UploadContentImageRequest;
use App\Http\Resources\ParafiaTopicResource;
use App\Models\ParafiaTopic;
use App\Services\ParafiaTopicService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class ParafiaTopicController extends Controller
{
    public function __construct(private readonly ParafiaTopicService $parafiaTopicService) {}

    public function index(): AnonymousResourceCollection
    {
        $topics = ParafiaTopic::with('author')->visible()->orderBy('order')->get();

        return ParafiaTopicResource::collection($topics);
    }

    public function manage(): AnonymousResourceCollection
    {
        $topics = ParafiaTopic::with('author')->orderBy('order')->get();

        return ParafiaTopicResource::collection($topics);
    }

    public function store(StoreParafiaTopicRequest $request): JsonResponse
    {
        $topic = $this->parafiaTopicService->create($request->validated());

        return ParafiaTopicResource::make($topic->load('author'))->response()->setStatusCode(201);
    }

    public function update(UpdateParafiaTopicRequest $request, ParafiaTopic $parafiaTopic): ParafiaTopicResource
    {
        $topic = $this->parafiaTopicService->update($parafiaTopic, $request->validated());

        return ParafiaTopicResource::make($topic->load('author'));
    }

    public function destroy(ParafiaTopic $parafiaTopic): Response
    {
        $parafiaTopic->delete();

        return response()->noContent();
    }

    public function uploadImage(UploadContentImageRequest $request): JsonResponse
    {
        $url = $this->parafiaTopicService->storeImage(
            $request->file('image'),
            $request->getSchemeAndHttpHost()
        );

        return response()->json(['url' => $url]);
    }
}
