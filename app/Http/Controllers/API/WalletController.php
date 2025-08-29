<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\WalletRequest;
use App\Http\Resources\WalletResource;
use App\Services\WalletService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class WalletController extends Controller
{
    /**
     * @var WalletService
     */
    protected WalletService $walletService;

    /**
     * DummyModel Constructor
     *
     * @param WalletService $walletService
     *
     */
    public function __construct(WalletService $walletService)
    {
        $this->walletService = $walletService;
    }

    public function index(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        return WalletResource::collection($this->walletService->getAll());
    }

    public function store(WalletRequest $request): WalletResource|\Illuminate\Http\JsonResponse
    {
        try {
            return new WalletResource($this->walletService->save($request->validated()));
        } catch (\Exception $exception) {
            report($exception);
            return response()->json(['error' => 'There is an error.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(int $id): WalletResource
    {
        return WalletResource::make($this->walletService->getById($id));
    }

    public function update(WalletRequest $request, int $id): WalletResource|\Illuminate\Http\JsonResponse
    {
        try {
            return new WalletResource($this->walletService->update($request->validated(), $id));
        } catch (\Exception $exception) {
            report($exception);
            return response()->json(['error' => 'There is an error.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy(int $id): \Illuminate\Http\JsonResponse
    {
        try {
            $this->walletService->deleteById($id);
            return response()->json(['message' => 'Deleted successfully'], Response::HTTP_OK);
        } catch (\Exception $exception) {
            report($exception);
            return response()->json(['error' => 'There is an error.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
