<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ActivityRequest;
use App\Http\Resources\ActivityResource;
use App\Services\ActivityService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ActivityController extends Controller
{
    /**
     * @var ActivityService
     */
    protected ActivityService $activityService;

    /**
     * DummyModel Constructor
     *
     * @param ActivityService $activityService
     *
     */
    public function __construct(ActivityService $activityService)
    {
        $this->activityService = $activityService;
    }

    public function index(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        return ActivityResource::collection($this->activityService->getAll());
    }

    public function store(ActivityRequest $request): ActivityResource|\Illuminate\Http\JsonResponse
    {
        try {
            return new ActivityResource($this->activityService->save($request->validated()));
        } catch (\Exception $exception) {
            report($exception);
            return response()->json(['error' => 'There is an error.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(int $id): ActivityResource
    {
        return ActivityResource::make($this->activityService->getById($id));
    }

    public function update(ActivityRequest $request, int $id): ActivityResource|\Illuminate\Http\JsonResponse
    {
        try {
            return new ActivityResource($this->activityService->update($request->validated(), $id));
        } catch (\Exception $exception) {
            report($exception);
            return response()->json(['error' => 'There is an error.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy(int $id): \Illuminate\Http\JsonResponse
    {
        try {
            $this->activityService->deleteById($id);
            return response()->json(['message' => 'Deleted successfully'], Response::HTTP_OK);
        } catch (\Exception $exception) {
            report($exception);
            return response()->json(['error' => 'There is an error.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
