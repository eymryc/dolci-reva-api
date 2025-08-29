<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FileService
{
   /**
    * Upload un fichier unique.
    *
    * @param UploadedFile $file
    * @param string $directory
    * @return string Le chemin du fichier stocké
    */
   public function upload(UploadedFile $file, string $directory = 'uploads'): string
   {
      return $file->store($directory, 'public');
   }

   /**
    * Upload plusieurs fichiers.
    *
    * @param array $files
    * @param string $directory
    * @return array Liste des chemins stockés
    */
   public function uploadMultiple(array $files, string $directory = 'uploads'): array
   {
      $paths = [];
      foreach ($files as $file) {
         if ($file instanceof UploadedFile) {
            $paths[] = $this->upload($file, $directory);
         }
      }
      return $paths;
   }

   /**
    * Remplace un fichier existant par un nouveau.
    *
    * @param UploadedFile $file
    * @param string|null $oldPath
    * @param string $directory
    * @return string Nouveau chemin
    */
   public function update(UploadedFile $file, ?string $oldPath, string $directory = 'uploads'): string
   {
      if ($oldPath && Storage::disk('public')->exists($oldPath)) {
         Storage::disk('public')->delete($oldPath);
      }
      return $this->upload($file, $directory);
   }

   /**
    * Supprime un fichier.
    *
    * @param string $path
    * @return bool
    */
   public function delete(string $path): bool
   {
      return Storage::disk('public')->delete($path);
   }
}
