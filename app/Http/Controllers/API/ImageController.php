<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ImageRequest;
use App\Http\Resources\ImageResource;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ImageController extends Controller
{
    /**
     * @var ImageService
     */
    protected ImageService $imageService;

    /**
     * DummyModel Constructor
     *
     * @param ImageService $imageService
     *
     */
    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    public function index(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        return ImageResource::collection($this->imageService->getAll());
    }

    public function store(ImageRequest $request): ImageResource|\Illuminate\Http\JsonResponse
    {
        try {
            return new ImageResource($this->imageService->save($request->validated()));
        } catch (\Exception $exception) {
            report($exception);
            return response()->json(['error' => 'There is an error.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(int $id): ImageResource
    {
        return ImageResource::make($this->imageService->getById($id));
    }

    public function update(ImageRequest $request, int $id): ImageResource|\Illuminate\Http\JsonResponse
    {
        try {
            return new ImageResource($this->imageService->update($request->validated(), $id));
        } catch (\Exception $exception) {
            report($exception);
            return response()->json(['error' => 'There is an error.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy(int $id): \Illuminate\Http\JsonResponse
    {
        try {
            $this->imageService->deleteById($id);
            return response()->json(['message' => 'Deleted successfully'], Response::HTTP_OK);
        } catch (\Exception $exception) {
            report($exception);
            return response()->json(['error' => 'There is an error.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
