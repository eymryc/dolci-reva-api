# 📚 Documentation API Dolcireva v2.0

## 🌐 Accès à la documentation Swagger

La documentation interactive de l'API est disponible à l'adresse suivante :

**🔗 [http://localhost:8000/api/documentation](http://localhost:8000/api/documentation)**

## 📋 Vue d'ensemble

L'API Dolcireva v2.0 est une API REST complète pour la gestion et la réservation d'établissements :

- 🏨 **Hôtels** et chambres
- 🏠 **Résidences** 
- 🍽️ **Restaurants** et tables
- 🍸 **Lounges** et tables
- 🎵 **Night Clubs** et zones

## 🔐 Authentification

L'API utilise l'authentification par token Bearer (Laravel Sanctum).

### Connexion
```http
POST /api/auth/login
Content-Type: application/json

{
    "email": "user@example.com",
    "password": "password123"
}
```

### Réponse
```json
{
    "status": 200,
    "success": true,
    "message": "Login successful",
    "data": {
        "user": {
            "id": 1,
            "first_name": "John",
            "last_name": "Doe",
            "email": "user@example.com",
            "type": "CUSTOMER"
        },
        "token": "1|abcdef123456..."
    }
}
```

## 🌍 Endpoints Publics

Ces endpoints sont accessibles sans authentification :

### Restaurants
```http
GET /api/public/restaurants
```

### Lounges
```http
GET /api/public/lounges
```

### Night Clubs
```http
GET /api/public/night-clubs
```

### Hôtels
```http
GET /api/public/hotels
```

### Résidences
```http
GET /api/public/residences
```

## 🔒 Endpoints Authentifiés

Tous les endpoints suivants nécessitent un token d'authentification dans l'en-tête :

```http
Authorization: Bearer {token}
```

## 🏨 Gestion des Hôtels

### CRUD Hôtels
- `GET /api/hotels` - Liste des hôtels
- `POST /api/hotels` - Créer un hôtel
- `GET /api/hotels/{id}` - Détails d'un hôtel
- `PUT /api/hotels/{id}` - Modifier un hôtel
- `DELETE /api/hotels/{id}` - Supprimer un hôtel

### Gestion des Chambres
- `GET /api/hotels/{hotel}/rooms` - Chambres d'un hôtel
- `POST /api/hotels/{hotel}/rooms` - Créer une chambre
- `GET /api/hotels/{hotel}/rooms/{room}` - Détails d'une chambre
- `PUT /api/hotels/{hotel}/rooms/{room}` - Modifier une chambre
- `DELETE /api/hotels/{hotel}/rooms/{room}` - Supprimer une chambre

## 🏠 Gestion des Résidences

### CRUD Résidences
- `GET /api/residences` - Liste des résidences
- `POST /api/residences` - Créer une résidence
- `GET /api/residences/{id}` - Détails d'une résidence
- `PUT /api/residences/{id}` - Modifier une résidence
- `DELETE /api/residences/{id}` - Supprimer une résidence

### Vérification de disponibilité
- `GET /api/residences/{id}/availability` - Vérifier la disponibilité

## 🍽️ Gestion des Restaurants

### CRUD Restaurants
- `GET /api/restaurants` - Liste des restaurants
- `POST /api/restaurants` - Créer un restaurant
- `GET /api/restaurants/{id}` - Détails d'un restaurant
- `PUT /api/restaurants/{id}` - Modifier un restaurant
- `DELETE /api/restaurants/{id}` - Supprimer un restaurant

### Gestion des Tables
- `GET /api/restaurants/{restaurant}/available-tables` - Tables disponibles
- `GET /api/restaurants/{restaurant}/time-slots` - Créneaux horaires disponibles

## 🍸 Gestion des Lounges

### CRUD Lounges
- `GET /api/lounges` - Liste des lounges
- `POST /api/lounges` - Créer un lounge
- `GET /api/lounges/{id}` - Détails d'un lounge
- `PUT /api/lounges/{id}` - Modifier un lounge
- `DELETE /api/lounges/{id}` - Supprimer un lounge

### Gestion des Tables
- `GET /api/lounges/{lounge}/available-tables` - Tables disponibles
- `GET /api/lounges/{lounge}/recommended-tables` - Tables recommandées
- `GET /api/lounges/{lounge}/time-slots` - Créneaux horaires disponibles

## 🎵 Gestion des Night Clubs

### CRUD Night Clubs
- `GET /api/night-clubs` - Liste des night clubs
- `POST /api/night-clubs` - Créer un night club
- `GET /api/night-clubs/{id}` - Détails d'un night club
- `PUT /api/night-clubs/{id}` - Modifier un night club
- `DELETE /api/night-clubs/{id}` - Supprimer un night club

### Gestion des Zones
- `GET /api/night-clubs/{nightClub}/available-areas` - Zones disponibles
- `GET /api/night-clubs/{nightClub}/recommended-areas` - Zones recommandées
- `GET /api/night-clubs/{nightClub}/time-slots` - Créneaux horaires disponibles
- `GET /api/night-clubs/age/{ageRestriction}` - Night clubs par restriction d'âge

## 📅 Gestion des Réservations

### CRUD Réservations
- `GET /api/bookings` - Liste des réservations
- `GET /api/bookings/{id}` - Détails d'une réservation
- `DELETE /api/bookings/{id}` - Supprimer une réservation

### Réservations Spécifiques
- `POST /api/residences/{residence}/book` - Réserver une résidence
- `POST /api/hotels/{hotel}/book` - Réserver un hôtel
- `POST /api/restaurants/{restaurant}/book` - Réserver une table de restaurant
- `POST /api/lounges/{lounge}/book` - Réserver une table de lounge
- `POST /api/night-clubs/{nightClub}/book` - Réserver une zone de night club

### Gestion des Réservations
- `PATCH /api/bookings/{booking}/confirm` - Confirmer une réservation
- `PATCH /api/bookings/{booking}/cancel` - Annuler une réservation
- `PATCH /api/bookings/{booking}/complete` - Marquer comme terminée

## 🖼️ Gestion des Médias

### Upload et Gestion
- `POST /api/media/upload` - Uploader un fichier
- `GET /api/media/get` - Récupérer les médias
- `DELETE /api/media/{media}` - Supprimer un média
- `DELETE /api/media/clear-collection` - Vider une collection

## ⚙️ Gestion des Équipements

### CRUD Amenities
- `GET /api/amenities` - Liste des équipements
- `POST /api/amenities` - Créer un équipement
- `GET /api/amenities/{id}` - Détails d'un équipement
- `PUT /api/amenities/{id}` - Modifier un équipement
- `DELETE /api/amenities/{id}` - Supprimer un équipement

## 💰 Gestion Financière

### Wallets
- `GET /api/wallets` - Liste des portefeuilles
- `POST /api/wallets` - Créer un portefeuille
- `GET /api/wallets/{id}` - Détails d'un portefeuille
- `PUT /api/wallets/{id}` - Modifier un portefeuille
- `DELETE /api/wallets/{id}` - Supprimer un portefeuille

### Transactions
- `GET /api/wallet_transactions` - Liste des transactions
- `POST /api/wallet_transactions` - Créer une transaction
- `GET /api/wallet_transactions/{id}` - Détails d'une transaction
- `PUT /api/wallet_transactions/{id}` - Modifier une transaction
- `DELETE /api/wallet_transactions/{id}` - Supprimer une transaction

### Retraits
- `GET /api/withdrawals` - Liste des retraits
- `POST /api/withdrawals` - Créer un retrait
- `GET /api/withdrawals/{id}` - Détails d'un retrait
- `PUT /api/withdrawals/{id}` - Modifier un retrait
- `DELETE /api/withdrawals/{id}` - Supprimer un retrait

### Commissions
- `GET /api/commissions` - Liste des commissions
- `POST /api/commissions` - Créer une commission
- `GET /api/commissions/{id}` - Détails d'une commission
- `PUT /api/commissions/{id}` - Modifier une commission
- `DELETE /api/commissions/{id}` - Supprimer une commission

## 📊 Codes de Statut

- `200` - Succès
- `201` - Créé avec succès
- `400` - Requête invalide
- `401` - Non authentifié
- `403` - Non autorisé
- `404` - Non trouvé
- `422` - Erreur de validation
- `500` - Erreur serveur

## 🔧 Exemples d'Utilisation

### Créer une réservation de restaurant

```http
POST /api/restaurants/1/book
Authorization: Bearer {token}
Content-Type: application/json

{
    "start_date": "2024-01-15 19:00:00",
    "end_date": "2024-01-15 21:00:00",
    "guests": 4,
    "notes": "Table près de la fenêtre",
    "restaurant_table_ids": [1, 2]
}
```

### Réserver une zone de night club

```http
POST /api/night-clubs/1/book
Authorization: Bearer {token}
Content-Type: application/json

{
    "start_date": "2024-01-15 23:00:00",
    "end_date": "2024-01-16 03:00:00",
    "guests": 8,
    "notes": "Zone VIP",
    "night_club_area_ids": [1]
}
```

## 🚀 Démarrage Rapide

1. **Installation** : `composer install`
2. **Configuration** : Copier `.env.example` vers `.env`
3. **Base de données** : `php artisan migrate`
4. **Serveur** : `php artisan serve`
5. **Documentation** : [http://localhost:8000/api/documentation](http://localhost:8000/api/documentation)

## 📝 Notes Importantes

- Tous les endpoints retournent du JSON
- Les dates sont au format ISO 8601
- Les prix sont en décimales (ex: 150.50)
- L'authentification est requise pour tous les endpoints sauf les endpoints publics
- Les images sont gérées via Laravel Media Library

## 🆘 Support

Pour toute question ou problème, contactez :
- Email : support@dolcireva.com
- Documentation : [http://localhost:8000/api/documentation](http://localhost:8000/api/documentation)
