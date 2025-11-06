# 🔄 Refactorisation du BookingController

## 📋 **Résumé des changements**

Le `BookingController` a été refactorisé pour suivre la même structure et organisation que le `AmenityController`, avec une séparation claire des responsabilités entre le controller et le service.

## 🎯 **Structure adoptée (comme AmenityController)**

### **Controller simplifié :**
- ✅ **Responsabilité unique** : Gestion des requêtes HTTP et réponses
- ✅ **Délégation** : Toute la logique métier déléguée au service
- ✅ **Structure cohérente** : Même pattern que `AmenityController`
- ✅ **Gestion d'erreurs** : Try-catch uniforme avec messages d'erreur standardisés

### **Service enrichi :**
- ✅ **Logique métier** : Toutes les opérations complexes dans `BookingService`
- ✅ **Transactions** : Gestion des transactions DB dans le service
- ✅ **Validation** : Logique de validation métier dans le service
- ✅ **Méthodes utilitaires** : Calculs de prix, génération de références, etc.

## 🔧 **Méthodes refactorisées**

### **Méthodes de base (inchangées) :**
```php
public function index(): AnonymousResourceCollection
public function show(int $id): BookingResource
public function destroy(int $id): JsonResponse
```

### **Méthodes de réservation (refactorisées) :**

#### **1. bookResidence()**
```php
// AVANT : Logique complexe dans le controller
public function bookResidence(ResidenceBookingRequest $request, Residence $residence)
{
    // 30+ lignes de logique métier dans le controller
}

// APRÈS : Délégation au service
public function bookResidence(ResidenceBookingRequest $request, Residence $residence): BookingResource|JsonResponse
{
    $data = $request->validated();
    $data['customer_id'] = Auth::id();
    
    // Save the booking using the service
    $booking = $this->bookingService->saveResidenceBooking($data, $residence->id);
    
    // Set response (structure identique à AmenityController)
    $response = response()->json([...]);
    return $response;
}
```

#### **2. bookHotel()**
```php
// Même pattern que bookResidence()
public function bookHotel(HotelBookingRequest $request, Hotel $hotel): BookingResource|JsonResponse
{
    $data = $request->validated();
    $data['customer_id'] = Auth::id();
    
    $booking = $this->bookingService->saveHotelBooking($data, $hotel->id);
    
    $response = response()->json([...]);
    return $response;
}
```

#### **3. confirmBooking()**
```php
// AVANT : Logique de validation et mise à jour dans le controller
// APRÈS : Délégation complète au service
public function confirmBooking(ConfirmBookingRequest $request, Booking $booking): BookingResource|JsonResponse
{
    // Vérification d'autorisation uniquement
    if ($booking->owner_id !== Auth::id() && !$user->isAdmin()) {
        return response()->json([...], Response::HTTP_FORBIDDEN);
    }
    
    $data = $request->validated();
    $booking = $this->bookingService->confirmBooking($data, $booking->id);
    
    $response = response()->json([...]);
    return $response;
}
```

#### **4. cancelBooking() & completeBooking()**
```php
// Même pattern que confirmBooking()
// Vérification d'autorisation + délégation au service
```

## 🏗️ **BookingService enrichi**

### **Nouvelles méthodes ajoutées :**

```php
// Méthodes de création
public function saveResidenceBooking(array $data, int $residenceId): Booking
public function saveHotelBooking(array $data, int $hotelId): Booking

// Méthodes de gestion d'état
public function confirmBooking(array $data, int $bookingId): Booking
public function cancelBooking(array $data, int $bookingId): Booking
public function completeBooking(array $data, int $bookingId): Booking

// Méthodes utilitaires privées
private function generateBookingReference(): string
private function calculatePrice($residence, string $startDate, string $endDate): float
private function calculateHotelPrice($hotel, string $startDate, string $endDate): float
```

### **Logique métier déplacée :**
- ✅ **Génération de références** : `generateBookingReference()`
- ✅ **Calculs de prix** : `calculatePrice()`, `calculateHotelPrice()`
- ✅ **Validation métier** : Vérification des statuts, disponibilités
- ✅ **Gestion des relations** : Mise à jour des statuts de disponibilité
- ✅ **Transactions DB** : Gestion des rollbacks en cas d'erreur

## 📊 **Avantages de la refactorisation**

### **1. Séparation des responsabilités :**
- **Controller** : Gestion HTTP uniquement
- **Service** : Logique métier et accès aux données

### **2. Réutilisabilité :**
- Les méthodes du service peuvent être utilisées ailleurs
- Logique métier centralisée et testable

### **3. Maintenabilité :**
- Code plus lisible et organisé
- Structure cohérente avec le reste de l'application

### **4. Testabilité :**
- Service facilement mockable pour les tests
- Controller simplifié pour les tests d'intégration

### **5. Cohérence :**
- Même structure que `AmenityController`
- Patterns uniformes dans toute l'application

## 🔍 **Structure finale**

### **BookingController (simplifié) :**
```php
class BookingController extends Controller
{
    // Constructor + injection de dépendance
    public function __construct(BookingService $bookingService)
    
    // Méthodes CRUD de base
    public function index(): AnonymousResourceCollection
    public function show(int $id): BookingResource
    public function destroy(int $id): JsonResponse
    
    // Méthodes de réservation (délégation au service)
    public function bookResidence(...): BookingResource|JsonResponse
    public function bookHotel(...): BookingResource|JsonResponse
    public function confirmBooking(...): BookingResource|JsonResponse
    public function cancelBooking(...): BookingResource|JsonResponse
    public function completeBooking(...): BookingResource|JsonResponse
}
```

### **BookingService (enrichi) :**
```php
class BookingService
{
    // Méthodes CRUD de base
    public function getAll()
    public function getAllWithPagination(int $perPage = 15)
    public function getById(int $id)
    public function update(array $data, int $id)
    public function deleteById(int $id)
    
    // Nouvelles méthodes métier
    public function saveResidenceBooking(array $data, int $residenceId): Booking
    public function saveHotelBooking(array $data, int $hotelId): Booking
    public function confirmBooking(array $data, int $bookingId): Booking
    public function cancelBooking(array $data, int $bookingId): Booking
    public function completeBooking(array $data, int $bookingId): Booking
    
    // Méthodes utilitaires privées
    private function generateBookingReference(): string
    private function calculatePrice(...): float
    private function calculateHotelPrice(...): float
}
```

## ✅ **Résultat**

Le `BookingController` suit maintenant exactement la même structure que `AmenityController`, avec :
- **Controller simplifié** pour la gestion HTTP
- **Service enrichi** pour la logique métier
- **Patterns cohérents** dans toute l'application
- **Code maintenable** et testable
