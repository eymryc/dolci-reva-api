<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\TimeSlotRequest;
use App\Http\Resources\TimeSlotResource;
use App\Services\TimeSlotService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TimeSlotController extends Controller
{
    /**
     * @var TimeSlotService
     */
    protected TimeSlotService $timeSlotService;

    /**
     * DummyModel Constructor
     *
     * @param TimeSlotService $timeSlotService
     *
     */
    public function __construct(TimeSlotService $timeSlotService)
    {
        $this->timeSlotService = $timeSlotService;
    }

    public function index(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        return TimeSlotResource::collection($this->timeSlotService->getAll());
    }

    public function store(TimeSlotRequest $request): TimeSlotResource|\Illuminate\Http\JsonResponse
    {
        try {
            return new TimeSlotResource($this->timeSlotService->save($request->validated()));
        } catch (\Exception $exception) {
            report($exception);
            return response()->json(['error' => 'There is an error.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(int $id): TimeSlotResource
    {
        return TimeSlotResource::make($this->timeSlotService->getById($id));
    }

    public function update(TimeSlotRequest $request, int $id): TimeSlotResource|\Illuminate\Http\JsonResponse
    {
        try {
            return new TimeSlotResource($this->timeSlotService->update($request->validated(), $id));
        } catch (\Exception $exception) {
            report($exception);
            return response()->json(['error' => 'There is an error.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy(int $id): \Illuminate\Http\JsonResponse
    {
        try {
            $this->timeSlotService->deleteById($id);
            return response()->json(['message' => 'Deleted successfully'], Response::HTTP_OK);
        } catch (\Exception $exception) {
            report($exception);
            return response()->json(['error' => 'There is an error.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
