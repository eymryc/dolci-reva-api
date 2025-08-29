<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\MenuItemRequest;
use App\Http\Resources\MenuItemResource;
use App\Services\MenuItemService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MenuItemController extends Controller
{
    /**
     * @var MenuItemService
     */
    protected MenuItemService $menuItemService;

    /**
     * DummyModel Constructor
     *
     * @param MenuItemService $menuItemService
     *
     */
    public function __construct(MenuItemService $menuItemService)
    {
        $this->menuItemService = $menuItemService;
    }

    public function index(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        return MenuItemResource::collection($this->menuItemService->getAll());
    }

    public function store(MenuItemRequest $request): MenuItemResource|\Illuminate\Http\JsonResponse
    {
        try {
            return new MenuItemResource($this->menuItemService->save($request->validated()));
        } catch (\Exception $exception) {
            report($exception);
            return response()->json(['error' => 'There is an error.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(int $id): MenuItemResource
    {
        return MenuItemResource::make($this->menuItemService->getById($id));
    }

    public function update(MenuItemRequest $request, int $id): MenuItemResource|\Illuminate\Http\JsonResponse
    {
        try {
            return new MenuItemResource($this->menuItemService->update($request->validated(), $id));
        } catch (\Exception $exception) {
            report($exception);
            return response()->json(['error' => 'There is an error.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy(int $id): \Illuminate\Http\JsonResponse
    {
        try {
            $this->menuItemService->deleteById($id);
            return response()->json(['message' => 'Deleted successfully'], Response::HTTP_OK);
        } catch (\Exception $exception) {
            report($exception);
            return response()->json(['error' => 'There is an error.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
