<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddressRequest;
use App\Http\Resources\AddressResource;
use App\Services\AddressService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AddressController extends Controller
{
    /**
     * @var AddressService
     */
    protected AddressService $addressService;

    /**
     * DummyModel Constructor
     *
     * @param AddressService $addressService
     *
     */
    public function __construct(AddressService $addressService)
    {
        $this->addressService = $addressService;
    }

    public function index(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        return AddressResource::collection($this->addressService->getAll());
    }

    public function store(AddressRequest $request): AddressResource|\Illuminate\Http\JsonResponse
    {
        try {
            $data =  new AddressResource($this->addressService->save($request->validated()));

            // Set response
            $response = response()->json([
                'status'    => Response::HTTP_CREATED,
                'success'   => true,    
                'message'   => 'Address created successfully',
                'data'      =>  $data
            ], Response::HTTP_CREATED);

            // Return the response
            return $response;
        } catch (\Exception $exception) {
            report($exception);
            return response()->json(['error' => 'There is an error.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(int $id): AddressResource
    {
        return AddressResource::make($this->addressService->getById($id));
    }

    public function update(AddressRequest $request, int $id): AddressResource|\Illuminate\Http\JsonResponse
    {
        try {
            $data =  new AddressResource($this->addressService->update($request->validated(), $id));
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
            $this->addressService->deleteById($id);
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
