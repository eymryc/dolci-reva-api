# Residence Media Library - Exemples d'utilisation

## 🎯 Vue d'ensemble

Le `ResidenceRepository` a été mis à jour pour utiliser Laravel Media Library au lieu de l'ancien système `FileService` et relation `images()`.

## 🔄 **Changements apportés :**

### ✅ **Avant (ancien système) :**
```php
// Utilisait FileService et relation images()
$fileService = new FileService();
$paths = $fileService->uploadMultiple($images, $folderName);
$residence->images()->create(['path' => "storage/" . $path]);
```

### ✅ **Maintenant (Media Library) :**
```php
// Utilise Media Library avec collections
$residence->addMediaFromRequest("images.{$index}")
    ->toMediaCollection($collection);
```

## 🚀 **Nouvelles fonctionnalités :**

### 1. **Collections automatiques :**
- **Première image** → Collection `images` (image principale)
- **Images suivantes** → Collection `gallery` (galerie)

### 2. **Conversions automatiques :**
- **Thumb** : 300x200px
- **Medium** : 800x600px  
- **Large** : 1200x800px

### 3. **Nouvelles méthodes :**
```php
// Ajouter un média
$media = $repository->addMedia($residenceId, $file, 'gallery');

// Vider une collection
$repository->clearMediaCollection($residenceId, 'gallery');

// Récupérer avec médias
$residence = $repository->getWithMedia($residenceId);
```

## 📋 **Exemples d'utilisation :**

### **Créer une résidence avec images :**
```php
$data = [
    'name' => 'Villa de luxe',
    'description' => 'Magnifique villa...',
    'address' => '123 Rue de la Paix',
    'city' => 'Paris',
    'country' => 'France',
    'price' => 150.00,
    'images' => [
        $request->file('main_image'),    // → Collection 'images'
        $request->file('gallery_1'),     // → Collection 'gallery'
        $request->file('gallery_2'),     // → Collection 'gallery'
    ],
    'amenities' => [1, 2, 3]
];

$residence = $repository->save($data);
```

### **Récupérer les URLs d'images :**
```php
$residence = $repository->getWithMedia($id);

// URLs automatiques via le trait HasMediaTrait
$mainImage = $residence->main_image_url;
$thumbUrl = $residence->main_image_thumb_url;
$gallery = $residence->gallery_images;
$allImages = $residence->all_images;
```

### **Ajouter des images à une résidence existante :**
```php
// Via le repository
$media = $repository->addMedia($residenceId, $file, 'gallery');

// Ou directement sur le modèle
$residence = Residence::find($id);
$media = $residence->addMediaFromRequest('photo')
    ->toMediaCollection('gallery');
```

### **Supprimer des images :**
```php
// Supprimer une collection complète
$repository->clearMediaCollection($residenceId, 'gallery');

// Supprimer un média spécifique
$media = Media::find($mediaId);
$media->delete();
```

## 🔧 **API Endpoints :**

### **Upload via API :**
```bash
POST /api/media/upload
{
    "model_type": "App\\Models\\Residence",
    "model_id": 1,
    "collection": "gallery",
    "file": [fichier_image]
}
```

### **Récupérer les médias :**
```bash
GET /api/media/get?model_type=App\\Models\\Residence&model_id=1&collection=gallery
```

## 📊 **Avantages du nouveau système :**

1. **✅ Gestion automatique** des conversions d'images
2. **✅ Collections organisées** (images principales vs galerie)
3. **✅ URLs optimisées** avec différentes tailles
4. **✅ API dédiée** pour la gestion des médias
5. **✅ Trait réutilisable** pour tous les modèles
6. **✅ Stockage flexible** (local, S3, etc.)
7. **✅ Métadonnées complètes** des fichiers

## 🎨 **Dans les Resources :**

```php
// ResidenceResource.php
public function toArray($request)
{
    return [
        'id' => $this->id,
        'name' => $this->name,
        // ... autres champs
        
        // Médias automatiques
        'main_image_url' => $this->main_image_url,
        'main_image_thumb_url' => $this->main_image_thumb_url,
        'gallery_images' => $this->gallery_images,
        'all_images' => $this->all_images,
    ];
}
```

## 🔄 **Migration depuis l'ancien système :**

Si vous avez des données existantes avec l'ancien système `images()`, vous pouvez créer une commande de migration :

```php
// Commande Artisan pour migrer les anciennes images
php artisan make:command MigrateResidenceImages
```

Le nouveau système est maintenant prêt et plus puissant que l'ancien !
