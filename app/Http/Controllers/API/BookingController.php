<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\BookingRequest;
use App\Http\Resources\BookingResource;
use App\Services\BookingService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BookingController extends Controller
{
    /**
     * @var BookingService
     */
    protected BookingService $bookingService;

    /**
     * DummyModel Constructor
     *
     * @param BookingService $bookingService
     *
     */
    public function __construct(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    public function index(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        return BookingResource::collection($this->bookingService->getAll());
    }

    public function store(BookingRequest $request): BookingResource|\Illuminate\Http\JsonResponse
    {
        try {
            return new BookingResource($this->bookingService->save($request->validated()));
        } catch (\Exception $exception) {
            report($exception);
            return response()->json(['error' => 'There is an error.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(int $id): BookingResource
    {
        return BookingResource::make($this->bookingService->getById($id));
    }

    public function update(BookingRequest $request, int $id): BookingResource|\Illuminate\Http\JsonResponse
    {
        try {
            return new BookingResource($this->bookingService->update($request->validated(), $id));
        } catch (\Exception $exception) {
            report($exception);
            return response()->json(['error' => 'There is an error.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy(int $id): \Illuminate\Http\JsonResponse
    {
        try {
            $this->bookingService->deleteById($id);
            return response()->json(['message' => 'Deleted successfully'], Response::HTTP_OK);
        } catch (\Exception $exception) {
            report($exception);
            return response()->json(['error' => 'There is an error.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
