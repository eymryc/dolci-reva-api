<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\CommissionRequest;
use App\Http\Resources\CommissionResource;
use App\Services\CommissionService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CommissionController extends Controller
{
    /**
     * @var CommissionService
     */
    protected CommissionService $commissionService;

    /**
     * DummyModel Constructor
     *
     * @param CommissionService $commissionService
     *
     */
    public function __construct(CommissionService $commissionService)
    {
        $this->commissionService = $commissionService;
    }

    public function index(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        return CommissionResource::collection($this->commissionService->getAll());
    }

    public function store(CommissionRequest $request): CommissionResource|\Illuminate\Http\JsonResponse
    {
        try {
            return new CommissionResource($this->commissionService->save($request->validated()));
        } catch (\Exception $exception) {
            report($exception);
            return response()->json(['error' => 'There is an error.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(int $id): CommissionResource
    {
        return CommissionResource::make($this->commissionService->getById($id));
    }

    public function update(CommissionRequest $request, int $id): CommissionResource|\Illuminate\Http\JsonResponse
    {
        try {
            return new CommissionResource($this->commissionService->update($request->validated(), $id));
        } catch (\Exception $exception) {
            report($exception);
            return response()->json(['error' => 'There is an error.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy(int $id): \Illuminate\Http\JsonResponse
    {
        try {
            $this->commissionService->deleteById($id);
            return response()->json(['message' => 'Deleted successfully'], Response::HTTP_OK);
        } catch (\Exception $exception) {
            report($exception);
            return response()->json(['error' => 'There is an error.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
