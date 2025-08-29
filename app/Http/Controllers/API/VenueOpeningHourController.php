<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\VenueOpeningHourRequest;
use App\Http\Resources\VenueOpeningHourResource;
use App\Services\VenueOpeningHourService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VenueOpeningHourController extends Controller
{
    /**
     * @var VenueOpeningHourService
     */
    protected VenueOpeningHourService $venueOpeningHourService;

    /**
     * DummyModel Constructor
     *
     * @param VenueOpeningHourService $venueOpeningHourService
     *
     */
    public function __construct(VenueOpeningHourService $venueOpeningHourService)
    {
        $this->venueOpeningHourService = $venueOpeningHourService;
    }

    public function index(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        return VenueOpeningHourResource::collection($this->venueOpeningHourService->getAll());
    }

    public function store(VenueOpeningHourRequest $request): VenueOpeningHourResource|\Illuminate\Http\JsonResponse
    {
        try {
            return new VenueOpeningHourResource($this->venueOpeningHourService->save($request->validated()));
        } catch (\Exception $exception) {
            report($exception);
            return response()->json(['error' => 'There is an error.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(int $id): VenueOpeningHourResource
    {
        return VenueOpeningHourResource::make($this->venueOpeningHourService->getById($id));
    }

    public function update(VenueOpeningHourRequest $request, int $id): VenueOpeningHourResource|\Illuminate\Http\JsonResponse
    {
        try {
            return new VenueOpeningHourResource($this->venueOpeningHourService->update($request->validated(), $id));
        } catch (\Exception $exception) {
            report($exception);
            return response()->json(['error' => 'There is an error.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy(int $id): \Illuminate\Http\JsonResponse
    {
        try {
            $this->venueOpeningHourService->deleteById($id);
            return response()->json(['message' => 'Deleted successfully'], Response::HTTP_OK);
        } catch (\Exception $exception) {
            report($exception);
            return response()->json(['error' => 'There is an error.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
