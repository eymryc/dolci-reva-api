<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Services\AmenityService;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\AmenityRequest;
use App\Http\Resources\AmenityResource;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(name="Amenities")
 */
class AmenityController extends Controller
{
    /**
     * @var AmenityService
     */
    protected AmenityService $amenityService;

    /**
     * DummyModel Constructor
     *
     * @param AmenityService $amenityService
     *
     */
    public function __construct(AmenityService $amenityService)
    {
        $this->amenityService = $amenityService;
    }

    /**
     * @OA\Get(
     *     path="/amenities",
     *     summary="Liste des équipements",
     *     description="Récupère la liste de tous les équipements",
     *     operationId="getAmenities",
     *     tags={"Amenities"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Liste des équipements récupérée avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(ref="#/components/schemas/Amenity")
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
    public function index():AnonymousResourceCollection
    {
        return AmenityResource::collection($this->amenityService->getAllWithPagination());
    }

    /**
     * @OA\Post(
     *     path="/amenities",
     *     summary="Créer un équipement",
     *     description="Crée un nouvel équipement",
     *     operationId="createAmenity",
     *     tags={"Amenities"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "description"},
     *             @OA\Property(property="name", type="string", example="Wi-Fi"),
     *             @OA\Property(property="description", type="string", example="Accès Internet sans fil"),
     *             @OA\Property(property="icon", type="string", example="wifi"),
     *             @OA\Property(property="is_active", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Équipement créé avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=201),
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Amenity created successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/Amenity")
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
    public function store(AmenityRequest $request): AmenityResource|JsonResponse
    {
        try {

            // Save the amenity using the service
            $data =  new AmenityResource($this->amenityService->firstOrCreate($request->validated()));

            // Set response
            $response = response()->json([
                'status'    => Response::HTTP_CREATED,
                'success'   => true,
                'message'   => 'Amenity created successfully',
                'data'      =>  $data
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
     *     path="/amenities/{id}",
     *     summary="Afficher un équipement",
     *     description="Récupère les détails d'un équipement spécifique",
     *     operationId="getAmenity",
     *     tags={"Amenities"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de l'équipement",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Équipement récupéré avec succès",
     *         @OA\JsonContent(ref="#/components/schemas/Amenity")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Équipement non trouvé",
     *         @OA\JsonContent(ref="#/components/schemas/Error")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Non authentifié",
     *         @OA\JsonContent(ref="#/components/schemas/Error")
     *     )
     * )
     */
    public function show(int $id): AmenityResource
    {
        return AmenityResource::make($this->amenityService->getById($id));
    }

    /**
     * @OA\Put(
     *     path="/amenities/{id}",
     *     summary="Modifier un équipement",
     *     description="Met à jour un équipement existant",
     *     operationId="updateAmenity",
     *     tags={"Amenities"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de l'équipement à modifier",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Wi-Fi Modifié"),
     *             @OA\Property(property="description", type="string", example="Accès Internet sans fil"),
     *             @OA\Property(property="icon", type="string", example="wifi"),
     *             @OA\Property(property="is_active", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Équipement modifié avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Amenity updated successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/Amenity")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Équipement non trouvé",
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
    public function update(AmenityRequest $request, int $id): AmenityResource|JsonResponse
    {
        try {
            $data =  new AmenityResource($this->amenityService->update($request->validated(), $id));

            // Set response
            $response = response()->json([
                'status'    => Response::HTTP_OK,
                'success'   => true,
                'message'   => 'Amenity updated successfully',
                'data'      =>  $data
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
     *     path="/amenities/{id}",
     *     summary="Supprimer un équipement",
     *     description="Supprime un équipement existant",
     *     operationId="deleteAmenity",
     *     tags={"Amenities"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de l'équipement à supprimer",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Équipement supprimé avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Deleted successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Équipement non trouvé",
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
            $this->amenityService->deleteById($id);
            return response()->json([
                'status' => Response::HTTP_OK,
                'success' => true,
                'message' => 'Deleted successfully'
            ], Response::HTTP_OK);
        } catch (\Exception $exception) {
            report($exception);
            return response()->json(['error' => 'There is an error.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Get(
     *     path="/public/amenities",
     *     summary="Liste des équipements (public)",
     *     description="Récupère la liste de tous les équipements disponibles sans authentification",
     *     operationId="getPublicAmenities",
     *     tags={"Public", "Amenities"},
     *     @OA\Response(
     *         response=200,
     *         description="Liste des équipements récupérée avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(ref="#/components/schemas/Amenity")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erreur serveur",
     *         @OA\JsonContent(ref="#/components/schemas/Error")
     *     )
     * )
     */
    public function publicIndex()
    {
        try {
            $amenities = $this->amenityService->getAll();
            return AmenityResource::collection($amenities);
        } catch (\Exception $exception) {
            report($exception);
            return response()->json(['error' => 'There is an error.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
