<?php
require_once '../config.php';

// Obtener el método de la solicitud
$method = $_SERVER['REQUEST_METHOD'];
$action = isset($_GET['action']) ? $_GET['action'] : '';

// Manejar las diferentes acciones
switch ($action) {
    case 'login':
        if ($method === 'POST') {
            handleLogin();
        }
        break;
    case 'register':
        if ($method === 'POST') {
            handleRegister();
        }
        break;
    default:
        jsonResponse(['error' => 'Acción no válida'], 400);
}

function handleLogin() {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['email']) || !isset($data['password'])) {
        jsonResponse(['error' => 'Email y contraseña son requeridos'], 400);
    }

    $email = sanitizeInput($data['email']);
    $password = $data['password'];

    if (!validateEmail($email)) {
        jsonResponse(['error' => 'Email no válido'], 400);
    }

    try {
        $db = getDBConnection();
        $stmt = $db->prepare("SELECT id, username, email, password_hash FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user || !password_verify($password, $user['password_hash'])) {
            jsonResponse(['error' => 'Credenciales inválidas'], 401);
        }

        // Generar token JWT (en producción)
        $token = bin2hex(random_bytes(32)); // Simplificado para desarrollo

        jsonResponse([
            'success' => true,
            'user' => [
                'id' => $user['id'],
                'username' => $user['username'],
                'email' => $user['email']
            ],
            'token' => $token
        ]);

    } catch (PDOException $e) {
        jsonResponse(['error' => 'Error en el servidor'], 500);
    }
}

function handleRegister() {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['username']) || !isset($data['email']) || !isset($data['password'])) {
        jsonResponse(['error' => 'Todos los campos son requeridos'], 400);
    }

    $username = sanitizeInput($data['username']);
    $email = sanitizeInput($data['email']);
    $password = $data['password'];

    if (!validateEmail($email)) {
        jsonResponse(['error' => 'Email no válido'], 400);
    }

    if (strlen($password) < 6) {
        jsonResponse(['error' => 'La contraseña debe tener al menos 6 caracteres'], 400);
    }

    try {
        $db = getDBConnection();
        
        // Verificar si el email ya existe
        $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            jsonResponse(['error' => 'El email ya está registrado'], 400);
        }

        // Insertar nuevo usuario
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $db->prepare("INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)");
        $stmt->execute([$username, $email, $passwordHash]);

        $userId = $db->lastInsertId();

        jsonResponse([
            'success' => true,
            'message' => 'Usuario registrado exitosamente',
            'user' => [
                'id' => $userId,
                'username' => $username,
                'email' => $email
            ]
        ], 201);

    } catch (PDOException $e) {
        jsonResponse(['error' => 'Error en el servidor'], 500);
    }
}
?>