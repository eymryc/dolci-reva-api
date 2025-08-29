<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\RoomRequest;
use App\Http\Resources\RoomResource;
use App\Services\RoomService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoomController extends Controller
{
    /**
     * @var RoomService
     */
    protected RoomService $roomService;

    /**
     * DummyModel Constructor
     *
     * @param RoomService $roomService
     *
     */
    public function __construct(RoomService $roomService)
    {
        $this->roomService = $roomService;
    }

    public function index(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        return RoomResource::collection($this->roomService->getAll());
    }

    public function store(RoomRequest $request): RoomResource|\Illuminate\Http\JsonResponse
    {
        try {
            return new RoomResource($this->roomService->save($request->validated()));
        } catch (\Exception $exception) {
            report($exception);
            return response()->json(['error' => 'There is an error.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(int $id): RoomResource
    {
        return RoomResource::make($this->roomService->getById($id));
    }

    public function update(RoomRequest $request, int $id): RoomResource|\Illuminate\Http\JsonResponse
    {
        try {
            return new RoomResource($this->roomService->update($request->validated(), $id));
        } catch (\Exception $exception) {
            report($exception);
            return response()->json(['error' => 'There is an error.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy(int $id): \Illuminate\Http\JsonResponse
    {
        try {
            $this->roomService->deleteById($id);
            return response()->json(['message' => 'Deleted successfully'], Response::HTTP_OK);
        } catch (\Exception $exception) {
            report($exception);
            return response()->json(['error' => 'There is an error.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
