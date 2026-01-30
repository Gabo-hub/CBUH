<?php
// modules/auth/login.php
session_start();
require_once '../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (!empty($username) && !empty($password)) {
        try {
            $pdo = getDBConnection();
            // Nuevas tablas en español: usuarios, roles
            $stmt = $pdo->prepare("SELECT u.id, u.usuario, u.clave_hash, u.rol_id, u.url_foto 
                                   FROM usuarios u 
                                   WHERE u.usuario = :username AND u.estado_id = 1");
            $stmt->execute(['username' => $username]);
            $user = $stmt->fetch();

            // Verificación de contraseña con password_verify (bcrypt)
            if ($user && password_verify($password, $user['clave_hash'])) {
                // Guardar datos del usuario en sesión
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['usuario'] = $user['usuario'];
                $_SESSION['rol_id'] = $user['rol_id'];
                $_SESSION['url_foto'] = $user['url_foto'];

                // Obtener el nombre del rol
                $stmtRole = $pdo->prepare("SELECT nombre FROM roles WHERE id = :id");
                $stmtRole->execute(['id' => $user['rol_id']]);
                $roleName = $stmtRole->fetchColumn();

                $_SESSION['rol_nombre'] = $roleName;
                $roleName = strtolower($roleName);

                // Redirección según el rol
                if (strpos($roleName, 'estudiante') !== false) {
                    header('Location: ../student/index.php');
                } elseif (strpos($roleName, 'docente') !== false) {
                    header('Location: ../teacher/index.php');
                } else {
                    // Admin, Control de Estudio, Director
                    header('Location: ../admin/index.php');
                }
                exit;

            } else {
                $error = "Credenciales incorrectas.";
            }
        } catch (Exception $e) {
            $error = "Error de conexión: " . $e->getMessage();
        }
    } else {
        $error = "Por favor complete todos los campos.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de Sesión</title>
    <!-- Fuente Montserrat para que coincida con el estilo -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" type="image/png" href="../../assets/img/libro.png">
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            /* Fondo actualizado a un degradado radial elegante y limpio */
            background: radial-gradient(ellipse at center, #5e1b26 0%, #1a0508 100%);
            min-height: 100vh;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.95);
            box-shadow: 0 0 50px rgba(255, 255, 255, 0.1), 0 0 20px rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(10px);
        }

        .input-glow {
            border: 2px solid rgba(255, 255, 255, 0.5);
            background: linear-gradient(to right, rgba(255, 255, 255, 0.8), rgba(255, 255, 255, 0.4));
            box-shadow: 0 0 15px rgba(238, 188, 63, 0.2);
            transition: all 0.3s ease;
        }

        .input-glow:focus-within {
            border-color: #eebc3f;
            box-shadow: 0 0 20px rgba(238, 188, 63, 0.6);
        }

        .btn-gold {
            background: linear-gradient(180deg, #fceeb5 0%, #eebc3f 30%, #d49f2a 100%);
            box-shadow: 0 4px 6px rgba(212, 159, 42, 0.4);
            transition: transform 0.1s ease;
        }

        .btn-gold:active {
            transform: scale(0.98);
        }

        /* Efecto para que el logo sobresalga */
        .logo-container {
            margin-top: -80px;
        }
    </style>
</head>

<body class="min-h-screen flex items-center justify-center p-4">

    <!-- Contenedor Principal de la Tarjeta -->
    <div class="login-card w-full max-w-md rounded-[30px] p-8 pb-10 relative flex flex-col items-center">

        Área del Logo
        <div class="logo-container mb-6 relative z-10">
            <img src="../../assets/img/cbuh.png" alt="Logo Institucional"
                class="w-32 md:w-40 drop-shadow-2xl object-contain"
                style="filter: drop-shadow(0 4px 6px rgba(0,0,0,0.3));">
        </div>

        <!-- Título -->
        <h1 class="text-2xl md:text-3xl font-bold text-black mb-8 tracking-wide text-center">
            PORTAL ESTUDIANTIL
        </h1>

        <!-- Formulario -->
        <form class="w-full space-y-6 flex flex-col items-center" method="POST" action="login.php">
            <?php if (isset($error)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline"><?php echo $error; ?></span>
                </div>
            <?php endif; ?>

            <!-- Campo Cédula -->
            <div class="input-glow w-full rounded-xl flex items-center px-4 py-3 relative group">
                <!-- Icono Usuario -->
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-[#721c24] mr-3" fill="currentColor"
                    viewBox="0 0 24 24">
                    <path
                        d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                </svg>
                <input type="text" name="username" placeholder="Cédula"
                    class="bg-transparent border-none outline-none text-gray-800 placeholder-gray-800 text-lg w-full font-medium"
                    required>
            </div>

            <!-- Campo Contraseña -->
            <div class="input-glow w-full rounded-xl flex items-center px-4 py-3 relative group">
                <!-- Icono Candado -->
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-[#721c24] mr-3" fill="currentColor"
                    viewBox="0 0 24 24">
                    <path
                        d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z" />
                </svg>
                <input type="password" name="password" placeholder="Contraseña"
                    class="bg-transparent border-none outline-none text-gray-800 placeholder-gray-800 text-lg w-full font-medium"
                    required>
            </div>

            <!-- Botón Acceder -->
            <button type="submit"
                class="btn-gold w-full py-3 rounded-full text-black font-bold text-xl tracking-wider mt-4 hover:brightness-110">
                ACCEDER
            </button>

            <!-- Enlace Olvidar Contraseña -->
            <a href="#"
                class="text-[#5a1a25] underline decoration-1 underline-offset-4 text-sm font-semibold hover:text-black transition-colors mt-2">
                ¿Olvidaste tu contraseña?
            </a>

        </form>
    </div>

</body>

</html>