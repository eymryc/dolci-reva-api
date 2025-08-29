<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\Services\UserService;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    /**
     * Service layer for managing user-related business logic.
     *
     * @var UserService
     */
    protected UserService $userService;

    /**
     * Inject the UserService dependency.
     *
     * @param UserService $userService
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Get a paginated list of users.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        return UserResource::collection($this->userService->getAllWithPagination());
    }

    /**
     * Store a newly created user in storage.
     *
     * @param UserRequest $request
     * @return UserResource|\Illuminate\Http\JsonResponse
     */
    public function store(UserRequest $request): UserResource|\Illuminate\Http\JsonResponse
    {
        try {
            $user = $this->userService->save($request->validated());
            $data = new UserResource($user);

            return response()->json([
                'status'  => Response::HTTP_CREATED,
                'success' => true,
                'message' => 'Account created successfully',
                'data'    => $data,
            ], Response::HTTP_CREATED);

        } catch (\Exception $exception) {
            report($exception);

            return response()->json([
                'error' => 'Internal server error.'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified user.
     *
     * @param int $id
     * @return UserResource
     */
    public function show(int $id): UserResource
    {
        return UserResource::make($this->userService->getById($id));
    }

    /**
     * Update the specified user.
     *
     * @param UserRequest $request
     * @param int $id
     * @return UserResource|\Illuminate\Http\JsonResponse
     */
    public function update(UserRequest $request, int $id): UserResource|\Illuminate\Http\JsonResponse
    {
        try {
            $user = $this->userService->update($request->validated(), $id);

            if (!$user) {
                return response()->json([
                    'status'  => Response::HTTP_NOT_FOUND,
                    'success' => false,
                    'message' => 'User not found',
                ], Response::HTTP_NOT_FOUND);
            }

            $data = new UserResource($user);

            return response()->json([
                'status'  => Response::HTTP_OK,
                'success' => true,
                'message' => 'Account updated successfully',
                'data'    => $data,
            ], Response::HTTP_OK);

        } catch (\Exception $exception) {
            report($exception);

            return response()->json([
                'error' => 'Internal server error.'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified user from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(int $id): \Illuminate\Http\JsonResponse
    {
        try {
            $deleted = $this->userService->deleteById($id);

            if (!$deleted) {
                return response()->json([
                    'status'  => Response::HTTP_NOT_FOUND,
                    'success' => false,
                    'message' => 'User not found',
                ], Response::HTTP_NOT_FOUND);
            }

            return response()->json([
                'status'  => Response::HTTP_OK,
                'success' => true,
                'message' => 'Deleted successfully',
            ], Response::HTTP_OK);

        } catch (\Exception $exception) {
            report($exception);

            return response()->json([
                'error' => 'Internal server error.'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
