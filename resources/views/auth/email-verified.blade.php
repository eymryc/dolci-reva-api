<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Vérifié - DOLCIREVA</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: #151e2e;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .container {
            max-width: 500px;
            width: 100%;
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            animation: slideUp 0.5s ease-out;
        }
        
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .header {
            background: #151e2e;
            padding: 40px 30px;
            text-align: center;
            color: #ffffff;
        }
        
        .success-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 20px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: scaleIn 0.5s ease-out 0.3s both;
        }
        
        @keyframes scaleIn {
            from {
                transform: scale(0);
            }
            to {
                transform: scale(1);
            }
        }
        
        .success-icon svg {
            width: 50px;
            height: 50px;
            stroke: #ffffff;
            stroke-width: 3;
            fill: none;
            stroke-linecap: round;
            stroke-linejoin: round;
        }
        
        .header h1 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 10px;
            letter-spacing: -0.5px;
        }
        
        .header .logo {
            font-size: 24px;
            font-weight: 800;
            opacity: 0.9;
        }
        
        .content {
            padding: 40px 30px;
            text-align: center;
        }
        
        .content h2 {
            font-size: 24px;
            color: #1a1a1a;
            margin-bottom: 15px;
            font-weight: 600;
        }
        
        .content p {
            font-size: 16px;
            color: #666666;
            line-height: 1.6;
            margin-bottom: 30px;
        }
        
        .button-container {
            margin-top: 30px;
        }
        
        .login-button {
            display: inline-block;
            padding: 16px 40px;
            background: #ff6a13;
            color: #ffffff;
            text-decoration: none;
            border-radius: 10px;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(255, 106, 19, 0.4);
        }
        
        .login-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 106, 19, 0.5);
        }
        
        .info-box {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-left: 4px solid #ff6a13;
            padding: 20px;
            margin-top: 30px;
            border-radius: 8px;
            text-align: left;
        }
        
        .info-box p {
            margin: 0;
            font-size: 14px;
            color: #555555;
        }
        
        .footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            color: #888888;
            font-size: 12px;
        }
        
        .confetti {
            position: fixed;
            width: 10px;
            height: 10px;
            background: #ff6a13;
            position: absolute;
            animation: confetti-fall 3s linear infinite;
        }
        
        @keyframes confetti-fall {
            to {
                transform: translateY(100vh) rotate(360deg);
                opacity: 0;
            }
        }
        
        @media only screen and (max-width: 600px) {
            .container {
                border-radius: 15px;
            }
            
            .header, .content {
                padding: 30px 20px;
            }
            
            .login-button {
                padding: 14px 30px;
                font-size: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            @if($success ?? false)
                <div class="success-icon">
                    <svg viewBox="0 0 24 24">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                </div>
                <div class="logo">DOLCIREVA</div>
                <h1>Email Vérifié !</h1>
            @else
                <div class="success-icon" style="background: rgba(255, 77, 77, 0.2);">
                    <svg viewBox="0 0 24 24" style="stroke: #ffffff;">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                </div>
                <div class="logo">DOLCIREVA</div>
                <h1>Erreur</h1>
            @endif
        </div>
        
        <!-- Content -->
        <div class="content">
            @if($success ?? false)
                <h2>Félicitations ! 🎉</h2>
                <p>{{ $message ?? 'Votre adresse email a été vérifiée avec succès. Votre compte est maintenant actif et vous pouvez profiter de tous nos services.' }}</p>
                
                <div class="button-container">
                    <a href="#" onclick="window.close(); return false;" class="login-button">
                        Continuer
                    </a>
                </div>
                
                <div class="info-box">
                    <p>
                        <strong>💡 Prochaine étape :</strong> Vous pouvez maintenant vous connecter à votre compte et commencer à utiliser nos services.
                    </p>
                </div>
            @else
                <h2 style="color: #dc3545;">Oops ! 😔</h2>
                <p style="color: #dc3545;">{{ $message ?? 'Une erreur est survenue lors de la vérification de votre email.' }}</p>
                
                <div class="button-container">
                    <a href="#" onclick="window.close(); return false;" class="login-button" style="background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);">
                        Fermer
                    </a>
                </div>
                
                <div class="info-box" style="border-left-color: #dc3545;">
                    <p>
                        <strong>💡 Que faire ?</strong> Si le problème persiste, contactez notre support ou demandez un nouvel email de vérification depuis votre application.
                    </p>
                </div>
            @endif
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <p><strong>DOLCIREVA</strong></p>
            <p>Votre plateforme de réservation de luxe</p>
            <p style="margin-top: 10px;">© {{ date('Y') }} DOLCIREVA. Tous droits réservés.</p>
        </div>
    </div>
    
    <script>
        // Animation de confettis
        function createConfetti() {
            const colors = ['#ff6a13', '#151e2e', '#ff8c42', '#2a3d5a'];
            for (let i = 0; i < 20; i++) {
                setTimeout(() => {
                    const confetti = document.createElement('div');
                    confetti.className = 'confetti';
                    confetti.style.left = Math.random() * 100 + '%';
                    confetti.style.background = colors[Math.floor(Math.random() * colors.length)];
                    confetti.style.animationDelay = Math.random() * 2 + 's';
                    confetti.style.animationDuration = (Math.random() * 2 + 2) + 's';
                    document.body.appendChild(confetti);
                    
                    setTimeout(() => {
                        confetti.remove();
                    }, 4000);
                }, i * 100);
            }
        }
        
        // Démarrer les confettis après un court délai
        setTimeout(createConfetti, 500);
    </script>
</body>
</html>

