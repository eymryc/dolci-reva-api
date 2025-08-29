<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\VenueRequest;
use App\Http\Resources\VenueResource;
use App\Services\VenueService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VenueController extends Controller
{
    /**
     * @var VenueService
     */
    protected VenueService $venueService;

    /**
     * DummyModel Constructor
     *
     * @param VenueService $venueService
     *
     */
    public function __construct(VenueService $venueService)
    {
        $this->venueService = $venueService;
    }

    public function index(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        return VenueResource::collection($this->venueService->getAll());
    }

    public function store(VenueRequest $request): VenueResource|\Illuminate\Http\JsonResponse
    {
        try {
            return new VenueResource($this->venueService->save($request->validated()));
        } catch (\Exception $exception) {
            report($exception);
            return response()->json(['error' => 'There is an error.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(int $id): VenueResource
    {
        return VenueResource::make($this->venueService->getById($id));
    }

    public function update(VenueRequest $request, int $id): VenueResource|\Illuminate\Http\JsonResponse
    {
        try {
            return new VenueResource($this->venueService->update($request->validated(), $id));
        } catch (\Exception $exception) {
            report($exception);
            return response()->json(['error' => 'There is an error.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy(int $id): \Illuminate\Http\JsonResponse
    {
        try {
            $this->venueService->deleteById($id);
            return response()->json(['message' => 'Deleted successfully'], Response::HTTP_OK);
        } catch (\Exception $exception) {
            report($exception);
            return response()->json(['error' => 'There is an error.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
