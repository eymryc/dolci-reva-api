<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\HotelRoomService;
use App\Http\Controllers\Controller;
use App\Http\Requests\HotelRoomRequest;
use App\Http\Resources\HotelRoomResource;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(name="Hotel Rooms")
 */
class HotelRoomController extends Controller
{
    /**
     * @var HotelRoomService
     */
    protected HotelRoomService $hotelRoomService;

    /**
     * HotelRoomController Constructor
     *
     * @param HotelRoomService $hotelRoomService
     */
    public function __construct(HotelRoomService $hotelRoomService)
    {
        $this->hotelRoomService = $hotelRoomService;
    }

    /**
     * @OA\Get(
     *     path="/hotel-rooms",
     *     summary="Liste des chambres d'hôtel",
     *     description="Récupère la liste de toutes les chambres d'hôtel",
     *     operationId="getHotelRooms",
     *     tags={"Hotel Rooms"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Liste des chambres récupérée avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(ref="#/components/schemas/HotelRoom")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Non authentifié",
     *         @OA\JsonContent(ref="#/components/schemas/Error")
     *     )
     * )
     */
    public function index(): AnonymousResourceCollection
    {
        return HotelRoomResource::collection($this->hotelRoomService->getAll());
    }

    /**
     * @OA\Get(
     *     path="/hotel-rooms/by-hotel/{hotelId}",
     *     summary="Chambres par hôtel",
     *     description="Récupère les chambres d'un hôtel spécifique",
     *     operationId="getHotelRoomsByHotel",
     *     tags={"Hotel Rooms"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="hotelId",
     *         in="path",
     *         required=true,
     *         description="ID de l'hôtel",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Chambres récupérées avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(ref="#/components/schemas/HotelRoom")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Non authentifié",
     *         @OA\JsonContent(ref="#/components/schemas/Error")
     *     )
     * )
     */
    public function getByHotel(int $hotelId): AnonymousResourceCollection
    {
        return HotelRoomResource::collection($this->hotelRoomService->getByHotelId($hotelId));
    }

    /**
     * @OA\Post(
     *     path="/hotel-rooms",
     *     summary="Créer une chambre d'hôtel",
     *     description="Crée une nouvelle chambre d'hôtel",
     *     operationId="createHotelRoom",
     *     tags={"Hotel Rooms"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"hotel_id", "room_number", "room_type", "capacity", "price_per_night"},
     *             @OA\Property(property="hotel_id", type="integer", example=1),
     *             @OA\Property(property="room_number", type="string", example="101"),
     *             @OA\Property(property="room_type", type="string", example="Deluxe"),
     *             @OA\Property(property="capacity", type="integer", example=2),
     *             @OA\Property(property="price_per_night", type="number", format="float", example=150.00),
     *             @OA\Property(property="description", type="string", example="Chambre de luxe avec vue sur la mer"),
     *             @OA\Property(property="is_available", type="boolean", example=true),
     *             @OA\Property(property="is_active", type="boolean", example=true),
     *             @OA\Property(property="images", type="array", @OA\Items(type="string", format="binary")),
     *             @OA\Property(property="amenities", type="array", @OA\Items(type="integer"), example={1, 2, 3})
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Chambre créée avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=201),
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Hotel room created successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/HotelRoom")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erreur de validation",
     *         @OA\JsonContent(ref="#/components/schemas/ValidationError")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Non authentifié",
     *         @OA\JsonContent(ref="#/components/schemas/Error")
     *     )
     * )
     */
    public function store(HotelRoomRequest $request): HotelRoomResource|JsonResponse
    {
        try {
            $data = new HotelRoomResource($this->hotelRoomService->save($request->validated()));

            // Set response
            $response = response()->json([
                'status'    => Response::HTTP_CREATED,
                'success'   => true,    
                'message'   => 'Hotel room created successfully',
                'data'      => $data
            ], Response::HTTP_CREATED);

            // Return the response
            return $response;
        } catch (\Exception $exception) {
            report($exception);
            return response()->json(['error' => 'There is an error.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Get(
     *     path="/hotel-rooms/{id}",
     *     summary="Afficher une chambre d'hôtel",
     *     description="Récupère les détails d'une chambre d'hôtel spécifique",
     *     operationId="getHotelRoom",
     *     tags={"Hotel Rooms"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la chambre",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Chambre récupérée avec succès",
     *         @OA\JsonContent(ref="#/components/schemas/HotelRoom")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Chambre non trouvée",
     *         @OA\JsonContent(ref="#/components/schemas/Error")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Non authentifié",
     *         @OA\JsonContent(ref="#/components/schemas/Error")
     *     )
     * )
     */
    public function show(int $id): HotelRoomResource
    {
        return HotelRoomResource::make($this->hotelRoomService->getById($id));
    }

    /**
     * @OA\Put(
     *     path="/hotel-rooms/{id}",
     *     summary="Modifier une chambre d'hôtel",
     *     description="Met à jour une chambre d'hôtel existante",
     *     operationId="updateHotelRoom",
     *     tags={"Hotel Rooms"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la chambre à modifier",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="hotel_id", type="integer", example=1),
     *             @OA\Property(property="room_number", type="string", example="101"),
     *             @OA\Property(property="room_type", type="string", example="Deluxe"),
     *             @OA\Property(property="capacity", type="integer", example=2),
     *             @OA\Property(property="price_per_night", type="number", format="float", example=150.00),
     *             @OA\Property(property="description", type="string", example="Chambre de luxe avec vue sur la mer"),
     *             @OA\Property(property="is_available", type="boolean", example=true),
     *             @OA\Property(property="is_active", type="boolean", example=true),
     *             @OA\Property(property="images", type="array", @OA\Items(type="string", format="binary")),
     *             @OA\Property(property="amenities", type="array", @OA\Items(type="integer"), example={1, 2, 3})
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Chambre modifiée avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Hotel room updated successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/HotelRoom")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Chambre non trouvée",
     *         @OA\JsonContent(ref="#/components/schemas/Error")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erreur de validation",
     *         @OA\JsonContent(ref="#/components/schemas/ValidationError")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Non authentifié",
     *         @OA\JsonContent(ref="#/components/schemas/Error")
     *     )
     * )
     */
    public function update(HotelRoomRequest $request, int $id): HotelRoomResource|JsonResponse
    {
        try {
            $data = new HotelRoomResource($this->hotelRoomService->update($request->validated(), $id));
            
            // Set response
            $response = response()->json([
                'status'    => Response::HTTP_OK,
                'success'   => true,
                'message'   => 'Hotel room updated successfully',
                'data'      => $data
            ], Response::HTTP_OK);

            // Return the response
            return $response;
        } catch (\Exception $exception) {
            report($exception);
            return response()->json(['error' => 'There is an error.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Delete(
     *     path="/hotel-rooms/{id}",
     *     summary="Supprimer une chambre d'hôtel",
     *     description="Supprime une chambre d'hôtel existante",
     *     operationId="deleteHotelRoom",
     *     tags={"Hotel Rooms"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la chambre à supprimer",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Chambre supprimée avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Hotel room deleted successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Chambre non trouvée",
     *         @OA\JsonContent(ref="#/components/schemas/Error")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Non authentifié",
     *         @OA\JsonContent(ref="#/components/schemas/Error")
     *     )
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->hotelRoomService->deleteById($id);
            return response()->json([
                'status' => Response::HTTP_OK,
                'success' => true,
                'message' => 'Hotel room deleted successfully'
            ], Response::HTTP_OK);
        } catch (\Exception $exception) {
            report($exception);
            return response()->json(['error' => 'There is an error.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
