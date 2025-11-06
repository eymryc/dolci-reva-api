# Laravel Media Library - Guide d'utilisation

## 🎯 Vue d'ensemble

Laravel Media Library a été intégré dans le projet pour gérer facilement les fichiers multimédias (images, vidéos, documents) associés aux modèles Hotel, Residence et HotelRoom.

## 📁 Collections de médias

Chaque modèle supporte deux collections :
- **`images`** : Image principale (single file)
- **`gallery`** : Galerie d'images (multiple files)

## 🔧 Conversions d'images

Les images sont automatiquement converties en 3 tailles :
- **`thumb`** : 300x200px (miniature)
- **`medium`** : 800x600px (moyenne)
- **`large`** : 1200x800px (grande)

## 🚀 Utilisation

### 1. Upload d'un fichier

```bash
POST /api/media/upload
Content-Type: multipart/form-data

{
    "model_type": "App\\Models\\Hotel",
    "model_id": 1,
    "collection": "images",
    "file": [fichier_image]
}
```

### 2. Récupérer les médias

```bash
GET /api/media/get?model_type=App\\Models\\Hotel&model_id=1&collection=images
```

### 3. Supprimer un média

```bash
DELETE /api/media/{media_id}
```

### 4. Vider une collection

```bash
DELETE /api/media/clear-collection
Content-Type: application/json

{
    "model_type": "App\\Models\\Hotel",
    "model_id": 1,
    "collection": "gallery"
}
```

## 💻 Utilisation dans le code

### Dans un modèle

```php
// Ajouter un fichier
$hotel->addMediaFromRequest('photo')->toMediaCollection('images');

// Ajouter plusieurs fichiers
$hotel->addMediaFromRequest('photos')->toMediaCollection('gallery');

// Récupérer les médias
$images = $hotel->getMedia('images');
$gallery = $hotel->getMedia('gallery');

// URLs des conversions
$thumbUrl = $hotel->getFirstMediaUrl('images', 'thumb');
$mediumUrl = $hotel->getFirstMediaUrl('images', 'medium');
$largeUrl = $hotel->getFirstMediaUrl('images', 'large');
```

### Dans une Resource

```php
// Les attributs suivants sont automatiquement disponibles :
'main_image_url' => $this->main_image_url,
'main_image_thumb_url' => $this->main_image_thumb_url,
'gallery_images' => $this->gallery_images,
'all_images' => $this->all_images,
```

## 📋 Exemples d'API

### Upload d'image principale pour un hôtel

```bash
curl -X POST "http://localhost:8000/api/media/upload" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -F "model_type=App\\Models\\Hotel" \
  -F "model_id=1" \
  -F "collection=images" \
  -F "file=@hotel_main.jpg"
```

### Upload d'images de galerie

```bash
curl -X POST "http://localhost:8000/api/media/upload" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -F "model_type=App\\Models\\Hotel" \
  -F "model_id=1" \
  -F "collection=gallery" \
  -F "file=@hotel_gallery_1.jpg"
```

### Récupérer toutes les images d'un hôtel

```bash
curl -X GET "http://localhost:8000/api/media/get?model_type=App\\Models\\Hotel&model_id=1" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

## 🎨 Formats supportés

- **Images** : JPEG, PNG, GIF, WebP
- **Taille maximale** : 10MB par fichier

## 📂 Stockage

Les fichiers sont stockés dans :
- **Développement** : `storage/app/public/media`
- **Production** : Configurable (S3, etc.)

## 🔄 Commandes utiles

```bash
# Générer les conversions manquantes
php artisan media-library:regenerate

# Nettoyer les médias orphelins
php artisan media-library:clear

# Vider le cache des conversions
php artisan media-library:clear
```

## 🛠️ Configuration

Le fichier de configuration se trouve dans `config/media-library.php` et permet de personnaliser :
- Le disque de stockage
- Les conversions d'images
- Les collections
- Les types de fichiers acceptés
