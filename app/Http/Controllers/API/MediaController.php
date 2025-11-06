<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaController extends Controller
{
    /**
     * Upload media for a specific model.
     */
    public function upload(Request $request): JsonResponse
    {
        $request->validate([
            'model_type' => 'required|string',
            'model_id' => 'required|integer',
            'collection' => 'required|string',
            'file' => 'required|file|mimes:jpeg,png,jpg,gif,webp|max:10240', // 10MB max
        ]);

        try {
            // Find the model instance
            $modelClass = $request->model_type;
            $model = $modelClass::findOrFail($request->model_id);

            // Upload the file
            $media = $model->addMediaFromRequest('file')
                ->toMediaCollection($request->collection);

            return response()->json([
                'status' => Response::HTTP_CREATED,
                'success' => true,
                'message' => 'Media uploaded successfully',
                'data' => [
                    'id' => $media->id,
                    'name' => $media->name,
                    'file_name' => $media->file_name,
                    'mime_type' => $media->mime_type,
                    'size' => $media->size,
                    'collection_name' => $media->collection_name,
                    'url' => $media->getUrl(),
                    'thumb_url' => $media->getUrl('thumb'),
                    'medium_url' => $media->getUrl('medium'),
                    'large_url' => $media->getUrl('large'),
                ]
            ], Response::HTTP_CREATED);

        } catch (\Exception $exception) {
            report($exception);
            return response()->json([
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'success' => false,
                'message' => 'Error uploading media: ' . $exception->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get media for a specific model.
     */
    public function getMedia(Request $request): JsonResponse
    {
        $request->validate([
            'model_type' => 'required|string',
            'model_id' => 'required|integer',
            'collection' => 'nullable|string',
        ]);

        try {
            // Find the model instance
            $modelClass = $request->model_type;
            $model = $modelClass::findOrFail($request->model_id);

            // Get media
            $query = $model->media();
            if ($request->collection) {
                $query->where('collection_name', $request->collection);
            }
            $media = $query->get();

            $mediaData = $media->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'file_name' => $item->file_name,
                    'mime_type' => $item->mime_type,
                    'size' => $item->size,
                    'collection_name' => $item->collection_name,
                    'url' => $item->getUrl(),
                    'thumb_url' => $item->getUrl('thumb'),
                    'medium_url' => $item->getUrl('medium'),
                    'large_url' => $item->getUrl('large'),
                    'created_at' => $item->created_at,
                ];
            });

            return response()->json([
                'status' => Response::HTTP_OK,
                'success' => true,
                'data' => $mediaData
            ], Response::HTTP_OK);

        } catch (\Exception $exception) {
            report($exception);
            return response()->json([
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'success' => false,
                'message' => 'Error retrieving media: ' . $exception->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Delete a specific media item.
     */
    public function deleteMedia(Media $media): JsonResponse
    {
        try {
            $media->delete();

            return response()->json([
                'status' => Response::HTTP_OK,
                'success' => true,
                'message' => 'Media deleted successfully'
            ], Response::HTTP_OK);

        } catch (\Exception $exception) {
            report($exception);
            return response()->json([
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'success' => false,
                'message' => 'Error deleting media: ' . $exception->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Clear all media from a collection for a specific model.
     */
    public function clearCollection(Request $request): JsonResponse
    {
        $request->validate([
            'model_type' => 'required|string',
            'model_id' => 'required|integer',
            'collection' => 'required|string',
        ]);

        try {
            // Find the model instance
            $modelClass = $request->model_type;
            $model = $modelClass::findOrFail($request->model_id);

            // Clear the collection
            $model->clearMediaCollection($request->collection);

            return response()->json([
                'status' => Response::HTTP_OK,
                'success' => true,
                'message' => 'Collection cleared successfully'
            ], Response::HTTP_OK);

        } catch (\Exception $exception) {
            report($exception);
            return response()->json([
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'success' => false,
                'message' => 'Error clearing collection: ' . $exception->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
