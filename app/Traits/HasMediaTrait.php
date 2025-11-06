<?php

namespace App\Traits;

use Spatie\MediaLibrary\MediaCollections\Models\Media;

trait HasMediaTrait
{
    /**
     * Get media data for API responses.
     */
    public function getMediaData($collection = null): array
    {
        $query = $this->media();
        
        if ($collection) {
            $query->where('collection_name', $collection);
        }
        
        return $query->get()->map(function (Media $media) {
            return [
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
                'created_at' => $media->created_at,
            ];
        })->toArray();
    }

    /**
     * Get the main image URL (first image from images collection).
     */
    public function getMainImageUrlAttribute(): ?string
    {
        $media = $this->getFirstMedia('images');
        return $media ? $media->getUrl() : null;
    }

    /**
     * Get the main image thumbnail URL.
     */
    public function getMainImageThumbUrlAttribute(): ?string
    {
        $media = $this->getFirstMedia('images');
        return $media ? $media->getUrl('thumb') : null;
    }

    /**
     * Get gallery images (all images from gallery collection).
     */
    public function getGalleryImagesAttribute(): array
    {
        return $this->getMediaData('gallery');
    }

    /**
     * Get all images (from both images and gallery collections).
     */
    public function getAllImagesAttribute(): array
    {
        return $this->getMediaData();
    }
}
