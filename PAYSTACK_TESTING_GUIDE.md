# 🧪 Guide de Test Paystack

## ✅ Configuration Terminée

Les variables d'environnement Paystack sont configurées dans `.env` :
- `PAYSTACK_SECRET_KEY` : Clé secrète de test
- `PAYSTACK_PUBLIC_KEY` : Clé publique de test
- `PAYSTACK_MERCHANT_EMAIL` : Email du marchand
- `PAYSTACK_URL` : URL de l'API Paystack

## 🚀 Étapes de Test

### 1. Vérifier que le serveur fonctionne

```bash
php artisan serve
# ou si vous utilisez Herd, votre serveur est déjà lancé
```

### 2. Obtenir un token d'authentification

**Endpoint :** `POST /api/auth/login`

```bash
curl -X POST http://v2-dolcireva-api.test/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "votre@email.com",
    "password": "votre_mot_de_passe"
  }'
```

**Réponse :**
```json
{
  "success": true,
  "status": 200,
  "message": "Login successful",
  "token": "1|abcdef123456...",
  "type": "Bearer",
  "user": {...}
}
```

⚠️ **Important :** Copiez le token pour l'utiliser dans les prochaines étapes.

---

### 3. Initialiser un Paiement

**Endpoint :** `POST /api/payments/initialize`  
**Authentification :** Requise (Bearer Token)

#### Exemple 1 : Paiement simple (recharger le wallet)

```bash
curl -X POST http://v2-dolcireva-api.test/api/payments/initialize \
  -H "Authorization: Bearer VOTRE_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "email": "test@example.com",
    "amount": 5000,
    "currency": "XOF"
  }'
```

**Réponse attendue :**
```json
{
  "status": 200,
  "success": true,
  "message": "Payment initialized successfully",
  "data": {
    "authorization_url": "https://checkout.paystack.com/abc123...",
    "access_code": "abc123...",
    "reference": "TXN_1234567890_abc123",
    "public_key": "pk_test_..."
  }
}
```

#### Exemple 2 : Paiement pour une réservation

```bash
curl -X POST http://v2-dolcireva-api.test/api/payments/initialize \
  -H "Authorization: Bearer VOTRE_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "email": "test@example.com",
    "amount": 10000,
    "booking_id": 1,
    "currency": "XOF"
  }'
```

**Note :** Si `booking_id` est fourni, le montant sera automatiquement utilisé depuis la réservation.

---

### 4. Effectuer le Paiement

1. Copiez l'`authorization_url` de la réponse précédente
2. Ouvrez cette URL dans votre navigateur
3. Utilisez les **cartes de test Paystack** :
   - **Carte valide :** `4084084084084081`
   - **CVV :** N'importe quel nombre à 3 chiffres
   - **Date d'expiration :** N'importe quelle date future (ex: 12/25)
   - **PIN :** `0000` (pour Mastercard) ou `1234` (pour Visa)
   - **OTP :** `123456`

4. Complétez le paiement sur Paystack

---

### 5. Vérifier le Paiement

**Endpoint :** `POST /api/payments/verify`  
**Authentification :** Requise (Bearer Token)

```bash
curl -X POST http://v2-dolcireva-api.test/api/payments/verify \
  -H "Authorization: Bearer VOTRE_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "reference": "TXN_1234567890_abc123"
  }'
```

**Réponse en cas de succès :**
```json
{
  "status": 200,
  "success": true,
  "message": "Payment verified and wallet credited successfully",
  "data": {
    "reference": "TXN_1234567890_abc123",
    "amount": 5000,
    "status": "success",
    "wallet_balance": 5000,
    "transaction_data": {...}
  }
}
```

✅ **Résultat :** Le wallet de l'utilisateur est automatiquement crédité !

---

### 6. Vérifier le Wallet

**Endpoint :** `GET /api/wallets` ou `GET /api/wallets/{id}`

```bash
curl -X GET http://v2-dolcireva-api.test/api/wallets \
  -H "Authorization: Bearer VOTRE_TOKEN"
```

Vous devriez voir le nouveau solde dans le wallet.

---

## 🔔 Configuration du Webhook (Optionnel pour le moment)

Pour tester les webhooks en local, vous pouvez utiliser **ngrok** :

1. **Installer ngrok** (si ce n'est pas déjà fait)
2. **Créer un tunnel** :
   ```bash
   ngrok http 80  # ou le port de votre serveur
   ```
3. **Copier l'URL ngrok** (ex: `https://abc123.ngrok.io`)
4. **Configurer dans Paystack Dashboard** :
   - Allez dans **Settings** → **API Keys & Webhooks**
   - Ajoutez l'URL : `https://abc123.ngrok.io/api/payments/webhook`
   - Sélectionnez les événements : `charge.success`, `charge.failed`

---

## 🧪 Tests avec Postman

### Collection Postman

1. Créez une nouvelle collection "Dolcireva API"
2. Ajoutez les variables d'environnement :
   - `base_url` : `http://v2-dolcireva-api.test`
   - `token` : (sera rempli après login)

3. **Requête 1 : Login**
   - Method : `POST`
   - URL : `{{base_url}}/api/auth/login`
   - Body (raw JSON) :
     ```json
     {
       "email": "test@example.com",
       "password": "password123"
     }
     ```
   - **Test Script** (pour sauvegarder le token) :
     ```javascript
     if (pm.response.code === 200) {
         var jsonData = pm.response.json();
         pm.environment.set("token", jsonData.token);
     }
     ```

4. **Requête 2 : Initialize Payment**
   - Method : `POST`
   - URL : `{{base_url}}/api/payments/initialize`
   - Headers : `Authorization: Bearer {{token}}`
   - Body (raw JSON) :
     ```json
     {
       "email": "test@example.com",
       "amount": 5000,
       "currency": "XOF"
     }
     ```

5. **Requête 3 : Verify Payment**
   - Method : `POST`
   - URL : `{{base_url}}/api/payments/verify`
   - Headers : `Authorization: Bearer {{token}}`
   - Body (raw JSON) :
     ```json
     {
       "reference": "TXN_1234567890_abc123"
     }
     ```

---

## 📊 Vérifier les Transactions Wallet

**Endpoint :** `GET /api/wallet_transactions`

```bash
curl -X GET http://v2-dolcireva-api.test/api/wallet_transactions \
  -H "Authorization: Bearer VOTRE_TOKEN"
```

Vous devriez voir une transaction de type `CREDIT` avec la raison :
```
Paystack Payment - Reference: TXN_1234567890_abc123
```

---

## ⚠️ Cartes de Test Paystack

Paystack fournit des cartes de test pour simuler différents scénarios :

| Carte | Scénario |
|-------|----------|
| `4084084084084081` | Paiement réussi |
| `5060666666666666666` | Paiement échoué |
| `5078387855778456` | Paiement nécessitant OTP |

**CVV :** N'importe quel nombre à 3 chiffres  
**PIN :** `0000` ou `1234`  
**OTP :** `123456`

---

## 🐛 Dépannage

### Erreur : "Invalid API key"
- Vérifiez que les clés dans `.env` sont correctes
- Assurez-vous d'avoir exécuté `php artisan config:clear`

### Erreur : "Unauthenticated"
- Vérifiez que votre token est valide
- Le token expire après un certain temps, reconnectez-vous si nécessaire

### Le wallet n'est pas crédité
- Vérifiez que vous avez bien appelé `/api/payments/verify` après le paiement
- Vérifiez les logs Laravel : `storage/logs/laravel.log`
- Vérifiez que la transaction a bien le statut "success" dans Paystack

---

## ✅ Checklist de Test

- [ ] Les variables d'environnement sont configurées
- [ ] Le serveur Laravel fonctionne
- [ ] Je peux me connecter et obtenir un token
- [ ] Je peux initialiser un paiement
- [ ] Je peux effectuer le paiement sur Paystack
- [ ] Je peux vérifier le paiement
- [ ] Le wallet est crédité correctement
- [ ] Les transactions wallet sont enregistrées
- [ ] Le statut de la réservation est mis à jour (si applicable)

---

## 🎉 Prêt pour la Production

Une fois les tests terminés et validés :

1. **Remplacer les clés de test par les clés de production** dans `.env`
2. **Configurer le webhook** dans le dashboard Paystack avec votre URL de production
3. **Tester à nouveau** avec les clés de production
4. **Monitorer les logs** pour détecter d'éventuels problèmes

---

## 📚 Documentation Paystack

- [Documentation API Paystack](https://paystack.com/docs/api)
- [Cartes de Test Paystack](https://paystack.com/docs/payments/test-payments)
- [Webhooks Paystack](https://paystack.com/docs/payments/webhooks)

