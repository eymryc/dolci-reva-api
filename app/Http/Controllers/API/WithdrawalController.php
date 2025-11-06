<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\WithdrawalRequest;
use App\Http\Resources\WithdrawalResource;
use App\Services\WithdrawalService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class WithdrawalController extends Controller
{
    /**
     * @var WithdrawalService
     */
    protected WithdrawalService $withdrawalService;

    /**
     * DummyModel Constructor
     *
     * @param WithdrawalService $withdrawalService
     *
     */
    public function __construct(WithdrawalService $withdrawalService)
    {
        $this->withdrawalService = $withdrawalService;
    }

    public function index(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        return WithdrawalResource::collection($this->withdrawalService->getAll());
    }

    public function store(WithdrawalRequest $request): WithdrawalResource|\Illuminate\Http\JsonResponse
    {
        try {
            $data = new WithdrawalResource($this->withdrawalService->save($request->validated()));

            // Set response
            $response = response()->json([
                'status'    => Response::HTTP_CREATED,
                'success'   => true,    
                'message'   => 'Withdrawal created successfully',
                'data'      => $data
            ], Response::HTTP_CREATED);

            // Return the response
            return $response;
        } catch (\Exception $exception) {
            report($exception);
            return response()->json(['error' => 'There is an error.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(int $id): WithdrawalResource
    {
        return WithdrawalResource::make($this->withdrawalService->getById($id));
    }

    public function update(WithdrawalRequest $request, int $id): WithdrawalResource|\Illuminate\Http\JsonResponse
    {
        try {
            $data = new WithdrawalResource($this->withdrawalService->update($request->validated(), $id));
            
            // Set response
            $response = response()->json([
                'status'    => Response::HTTP_OK,
                'success'   => true,
                'message'   => 'Withdrawal updated successfully',
                'data'      => $data
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
            $this->withdrawalService->deleteById($id);
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
