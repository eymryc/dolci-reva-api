# 🏠 Logique Property/Room

## 📋 **Vue d'ensemble**

Cette API gère deux types de propriétaires avec des besoins différents :

### 🏢 **Cas d'usage 1 : Propriétaire de studio simple**
- **Property** : Le studio lui-même
- **Room** : Une seule room (le studio)
- **Exemple** : "Studio Cocody" → 1 room

### 🏨 **Cas d'usage 2 : Propriétaire de complexe résidentiel**
- **Property** : Le complexe/bâtiment
- **Room** : Plusieurs rooms (chambres, suites, etc.)
- **Exemple** : "Résidence Les Palmiers" → 50 rooms

## 🗂️ **Structure des données**

### **Property (Bâtiment/Complexe)**
```json
{
  "id": 1,
  "name": "Résidence Les Palmiers",
  "type": "RESIDENCE",
  "description": "Complexe résidentiel de luxe",
  "address": "Abidjan, Cocody",
  "owner_id": 1,
  "category_id": 1,
  "tagline": "Votre confort, notre priorité",
  "legal_name": "SARL Les Palmiers",
  "brand": "Palmiers Group"
}
```

### **Room (Unité individuelle)**
```json
{
  "id": 1,
  "property_id": 1,
  "name": "Suite 201",
  "type": "DOUBLE",
  "standing": "DELUXE",
  "max_guests": 2,
  "price": 12000,
  "is_available": true
}
```

## 🔄 **Logique de réservation**

### **Pour un studio simple :**
1. Créer une Property (type: STUDIO)
2. Créer automatiquement une Room associée
3. Les réservations se font sur la Room

### **Pour un complexe :**
1. Créer une Property (type: RESIDENCE/HOTEL)
2. Créer plusieurs Rooms selon les besoins
3. Les réservations se font sur les Rooms individuelles

## 📊 **Types de Property**

- **STUDIO** : Studio simple
- **MAISON** : Maison individuelle
- **APPARTEMENT** : Appartement
- **HOTEL** : Hôtel
- **RESIDENCE** : Complexe résidentiel

## 🛏️ **Types de Room**

- **SIMPLE** : Chambre simple
- **DOUBLE** : Chambre double
- **TWIN** : Chambre avec lits jumeaux
- **TRIPLE** : Chambre triple
- **QUAD** : Chambre quadruple

## 🏆 **Standings de Room**

- **STANDARD** : Standard
- **DELUXE** : Deluxe
- **EXECUTIVE** : Executive
- **SUITE** : Suite
- **SUITE_JUNIOR** : Suite Junior
- **SUITE_EXECUTIVE** : Suite Executive
- **SUITE_PRESIDENTIELLE** : Suite Présidentielle

## 🔗 **Relations**

```
Property (1) → (N) Room
Property (N) → (N) Amenity (via amenityables)
Property (1) → (1) User (owner)
Property (1) → (1) Category
Room (N) → (N) Amenity (via amenityables)
Room (1) → (N) Booking
```

## 💡 **Avantages de cette approche**

1. **Flexibilité** : Gère les deux cas d'usage
2. **Évolutivité** : Facile d'ajouter de nouvelles rooms
3. **Gestion centralisée** : Les amenities sont partagées au niveau Property
4. **Réservations précises** : Réservation par room, pas par property
5. **Reporting** : Statistiques par property et par room
