<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion | Télévision Tunisienne</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(135deg, #b30000, #ffffff);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', sans-serif;
        }

        .login-card {
            background: #ffffff;
            border-radius: 18px;
            width: 400px;
            box-shadow: 0 15px 40px rgba(0,0,0,0.2);
            overflow: hidden;
        }

        .login-header {
            background: #b30000;
            color: white;
            text-align: center;
            padding: 25px;
        }

        .login-header h4 {
            margin: 0;
            font-weight: bold;
        }

        .login-body {
            padding: 30px;
        }

        .form-control {
            border-radius: 10px;
        }

        .btn-login {
            background-color: #b30000;
            border: none;
            border-radius: 10px;
            font-weight: bold;
        }

        .btn-login:hover {
            background-color: #8f0000;
        }

        .footer-text {
            font-size: 13px;
            text-align: center;
            margin-top: 15px;
            color: #777;
        }
    </style>
</head>

<body>

<div class="login-card">
    <div class="login-header">
        <h4>Télévision Tunisienne</h4>
        <small>Espace de gestion des congés</small>
    </div>

    <div class="login-body">
        <form method="post" action="login.php">

            <div class="mb-3">
                <label class="form-label">Login</label>
                <input type="text" name="login" class="form-control" placeholder="Votre login" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Mot de passe</label>
                <input type="password" name="mp" class="form-control" placeholder="Votre mot de passe" required>
            </div>

            <div class="d-grid mt-4">
                <button type="submit" class="btn btn-login text-white">
                    Se connecter
                </button>
            </div>

        </form>

        <div class="footer-text">
            © Télévision Tunisienne – 2026
        </div>
    </div>
</div>

</body>
</html>


