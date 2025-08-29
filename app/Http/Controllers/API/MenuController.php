<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\MenuRequest;
use App\Http\Resources\MenuResource;
use App\Services\MenuService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MenuController extends Controller
{
    /**
     * @var MenuService
     */
    protected MenuService $menuService;

    /**
     * DummyModel Constructor
     *
     * @param MenuService $menuService
     *
     */
    public function __construct(MenuService $menuService)
    {
        $this->menuService = $menuService;
    }

    public function index(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        return MenuResource::collection($this->menuService->getAll());
    }

    public function store(MenuRequest $request): MenuResource|\Illuminate\Http\JsonResponse
    {
        try {
            return new MenuResource($this->menuService->save($request->validated()));
        } catch (\Exception $exception) {
            report($exception);
            return response()->json(['error' => 'There is an error.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(int $id): MenuResource
    {
        return MenuResource::make($this->menuService->getById($id));
    }

    public function update(MenuRequest $request, int $id): MenuResource|\Illuminate\Http\JsonResponse
    {
        try {
            return new MenuResource($this->menuService->update($request->validated(), $id));
        } catch (\Exception $exception) {
            report($exception);
            return response()->json(['error' => 'There is an error.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy(int $id): \Illuminate\Http\JsonResponse
    {
        try {
            $this->menuService->deleteById($id);
            return response()->json(['message' => 'Deleted successfully'], Response::HTTP_OK);
        } catch (\Exception $exception) {
            report($exception);
            return response()->json(['error' => 'There is an error.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
