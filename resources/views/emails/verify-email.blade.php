<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vérifiez votre email - DOLCIREVA</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: #f5f7fa;
            padding: 20px;
            line-height: 1.6;
        }
        
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .email-header {
            background: #151e2e;
            padding: 40px 30px;
            text-align: center;
            color: #ffffff;
        }
        
        .email-header h1 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 10px;
            letter-spacing: -0.5px;
        }
        
        .email-header .logo {
            font-size: 32px;
            font-weight: 800;
            margin-bottom: 10px;
        }
        
        .email-body {
            padding: 40px 30px;
            color: #333333;
        }
        
        .email-body h2 {
            font-size: 24px;
            color: #1a1a1a;
            margin-bottom: 20px;
            font-weight: 600;
        }
        
        .email-body p {
            font-size: 16px;
            color: #666666;
            margin-bottom: 20px;
        }
        
        .email-body .highlight {
            color: #ff6a13;
            font-weight: 600;
        }
        
        .button-container {
            text-align: center;
            margin: 30px 0;
        }
        
        .verify-button {
            display: inline-block;
            padding: 16px 40px;
            background: #ff6a13;
            color: #ffffff;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            transition: transform 0.2s, box-shadow 0.2s;
            box-shadow: 0 4px 12px rgba(255, 106, 19, 0.4);
        }
        
        .verify-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(255, 106, 19, 0.5);
        }
        
        .divider {
            height: 1px;
            background: linear-gradient(90deg, transparent, #e0e0e0, transparent);
            margin: 30px 0;
        }
        
        .info-box {
            background-color: #f8f9fa;
            border-left: 4px solid #ff6a13;
            padding: 15px 20px;
            margin: 25px 0;
            border-radius: 4px;
        }
        
        .info-box p {
            margin: 0;
            font-size: 14px;
            color: #555555;
        }
        
        .email-footer {
            background-color: #f8f9fa;
            padding: 30px;
            text-align: center;
            color: #888888;
            font-size: 14px;
        }
        
        .email-footer p {
            margin: 5px 0;
        }
        
        .email-footer .social-links {
            margin-top: 20px;
        }
        
        .email-footer .social-links a {
            color: #ff6a13;
            text-decoration: none;
            margin: 0 10px;
        }
        
        .link-alternative {
            margin-top: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 8px;
            font-size: 13px;
            color: #666666;
            word-break: break-all;
        }
        
        .link-alternative strong {
            color: #333333;
            display: block;
            margin-bottom: 8px;
        }
        
        @media only screen and (max-width: 600px) {
            .email-container {
                width: 100% !important;
                border-radius: 0;
            }
            
            .email-header, .email-body, .email-footer {
                padding: 30px 20px !important;
            }
            
            .verify-button {
                padding: 14px 30px;
                font-size: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="email-header">
            <div class="logo">DOLCIREVA</div>
            <h1>Vérifiez votre email</h1>
        </div>
        
        <!-- Body -->
        <div class="email-body">
            <h2>Bonjour {{ $user->first_name }} {{ $user->last_name }} 👋</h2>
            
            <p>
                Bienvenue sur <span class="highlight">DOLCIREVA</span> ! Nous sommes ravis de vous avoir parmi nous.
            </p>
            
            <p>
                Pour finaliser votre inscription et activer votre compte, veuillez vérifier votre adresse email en cliquant sur le bouton ci-dessous.
            </p>
            
            <div class="button-container">
                <a href="{{ $verificationUrl }}" class="verify-button">
                    Vérifier mon email
                </a>
            </div>
            
            <div class="info-box">
                <p>
                    <strong>💡 Astuce :</strong> Ce lien de vérification est valide pendant <strong>60 minutes</strong>. 
                    Si vous n'avez pas demandé ce code, vous pouvez ignorer cet email.
                </p>
            </div>
            
            <div class="divider"></div>
            
            <p style="font-size: 14px; color: #888888;">
                Si le bouton ne fonctionne pas, copiez et collez le lien suivant dans votre navigateur :
            </p>
            
            <div class="link-alternative">
                <strong>Lien de vérification :</strong>
                <a href="{{ $verificationUrl }}" style="color: #ff6a13; text-decoration: none;">
                    {{ $verificationUrl }}
                </a>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="email-footer">
            <p><strong>DOLCIREVA</strong></p>
            <p>Votre plateforme de réservation de luxe</p>
            <p style="margin-top: 15px; font-size: 12px;">
                Si vous avez des questions, n'hésitez pas à nous contacter.
            </p>
            <div class="social-links">
                <p style="font-size: 12px; margin-top: 15px; color: #aaaaaa;">
                    © {{ date('Y') }} DOLCIREVA. Tous droits réservés.
                </p>
            </div>
        </div>
    </div>
</body>
</html>

