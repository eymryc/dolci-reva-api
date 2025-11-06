# Repositories Media Library - Exemples d'utilisation

## 🎯 Vue d'ensemble

Les repositories `HotelRepository` et `HotelRoomRepository` ont été mis à jour pour utiliser Laravel Media Library au lieu de l'ancien système `FileService` et relation `images()`.

## 🏨 **HotelRepository - Nouvelles fonctionnalités**

### ✅ **Changements apportés :**
- **❌ Supprimé** : `FileService` et relation `images()`
- **✅ Ajouté** : Support complet de Laravel Media Library
- **✅ Mis à jour** : Toutes les méthodes pour utiliser `media` au lieu de `images`

### 🚀 **Méthodes disponibles :**

```php
// Méthodes de base (mises à jour)
$hotels = $repository->all();                    // Avec relations media
$hotels = $repository->paginate(15);             // Avec relations media
$hotel = $repository->getById(1);                // Avec relations media
$hotel = $repository->save($data);               // Upload automatique des images
$hotel = $repository->update($data, 1);          // Upload automatique des images

// Méthodes spécialisées
$hotels = $repository->getByOwner($ownerId);     // Avec relations media
$hotels = $repository->getAvailable();           // Avec relations media
$hotels = $repository->search($criteria);        // Avec relations media

// Nouvelles méthodes Media Library
$media = $repository->addMedia($hotelId, $file, 'gallery');
$repository->clearMediaCollection($hotelId, 'gallery');
$hotel = $repository->getWithMedia($hotelId);
```

### 📋 **Exemple d'utilisation :**

```php
// Créer un hôtel avec images
$data = [
    'name' => 'Hôtel de Luxe',
    'description' => 'Magnifique hôtel...',
    'address' => '123 Avenue des Champs-Élysées',
    'city' => 'Paris',
    'country' => 'France',
    'star_rating' => 5,
    'images' => [
        $request->file('main_image'),    // → Collection 'images'
        $request->file('gallery_1'),     // → Collection 'gallery'
        $request->file('gallery_2'),     // → Collection 'gallery'
    ],
    'amenities' => [1, 2, 3]
];

$hotel = $repository->save($data);

// URLs automatiques via le trait HasMediaTrait
$mainImage = $hotel->main_image_url;
$thumbUrl = $hotel->main_image_thumb_url;
$gallery = $hotel->gallery_images;
$allImages = $hotel->all_images;
```

## 🏨 **HotelRoomRepository - Nouvelles fonctionnalités**

### ✅ **Changements apportés :**
- **❌ Supprimé** : `FileService` et relation `images()`
- **✅ Ajouté** : Support complet de Laravel Media Library
- **✅ Mis à jour** : Toutes les méthodes pour utiliser `media` au lieu de `images`

### 🚀 **Méthodes disponibles :**

```php
// Méthodes de base (mises à jour)
$rooms = $repository->all();                     // Avec relations media
$rooms = $repository->paginate(15);              // Avec relations media
$room = $repository->getById(1);                 // Avec relations media
$rooms = $repository->getByHotelId($hotelId);    // Avec relations media
$room = $repository->save($data);                // Upload automatique des images
$room = $repository->update($data, 1);           // Upload automatique des images

// Nouvelles méthodes Media Library
$media = $repository->addMedia($roomId, $file, 'gallery');
$repository->clearMediaCollection($roomId, 'gallery');
$room = $repository->getWithMedia($roomId);
```

### 📋 **Exemple d'utilisation :**

```php
// Créer une chambre d'hôtel avec images
$data = [
    'hotel_id' => 1,
    'room_number' => '101',
    'name' => 'Suite Présidentielle',
    'description' => 'Luxueuse suite...',
    'type' => 'SUITE',
    'max_guests' => 4,
    'bedrooms' => 2,
    'bathrooms' => 2,
    'price' => 500.00,
    'standing' => 'LUXURY',
    'images' => [
        $request->file('main_image'),    // → Collection 'images'
        $request->file('gallery_1'),     // → Collection 'gallery'
        $request->file('gallery_2'),     // → Collection 'gallery'
    ],
    'amenities' => [1, 2, 3]
];

$room = $repository->save($data);

// URLs automatiques via le trait HasMediaTrait
$mainImage = $room->main_image_url;
$thumbUrl = $room->main_image_thumb_url;
$gallery = $room->gallery_images;
$allImages = $room->all_images;
```

## 🎨 **Collections intelligentes**

### **Logique des collections :**
- **Première image** → Collection `images` (image principale)
- **Images suivantes** → Collection `gallery` (galerie)

### **Conversions automatiques :**
- **Thumb** : 300x200px (miniature)
- **Medium** : 800x600px (moyenne)
- **Large** : 1200x800px (grande)

## 🔧 **API Endpoints**

### **Upload via API :**
```bash
# Upload pour un hôtel
POST /api/media/upload
{
    "model_type": "App\\Models\\Hotel",
    "model_id": 1,
    "collection": "gallery",
    "file": [fichier_image]
}

# Upload pour une chambre
POST /api/media/upload
{
    "model_type": "App\\Models\\HotelRoom",
    "model_id": 1,
    "collection": "gallery",
    "file": [fichier_image]
}
```

### **Récupérer les médias :**
```bash
# Récupérer toutes les images d'un hôtel
GET /api/media/get?model_type=App\\Models\\Hotel&model_id=1

# Récupérer la galerie d'une chambre
GET /api/media/get?model_type=App\\Models\\HotelRoom&model_id=1&collection=gallery
```

## 📊 **Avantages du nouveau système :**

1. **✅ Gestion automatique** des conversions d'images
2. **✅ Collections organisées** (images principales vs galerie)
3. **✅ URLs optimisées** avec différentes tailles
4. **✅ API dédiée** pour la gestion des médias
5. **✅ Trait réutilisable** pour tous les modèles
6. **✅ Stockage flexible** (local, S3, etc.)
7. **✅ Métadonnées complètes** des fichiers
8. **✅ Performance améliorée** avec eager loading

## 🎯 **Utilisation dans les Controllers :**

```php
// HotelController.php
public function store(HotelRequest $request)
{
    $data = $request->validated();
    $hotel = $this->hotelService->save($data);
    
    return response()->json([
        'status' => Response::HTTP_CREATED,
        'success' => true,
        'message' => 'Hotel created successfully',
        'data' => new HotelResource($hotel->load('owner', 'hotelRooms', 'media', 'amenities'))
    ], Response::HTTP_CREATED);
}
```

## 🔄 **Migration depuis l'ancien système :**

Si vous avez des données existantes avec l'ancien système `images()`, vous pouvez créer une commande de migration :

```php
// Commande Artisan pour migrer les anciennes images
php artisan make:command MigrateHotelImages
php artisan make:command MigrateHotelRoomImages
```

Les repositories sont maintenant modernisés et prêts à utiliser le nouveau système Media Library !
