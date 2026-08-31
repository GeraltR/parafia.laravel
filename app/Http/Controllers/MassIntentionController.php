<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMassIntentionRequest;
use App\Http\Requests\UpdateMassIntentionRequest;
use App\Http\Requests\UpdateMassIntentionsConfigRequest;
use App\Http\Resources\MassIntentionResource;
use App\Http\Resources\MassIntentionsConfigResource;
use App\Models\MassIntention;
use App\Models\MassIntentionsConfig;
use App\Services\MassIntentionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MassIntentionController extends Controller
{
    public function __construct(private readonly MassIntentionService $massIntentionService) {}

    public function index(): JsonResponse
    {
        $today = now()->toDateString();
        $until = now()->addDays(14)->toDateString();

        $items = MassIntention::whereBetween('date', [$today, $until])
            ->orderBy('date')
            ->orderBy('time')
            ->get();

        return response()->json([
            'data' => [
                'config' => MassIntentionsConfigResource::make($this->config()),
                'items' => MassIntentionResource::collection($items),
            ],
        ]);
    }

    public function manage(Request $request): JsonResponse
    {
        $paginator = $this->massIntentionService
            ->manageQuery($request->string('search')->toString())
            ->paginate(50, page: $request->integer('page', 1));

        return response()->json([
            'data' => [
                'config' => MassIntentionsConfigResource::make($this->config()),
                'items' => MassIntentionResource::collection($paginator->items()),
                'meta' => [
                    'currentPage' => $paginator->currentPage(),
                    'lastPage' => $paginator->lastPage(),
                    'total' => $paginator->total(),
                ],
            ],
        ]);
    }

    public function printList(Request $request): JsonResponse
    {
        $from = $request->string('from')->toString();

        $items = MassIntention::query()
            ->when($from !== '', fn ($query) => $query->where('date', '>=', $from))
            ->orderBy('date')
            ->orderBy('time')
            ->get();

        return response()->json([
            'data' => MassIntentionResource::collection($items),
        ]);
    }

    public function store(StoreMassIntentionRequest $request): JsonResponse
    {
        $intention = $this->massIntentionService->create($request->validated());

        return MassIntentionResource::make($intention->load('author'))->response()->setStatusCode(201);
    }

    public function update(UpdateMassIntentionRequest $request, MassIntention $massIntention): MassIntentionResource
    {
        $intention = $this->massIntentionService->update($massIntention, $request->validated());

        return MassIntentionResource::make($intention->load('author'));
    }

    public function destroy(MassIntention $massIntention): Response
    {
        $massIntention->delete();

        return response()->noContent();
    }

    public function updateConfig(UpdateMassIntentionsConfigRequest $request): MassIntentionsConfigResource
    {
        $config = $this->massIntentionService->updateConfig($request->validated());

        return MassIntentionsConfigResource::make($config);
    }

    private function config(): MassIntentionsConfig
    {
        $config = MassIntentionsConfig::firstOrCreate([], [
            'holiday_described_color' => '#7bdcb5',
            'holiday_plain_color' => '#f78da7',
            'weekday_color' => '#8ed1fc',
        ]);
        $config->wasRecentlyCreated = false;

        return $config;
    }
}
