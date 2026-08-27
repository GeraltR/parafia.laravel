<?php

namespace App\Http\Controllers;

use App\Enums\ContentPageSlug;
use App\Http\Requests\StoreContentTopicRequest;
use App\Http\Requests\UpdateContentTopicRequest;
use App\Http\Requests\UploadContentImageRequest;
use App\Http\Resources\ContentTopicResource;
use App\Models\ContentTopic;
use App\Services\ContentTopicService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

class ContentTopicController extends Controller
{
    public function __construct(private readonly ContentTopicService $contentTopicService) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $page = $this->validatedPage($request);

        $topics = ContentTopic::with('author')
            ->where('page', $page)
            ->visible()
            ->orderBy('order')
            ->get();

        return ContentTopicResource::collection($topics);
    }

    public function manage(Request $request): AnonymousResourceCollection
    {
        $page = $this->validatedPage($request);

        $topics = ContentTopic::with('author')
            ->where('page', $page)
            ->orderBy('order')
            ->get();

        return ContentTopicResource::collection($topics);
    }

    public function store(StoreContentTopicRequest $request): JsonResponse
    {
        $topic = $this->contentTopicService->create($request->validated());

        return ContentTopicResource::make($topic->load('author'))->response()->setStatusCode(201);
    }

    public function update(UpdateContentTopicRequest $request, ContentTopic $contentTopic): ContentTopicResource
    {
        $topic = $this->contentTopicService->update($contentTopic, $request->validated());

        return ContentTopicResource::make($topic->load('author'));
    }

    public function destroy(ContentTopic $contentTopic): Response
    {
        $contentTopic->delete();

        return response()->noContent();
    }

    public function uploadImage(UploadContentImageRequest $request): JsonResponse
    {
        $url = $this->contentTopicService->storeImage(
            $request->file('image'),
            $request->getSchemeAndHttpHost()
        );

        return response()->json(['url' => $url]);
    }

    private function validatedPage(Request $request): string
    {
        $validated = $request->validate([
            'page' => ['required', 'string', Rule::in(array_column(ContentPageSlug::cases(), 'value'))],
        ]);

        return $validated['page'];
    }
}
