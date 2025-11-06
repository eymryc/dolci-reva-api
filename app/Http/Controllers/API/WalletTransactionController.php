<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\WalletTransactionRequest;
use App\Http\Resources\WalletTransactionResource;
use App\Services\WalletTransactionService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class WalletTransactionController extends Controller
{
    /**
     * @var WalletTransactionService
     */
    protected WalletTransactionService $walletTransactionService;

    /**
     * DummyModel Constructor
     *
     * @param WalletTransactionService $walletTransactionService
     *
     */
    public function __construct(WalletTransactionService $walletTransactionService)
    {
        $this->walletTransactionService = $walletTransactionService;
    }

    public function index(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        return WalletTransactionResource::collection($this->walletTransactionService->getAll());
    }

    public function store(WalletTransactionRequest $request): WalletTransactionResource|\Illuminate\Http\JsonResponse
    {
        try {
            $data = new WalletTransactionResource($this->walletTransactionService->save($request->validated()));

            // Set response
            $response = response()->json([
                'status'    => Response::HTTP_CREATED,
                'success'   => true,    
                'message'   => 'Wallet transaction created successfully',
                'data'      => $data
            ], Response::HTTP_CREATED);

            // Return the response
            return $response;
        } catch (\Exception $exception) {
            report($exception);
            return response()->json(['error' => 'There is an error.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(int $id): WalletTransactionResource
    {
        return WalletTransactionResource::make($this->walletTransactionService->getById($id));
    }

    public function update(WalletTransactionRequest $request, int $id): WalletTransactionResource|\Illuminate\Http\JsonResponse
    {
        try {
            $data = new WalletTransactionResource($this->walletTransactionService->update($request->validated(), $id));
            
            // Set response
            $response = response()->json([
                'status'    => Response::HTTP_OK,
                'success'   => true,
                'message'   => 'Wallet transaction updated successfully',
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
            $this->walletTransactionService->deleteById($id);
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
