<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\SpaceRequest;
use App\Http\Resources\SpaceResource;
use App\Services\SpaceService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SpaceController extends Controller
{
    /**
     * @var SpaceService
     */
    protected SpaceService $spaceService;

    /**
     * DummyModel Constructor
     *
     * @param SpaceService $spaceService
     *
     */
    public function __construct(SpaceService $spaceService)
    {
        $this->spaceService = $spaceService;
    }

    public function index(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        return SpaceResource::collection($this->spaceService->getAll());
    }

    public function store(SpaceRequest $request): SpaceResource|\Illuminate\Http\JsonResponse
    {
        try {
            return new SpaceResource($this->spaceService->save($request->validated()));
        } catch (\Exception $exception) {
            report($exception);
            return response()->json(['error' => 'There is an error.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(int $id): SpaceResource
    {
        return SpaceResource::make($this->spaceService->getById($id));
    }

    public function update(SpaceRequest $request, int $id): SpaceResource|\Illuminate\Http\JsonResponse
    {
        try {
            return new SpaceResource($this->spaceService->update($request->validated(), $id));
        } catch (\Exception $exception) {
            report($exception);
            return response()->json(['error' => 'There is an error.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy(int $id): \Illuminate\Http\JsonResponse
    {
        try {
            $this->spaceService->deleteById($id);
            return response()->json(['message' => 'Deleted successfully'], Response::HTTP_OK);
        } catch (\Exception $exception) {
            report($exception);
            return response()->json(['error' => 'There is an error.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
