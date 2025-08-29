<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\PropertyRequest;
use App\Http\Resources\PropertyResource;
use App\Services\PropertyService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PropertyController extends Controller
{
    /**
     * @var PropertyService
     */
    protected PropertyService $propertyService;

    /**
     * DummyModel Constructor
     *
     * @param PropertyService $propertyService
     *
     */
    public function __construct(PropertyService $propertyService)
    {
        $this->propertyService = $propertyService;
    }

    public function index(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        return PropertyResource::collection($this->propertyService->getAll());
    }

    public function store(PropertyRequest $request): PropertyResource|\Illuminate\Http\JsonResponse
    {   
        try {
            $data =  new PropertyResource($this->propertyService->save($request->validated()));

            // Set response
            $response = response()->json([
                'status'    => Response::HTTP_CREATED,
                'success'   => true,
                'message'   => 'Property created successfully',
                'data'      =>  $data
            ], Response::HTTP_CREATED);
            
            // Return the response
            return $response;

        } catch (\Exception $exception) {
            report($exception);
            return response()->json(['error' => $exception], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(int $id): PropertyResource
    {
        return PropertyResource::make($this->propertyService->getById($id));
    }

    public function update(PropertyRequest $request, int $id): PropertyResource|\Illuminate\Http\JsonResponse
    {
        try {
            return new PropertyResource($this->propertyService->update($request->validated(), $id));
        } catch (\Exception $exception) {
            report($exception);
            return response()->json(['error' => 'There is an error.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy(int $id): \Illuminate\Http\JsonResponse
    {
        try {
            $this->propertyService->deleteById($id);
            return response()->json(['message' => 'Deleted successfully'], Response::HTTP_OK);
        } catch (\Exception $exception) {
            report($exception);
            return response()->json(['error' => 'There is an error.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
