<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\AmenityRequest;
use App\Http\Resources\AmenityResource;
use App\Services\AmenityService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AmenityController extends Controller
{
    /**
     * @var AmenityService
     */
    protected AmenityService $amenityService;

    /**
     * DummyModel Constructor
     *
     * @param AmenityService $amenityService
     *
     */
    public function __construct(AmenityService $amenityService)
    {
        $this->amenityService = $amenityService;
    }

    public function index(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        return AmenityResource::collection($this->amenityService->getAllWithPagination());
    }

    public function store(AmenityRequest $request): AmenityResource|\Illuminate\Http\JsonResponse
    {
        try {

            // Save the amenity using the service
            $data =  new AmenityResource($this->amenityService->firstOrCreate($request->validated()));

            // Set response
            $response = response()->json([
                'status'    => Response::HTTP_CREATED,
                'success'   => true,
                'message'   => 'Amenity created successfully',
                'data'      =>  $data
            ], Response::HTTP_CREATED);

            // Return the response
            return $response;
        } catch (\Exception $exception) {
            report($exception);
            return response()->json(['error' => 'There is an error.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(int $id): AmenityResource
    {
        return AmenityResource::make($this->amenityService->getById($id));
    }

    public function update(AmenityRequest $request, int $id): AmenityResource|\Illuminate\Http\JsonResponse
    {
        try {
            $data =  new AmenityResource($this->amenityService->update($request->validated(), $id));

            // Set response
            $response = response()->json([
                'status'    => Response::HTTP_OK,
                'success'   => true,
                'message'   => 'Amenity updated successfully',
                'data'      =>  $data
            ], Response::HTTP_OK);

            // Return the response
            return $response;
        } catch (\Exception $exception) {
            report($exception);
            return response()->json(['error' => 'There is an error.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy(int $id): \Illuminate\Http\JsonResponse
    {
        try {
            $this->amenityService->deleteById($id);
            return response()->json([
                'status' => Response::HTTP_OK,
                'success' => true,
                'message' => 'Deleted successfully'
            ], Response::HTTP_OK);
        } catch (\Exception $exception) {
            report($exception);
            return response()->json(['error' => 'There is an error.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
