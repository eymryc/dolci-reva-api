<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Services\CategoryService;
use Symfony\Component\HttpFoundation\Response;

class CategoryController extends Controller
{
    /**
     * @var CategoryService
     */
    protected CategoryService $categoryService;

    /**
     * DummyModel Constructor
     *
     * @param CategoryService $categoryService
     *
     */
    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {   
        //
        return CategoryResource::collection($this->categoryService->getAllWithPagination());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CategoryRequest $request
     * @return CategoryResource|\Illuminate\Http\JsonResponse
     */
    public function store(CategoryRequest $request): CategoryResource|\Illuminate\Http\JsonResponse
    {       
        try {

            // Save the category using the service
            $data =  new CategoryResource($this->categoryService->save($request->validated()));

            // Set response
            $response = response()->json([
                'status'    => Response::HTTP_CREATED,
                'success'   => true,
                'message'   => 'Category created successfully',
                'data'      =>  $data
            ], Response::HTTP_CREATED);

            // Return response
            return $response;
        } catch (\Exception $exception) {
            report($exception);
            return response()->json(['error' => 'There is an error.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return CategoryResource
     */
    public function show(int $id): CategoryResource
    {
        return CategoryResource::make($this->categoryService->getById($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param CategoryRequest $request
     * @param int $id
     * @return CategoryResource|\Illuminate\Http\JsonResponse
     */
    public function update(CategoryRequest $request, int $id): CategoryResource|\Illuminate\Http\JsonResponse
    {
        try {

            // Update the category using the service
            $data = new CategoryResource($this->categoryService->update($request->validated(), $id));

            // Set response
            $response = response()->json([
                'status'    => Response::HTTP_OK,
                'success'   => true,
                'message'   => 'Category updated successfully',
                'data'      => $data
            ], Response::HTTP_OK);

            // Return response
            return $response;
        } catch (\Exception $exception) {
            report($exception);
            return response()->json(['error' => 'There is an error.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(int $id): \Illuminate\Http\JsonResponse
    {
        try {
            $this->categoryService->deleteById($id);
            return response()->json([
                'success' => true,
                'status' => Response::HTTP_OK,
                'message' => 'Deleted successfully'
            ], Response::HTTP_OK);
        } catch (\Exception $exception) {
            report($exception);
            return response()->json(['error' => 'There is an error.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
