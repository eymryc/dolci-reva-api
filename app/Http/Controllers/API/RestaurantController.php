<?php

namespace App\Http\Controllers\API;

use App\Services\RestaurantService;
use App\Http\Controllers\Controller;
use App\Http\Resources\RestaurantResource;
use App\Http\Requests\RestaurantRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\Response;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(name="Restaurants")
 */
class RestaurantController extends Controller
{
    /**
     * @var RestaurantService
     */
    protected RestaurantService $restaurantService;

    /**
     * RestaurantController Constructor
     *
     * @param RestaurantService $restaurantService
     */
    public function __construct(RestaurantService $restaurantService)
    {
        $this->restaurantService = $restaurantService;
    }

    public function index(): AnonymousResourceCollection
    {
        return RestaurantResource::collection($this->restaurantService->getAllWithPagination());
    }

    /**
     * @OA\Get(
     *     path="/public/restaurants",
     *     summary="Liste des restaurants (public)",
     *     description="Récupère la liste de tous les restaurants disponibles sans authentification",
     *     operationId="getAllRestaurants",
     *     tags={"Public", "Restaurants"},
     *     @OA\Response(
     *         response=200,
     *         description="Liste des restaurants récupérée avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Restaurant Le Gourmet"),
     *                     @OA\Property(property="description", type="string", example="Restaurant gastronomique au cœur de la ville"),
     *                     @OA\Property(property="address", type="string", example="123 Rue de la Paix"),
     *                     @OA\Property(property="city", type="string", example="Paris"),
     *                     @OA\Property(property="country", type="string", example="France"),
     *                     @OA\Property(property="opening_hours", type="object",
     *                         @OA\Property(property="monday", type="object",
     *                             @OA\Property(property="open", type="string", example="12:00"),
     *                             @OA\Property(property="close", type="string", example="22:00")
     *                         )
     *                     ),
     *                     @OA\Property(property="latitude", type="number", format="float", example=48.8566),
     *                     @OA\Property(property="longitude", type="number", format="float", example=2.3522),
     *                     @OA\Property(property="is_active", type="boolean", example=true),
     *                     @OA\Property(property="tables", type="array",
     *                         @OA\Items(
     *                             @OA\Property(property="id", type="integer", example=1),
     *                             @OA\Property(property="table_number", type="string", example="T1"),
     *                             @OA\Property(property="capacity", type="integer", example=4),
     *                             @OA\Property(property="location", type="string", example="window"),
     *                             @OA\Property(property="table_type", type="string", example="standard")
     *                         )
     *                     ),
     *                     @OA\Property(property="amenities", type="array",
     *                         @OA\Items(
     *                             @OA\Property(property="id", type="integer", example=1),
     *                             @OA\Property(property="name", type="string", example="WiFi")
     *                         )
     *                     ),
     *                     @OA\Property(property="main_image_url", type="string", example="http://example.com/image.jpg"),
     *                     @OA\Property(property="gallery_images", type="array", @OA\Items(type="object")),
     *                     @OA\Property(property="owner", type="object",
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="first_name", type="string", example="Jean"),
     *                         @OA\Property(property="last_name", type="string", example="Dupont"),
     *                         @OA\Property(property="email", type="string", example="jean@example.com")
     *                     )
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function getAllRestaurants(): AnonymousResourceCollection
    {
        return RestaurantResource::collection($this->restaurantService->getAvailable());
    }

    public function store(RestaurantRequest $request): RestaurantResource|JsonResponse
    {
        try {
            $data = $request->validated();
            
            // Save the restaurant using the service
            $restaurant = $this->restaurantService->save($data);

            // Set response
            $response = response()->json([
                'status'    => Response::HTTP_CREATED,
                'success'   => true,
                'message'   => 'Restaurant created successfully',
                'data'      => new RestaurantResource($restaurant)
            ], Response::HTTP_CREATED);

            // Return the response
            return $response;
        } catch (\Exception $exception) {
            report($exception);
            return response()->json(['error' => 'There is an error.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(int $id): RestaurantResource
    {
        return RestaurantResource::make($this->restaurantService->getById($id));
    }

    public function update(RestaurantRequest $request, int $id): RestaurantResource|JsonResponse
    {
        try {
            $data = $request->validated();
            
            // Update the restaurant using the service
            $restaurant = $this->restaurantService->update($data, $id);

            // Set response
            $response = response()->json([
                'status'    => Response::HTTP_OK,
                'success'   => true,
                'message'   => 'Restaurant updated successfully',
                'data'      => new RestaurantResource($restaurant)
            ], Response::HTTP_OK);

            // Return the response
            return $response;
        } catch (\Exception $exception) {
            report($exception);
            return response()->json(['error' => 'There is an error.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->restaurantService->deleteById($id);
            return response()->json([
                'status' => Response::HTTP_OK,
                'success' => true,
                'message' => 'Restaurant deleted successfully'
            ], Response::HTTP_OK);
        } catch (\Exception $exception) {
            report($exception);
            return response()->json(['error' => 'There is an error.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get available tables for a restaurant.
     */
    public function getAvailableTables(Request $request, int $restaurantId): JsonResponse
    {
        $request->validate([
            'date' => 'required|date|after:today',
            'time' => 'required|date_format:H:i',
            'guests' => 'required|integer|min:1|max:20'
        ]);

        try {
            $tables = $this->restaurantService->getAvailableTables(
                $restaurantId,
                $request->date,
                $request->time,
                $request->guests
            );

            return response()->json([
                'success' => true,
                'data' => $tables->map(function ($table) {
                    return [
                        'id' => $table->id,
                        'table_number' => $table->table_number,
                        'capacity' => $table->capacity,
                        'location' => $table->location,
                        'table_type' => $table->table_type,
                        'display_name' => $table->display_name,
                        'location_description' => $table->location_description,
                        'type_description' => $table->type_description
                    ];
                })
            ]);
        } catch (\Exception $exception) {
            report($exception);
            return response()->json(['error' => 'There is an error.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get available time slots for a restaurant.
     */
    public function getAvailableTimeSlots(Request $request, int $restaurantId): JsonResponse
    {
        $request->validate([
            'date' => 'required|date|after:today',
            'guests' => 'required|integer|min:1|max:20'
        ]);

        try {
            $timeSlots = $this->restaurantService->getAvailableTimeSlots(
                $restaurantId,
                $request->date,
                $request->guests
            );

            return response()->json([
                'success' => true,
                'data' => $timeSlots
            ]);
        } catch (\Exception $exception) {
            report($exception);
            return response()->json(['error' => 'There is an error.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

}