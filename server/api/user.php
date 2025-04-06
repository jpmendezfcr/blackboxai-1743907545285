<?php
require_once '../config.php';

// Obtener el método de la solicitud
$method = $_SERVER['REQUEST_METHOD'];
$action = isset($_GET['action']) ? $_GET['action'] : '';

// Manejar las diferentes acciones
switch ($action) {
    case 'passwordRecovery':
        if ($method === 'POST') {
            handlePasswordRecovery();
        }
        break;
    case 'resetPassword':
        if ($method === 'POST') {
            handleResetPassword();
        }
        break;
    case 'profile':
        switch ($method) {
            case 'GET':
                getProfile();
                break;
            case 'PUT':
                updateProfile();
                break;
            default:
                jsonResponse(['error' => 'Método no permitido'], 405);
        }
        break;
    default:
        jsonResponse(['error' => 'Acción no válida'], 400);
}

function handlePasswordRecovery() {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['email'])) {
        jsonResponse(['error' => 'Email es requerido'], 400);
    }

    $email = sanitizeInput($data['email']);

    if (!validateEmail($email)) {
        jsonResponse(['error' => 'Email no válido'], 400);
    }

    try {
        $db = getDBConnection();
        
        // Verificar si el usuario existe
        $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            // Por seguridad, no revelamos si el email existe o no
            jsonResponse([
                'success' => true,
                'message' => 'Si el email existe en nuestro sistema, recibirás instrucciones para restablecer tu contraseña'
            ]);
        }

        // Generar token de recuperación
        $token = bin2hex(random_bytes(32));
        
        // Guardar token en la base de datos
        $stmt = $db->prepare("INSERT INTO password_resets (user_id, token) VALUES (?, ?)");
        $stmt->execute([$user['id'], $token]);

        // En un entorno real, aquí enviaríamos el email con el token
        // Por ahora, solo devolvemos el token en la respuesta
        jsonResponse([
            'success' => true,
            'message' => 'Instrucciones enviadas al correo',
            'debug_token' => $token // Solo para desarrollo
        ]);

    } catch (PDOException $e) {
        jsonResponse(['error' => 'Error en el servidor'], 500);
    }
}

function handleResetPassword() {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['token']) || !isset($data['new_password'])) {
        jsonResponse(['error' => 'Token y nueva contraseña son requeridos'], 400);
    }

    $token = sanitizeInput($data['token']);
    $newPassword = $data['new_password'];

    if (strlen($newPassword) < 6) {
        jsonResponse(['error' => 'La contraseña debe tener al menos 6 caracteres'], 400);
    }

    try {
        $db = getDBConnection();
        
        // Verificar token y obtener usuario
        $stmt = $db->prepare("
            SELECT user_id 
            FROM password_resets 
            WHERE token = ? AND created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)
        ");
        $stmt->execute([$token]);
        $reset = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$reset) {
            jsonResponse(['error' => 'Token inválido o expirado'], 400);
        }

        // Actualizar contraseña
        $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $db->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
        $stmt->execute([$passwordHash, $reset['user_id']]);

        // Eliminar token usado
        $stmt = $db->prepare("DELETE FROM password_resets WHERE token = ?");
        $stmt->execute([$token]);

        jsonResponse([
            'success' => true,
            'message' => 'Contraseña actualizada exitosamente'
        ]);

    } catch (PDOException $e) {
        jsonResponse(['error' => 'Error en el servidor'], 500);
    }
}

function getProfile() {
    // En un entorno real, obtendríamos el ID del usuario del token JWT
    $userId = validateToken();

    try {
        $db = getDBConnection();
        $stmt = $db->prepare("SELECT id, username, email, created_at FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            jsonResponse(['error' => 'Usuario no encontrado'], 404);
        }

        jsonResponse(['success' => true, 'user' => $user]);

    } catch (PDOException $e) {
        jsonResponse(['error' => 'Error en el servidor'], 500);
    }
}

function updateProfile() {
    // En un entorno real, obtendríamos el ID del usuario del token JWT
    $userId = validateToken();
    
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['username']) && !isset($data['email'])) {
        jsonResponse(['error' => 'No hay datos para actualizar'], 400);
    }

    try {
        $db = getDBConnection();
        $updates = [];
        $params = [];

        if (isset($data['username'])) {
            $username = sanitizeInput($data['username']);
            $updates[] = "username = ?";
            $params[] = $username;
        }

        if (isset($data['email'])) {
            $email = sanitizeInput($data['email']);
            if (!validateEmail($email)) {
                jsonResponse(['error' => 'Email no válido'], 400);
            }
            
            // Verificar si el nuevo email ya existe
            $stmt = $db->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
            $stmt->execute([$email, $userId]);
            if ($stmt->fetch()) {
                jsonResponse(['error' => 'El email ya está en uso'], 400);
            }
            
            $updates[] = "email = ?";
            $params[] = $email;
        }

        $params[] = $userId;
        $sql = "UPDATE users SET " . implode(", ", $updates) . " WHERE id = ?";
        
        $stmt = $db->prepare($sql);
        $stmt->execute($params);

        jsonResponse([
            'success' => true,
            'message' => 'Perfil actualizado exitosamente'
        ]);

    } catch (PDOException $e) {
        jsonResponse(['error' => 'Error en el servidor'], 500);
    }
}

// Función simulada para validar token
function validateToken() {
    // En un entorno real, validaríamos el token JWT
    // Por ahora, devolvemos un ID de usuario fijo para pruebas
    return 1;
}
?>