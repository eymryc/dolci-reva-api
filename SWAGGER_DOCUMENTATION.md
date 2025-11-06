# 📖 Documentation Swagger API Dolcireva

## 🎯 Vue d'ensemble

Cette documentation décrit l'API REST Dolcireva v2.0 pour la gestion et la réservation d'établissements. L'API est entièrement documentée avec Swagger/OpenAPI 3.0.

## 🔗 Accès à la Documentation

### Interface Swagger UI
**URL :** [http://localhost:8000/api/documentation](http://localhost:8000/api/documentation)

### Fichier JSON OpenAPI
**URL :** [http://localhost:8000/api/docs](http://localhost:8000/api/docs)

## 🏗️ Structure de l'API

### Endpoints Publics (Sans Authentification)
- `GET /api/public/restaurants` - Liste des restaurants
- `GET /api/public/lounges` - Liste des lounges  
- `GET /api/public/night-clubs` - Liste des night clubs
- `GET /api/public/hotels` - Liste des hôtels
- `GET /api/public/residences` - Liste des résidences

### Endpoints Authentifiés (Token Bearer Requis)
- **Authentification** : `/api/auth/*`
- **Hôtels** : `/api/hotels/*`
- **Résidences** : `/api/residences/*`
- **Restaurants** : `/api/restaurants/*`
- **Lounges** : `/api/lounges/*`
- **Night Clubs** : `/api/night-clubs/*`
- **Réservations** : `/api/bookings/*`
- **Médias** : `/api/media/*`
- **Équipements** : `/api/amenities/*`
- **Finance** : `/api/wallets/*`, `/api/wallet_transactions/*`, `/api/withdrawals/*`, `/api/commissions/*`

## 📋 Modèles de Données

### User
```json
{
  "id": 1,
  "first_name": "John",
  "last_name": "Doe", 
  "email": "john@example.com",
  "phone": "+33123456789",
  "type": "CUSTOMER",
  "email_verified_at": "2024-01-01T00:00:00Z",
  "created_at": "2024-01-01T00:00:00Z",
  "updated_at": "2024-01-01T00:00:00Z"
}
```

### Restaurant
```json
{
  "id": 1,
  "owner_id": 1,
  "name": "Restaurant Le Gourmet",
  "description": "Restaurant gastronomique au cœur de la ville",
  "address": "123 Rue de la Paix",
  "city": "Paris",
  "country": "France",
  "opening_hours": {
    "monday": {
      "open": "12:00",
      "close": "22:00"
    }
  },
  "latitude": 48.8566,
  "longitude": 2.3522,
  "is_active": true,
  "tables": [...],
  "amenities": [...],
  "owner": {...}
}
```

### Booking
```json
{
  "id": 1,
  "customer_id": 1,
  "owner_id": 2,
  "bookable_type": "App\\Models\\Restaurant",
  "bookable_id": 1,
  "start_date": "2024-01-15T19:00:00Z",
  "end_date": "2024-01-15T21:00:00Z",
  "guests": 4,
  "booking_reference": "REST-20240115-001",
  "total_price": 120.00,
  "status": "CONFIRME",
  "payment_status": "PAYE",
  "notes": "Table près de la fenêtre"
}
```

## 🔐 Authentification

### Connexion
```http
POST /api/auth/login
Content-Type: application/json

{
  "email": "user@example.com",
  "password": "password123"
}
```

### Utilisation du Token
```http
Authorization: Bearer {token}
```

## 📊 Codes de Réponse

| Code | Description |
|------|-------------|
| 200 | Succès |
| 201 | Créé avec succès |
| 400 | Requête invalide |
| 401 | Non authentifié |
| 403 | Non autorisé |
| 404 | Non trouvé |
| 422 | Erreur de validation |
| 500 | Erreur serveur |

## 🎨 Interface Swagger

L'interface Swagger fournit :

- **Documentation interactive** - Testez les endpoints directement
- **Modèles de données** - Schémas complets des objets
- **Authentification intégrée** - Testez avec votre token
- **Exemples de requêtes** - Copiez-collez les exemples
- **Codes de réponse** - Documentation complète des réponses

## 🚀 Utilisation de l'Interface

1. **Ouvrez** [http://localhost:8000/api/documentation](http://localhost:8000/api/documentation)
2. **Authentifiez-vous** avec le bouton "Authorize" en haut à droite
3. **Explorez** les endpoints par catégorie
4. **Testez** les requêtes directement dans l'interface
5. **Consultez** les modèles de données dans la section "Schemas"

## 🔧 Configuration

### Génération de la Documentation
```bash
php artisan l5-swagger:generate
```

### Fichiers de Configuration
- `config/l5-swagger.php` - Configuration Swagger
- `storage/api-docs/api-docs.json` - Fichier JSON généré

### Annotations
Les annotations Swagger sont définies dans :
- `app/Http/Controllers/API/SwaggerController.php` - Configuration principale
- `app/Http/Controllers/API/SwaggerModels.php` - Modèles de données
- Chaque contrôleur contient ses annotations spécifiques

## 📝 Exemples d'Annotations

### Endpoint Simple
```php
/**
 * @OA\Get(
 *     path="/public/restaurants",
 *     summary="Liste des restaurants (public)",
 *     description="Récupère la liste de tous les restaurants disponibles",
 *     tags={"Public", "Restaurants"},
 *     @OA\Response(
 *         response=200,
 *         description="Liste des restaurants récupérée avec succès",
 *         @OA\JsonContent(
 *             @OA\Property(property="data", type="array",
 *                 @OA\Items(ref="#/components/schemas/Restaurant")
 *             )
 *         )
 *     )
 * )
 */
```

### Endpoint avec Authentification
```php
/**
 * @OA\Post(
 *     path="/restaurants",
 *     summary="Créer un restaurant",
 *     security={{"bearerAuth": {}}},
 *     tags={"Restaurants"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(ref="#/components/schemas/Restaurant")
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Restaurant créé avec succès",
 *         @OA\JsonContent(ref="#/components/schemas/Restaurant")
 *     )
 * )
 */
```

## 🎯 Bonnes Pratiques

1. **Toujours utiliser l'interface Swagger** pour tester les endpoints
2. **Consulter les modèles** avant de faire des requêtes
3. **Utiliser l'authentification** pour les endpoints protégés
4. **Vérifier les codes de réponse** pour comprendre les erreurs
5. **Consulter les exemples** fournis dans la documentation

## 🆘 Support

- **Documentation complète** : [API_DOCUMENTATION.md](./API_DOCUMENTATION.md)
- **Interface Swagger** : [http://localhost:8000/api/documentation](http://localhost:8000/api/documentation)
- **Support technique** : support@dolcireva.com
