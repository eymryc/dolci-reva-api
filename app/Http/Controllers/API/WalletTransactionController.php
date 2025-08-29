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
            return new WalletTransactionResource($this->walletTransactionService->save($request->validated()));
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
            return new WalletTransactionResource($this->walletTransactionService->update($request->validated(), $id));
        } catch (\Exception $exception) {
            report($exception);
            return response()->json(['error' => 'There is an error.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy(int $id): \Illuminate\Http\JsonResponse
    {
        try {
            $this->walletTransactionService->deleteById($id);
            return response()->json(['message' => 'Deleted successfully'], Response::HTTP_OK);
        } catch (\Exception $exception) {
            report($exception);
            return response()->json(['error' => 'There is an error.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
