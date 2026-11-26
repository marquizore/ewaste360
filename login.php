<?php
require_once __DIR__ . '/app/Middleware/AuthMiddleware.php';
require_once __DIR__ . '/app/Repositories/UsuarioRepository.php';

AuthMiddleware::iniciarSesionSegura();

$errorServidor = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $errorServidor = 'Por favor ingresa tu usuario y contraseña.';
    } else {
        try {
            $repo = new UsuarioRepository();
            $usuario = $repo->buscarPorUsernameOEmail($username);

            // Verificación estricta mediante hash bcrypt (sin contraseñas en texto plano)
            if ($usuario && $usuario->verificarPassword($password)) {
                if ($usuario->isActivo()) {
                    AuthMiddleware::establecerSesionUsuario($usuario);
                    header('Location: index.php');
                    exit;
                } else {
                    $errorServidor = 'Esta cuenta de usuario se encuentra desactivada.';
                }
            } else {
                $errorServidor = 'Usuario o contraseña incorrectos.';
            }
        } catch (Throwable $e) {
            $errorServidor = 'No fue posible conectar con el servicio de autenticación.';
        }
    }
}

$usuarioActual = AuthMiddleware::obtenerUsuarioActual();
if (!empty($usuarioActual['id'])) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión | E-Waste 360°</title>
    <link rel="stylesheet" href="public/css/styles.css?v=5">
</head>
<body class="login-body">
    <main class="login-wrapper">
        <div class="card login-card">
            <div class="login-header">
                <img src="public/img/icon-reutilizar.svg" alt="E-Waste 360" width="56" height="56">
                <h1>E-Waste 360°</h1>
                <p>Acceso a la Plataforma de Gestión Sustentable de RAEE</p>
            </div>

            <form id="form-login" method="post" action="login.php">
                <div class="form-group">
                    <label for="username">Usuario o Correo Institucional</label>
                    <input type="text" id="username" name="username" placeholder="Ingresa tu usuario o correo" autocomplete="username" required>
                </div>
                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <input type="password" id="password" name="password" placeholder="Ingresa tu contraseña" autocomplete="current-password" required>
                </div>
                <button type="submit" class="btn-primary btn-full">Iniciar Sesión Segura</button>

                <?php if ($errorServidor !== ''): ?>
                    <p id="login-error" class="msg-error"><?php echo htmlspecialchars($errorServidor); ?></p>
                <?php endif; ?>
            </form>

            <hr class="divider">

            <div class="login-footer">
                <a href="index.php">← Continuar como Visitante Público</a>
            </div>
        </div>
    </main>
</body>
</html>
