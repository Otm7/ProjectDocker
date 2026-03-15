<?php

// Minimal front controller for the demo.
// It provides a lightweight auth/register system backed by MySQL.

session_start();

$view = __DIR__ . '/../resources/views/welcome.blade.php';
if (!file_exists($view)) {
    http_response_code(404);
    echo "View not found";
    exit;
}

function env($key, $default = null) {
    static $vars;
    if ($vars === null) {
        $vars = [];
        $envFile = __DIR__ . '/../.env';
        if (file_exists($envFile)) {
            foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
                if (strpos(trim($line), '#') === 0) {
                    continue;
                }
                if (!strpos($line, '=')) {
                    continue;
                }
                list($k, $v) = explode('=', $line, 2);
                $vars[trim($k)] = trim($v);
            }
        }
    }
    return $vars[$key] ?? $default;
}

$dbHost = env('DB_HOST', 'esgi-mysql');
$dbPort = env('DB_PORT', '3307');
$dbName = env('DB_DATABASE', 'laravel');
$dbUser = env('DB_USERNAME', 'laravel');
$dbPass = env('DB_PASSWORD', 'laravel');

// Wait for the database to be ready (basic retry loop)
$pdo = null;
$attempts = 0;
while ($attempts < 10) {
    try {
        $pdo = new PDO("mysql:host={$dbHost};port={$dbPort};dbname={$dbName};charset=utf8mb4", $dbUser, $dbPass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
        break;
    } catch (Exception $e) {
        $attempts++;
        usleep(250000);
    }
}

if (!$pdo) {
    http_response_code(500);
    echo "Cannot connect to database. Please check docker logs.";
    exit;
}

// Auto-create users table if missing
$pdo->exec("CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(128) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

$messages = [];
$errors = [];

function redirect($url) {
    header('Location: ' . $url);
    exit;
}

$action = $_POST['action'] ?? null;
if ($action === 'register') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($name === '' || $email === '' || $password === '') {
        $errors[] = 'Tous les champs sont obligatoires.';
    } else {
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors[] = 'Cette adresse e-mail est déjà utilisée.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('INSERT INTO users (name, email, password) VALUES (?, ?, ?)');
            $stmt->execute([$name, $email, $hash]);
            $_SESSION['user_id'] = $pdo->lastInsertId();
            $_SESSION['user_name'] = $name;
            $messages[] = 'Inscription réussie, bienvenu(e) ' . htmlspecialchars($name) . '!';
        }
    }
}

if ($action === 'login') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $errors[] = 'Tous les champs sont obligatoires.';
    } else {
        $stmt = $pdo->prepare('SELECT id, name, password FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        if (!$user || !password_verify($password, $user['password'])) {
            $errors[] = 'Identifiants incorrects.';
        } else {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $messages[] = 'Connexion réussie, bienvenue ' . htmlspecialchars($user['name']) . '!';
        }
    }
}

if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_unset();
    session_destroy();
    redirect('/');
}

$authContent = '';
if (!empty($messages)) {
    foreach ($messages as $msg) {
        $authContent .= '<div class="notice">' . htmlspecialchars($msg) . '</div>';
    }
}
if (!empty($errors)) {
    foreach ($errors as $err) {
        $authContent .= '<div class="error">' . htmlspecialchars($err) . '</div>';
    }
}

if (!empty($_SESSION['user_id'])) {
    $name = htmlspecialchars($_SESSION['user_name'] ?? '');
    $authContent .= "<div class=\"form-box\"><p>Connecté en tant que <strong>{$name}</strong>.</p><p><a href=\"?action=logout\">Se déconnecter</a></p></div>";
} else {
    $authContent .= "<div class=\"form-box\"><h4>Connexion</h4><form method=\"POST\"><input type=\"hidden\" name=\"action\" value=\"login\"><label>Email</label><input type=\"email\" name=\"email\" required><label>Mot de passe</label><input type=\"password\" name=\"password\" required><button type=\"submit\">Se connecter</button></form></div>";
    $authContent .= "<div class=\"form-box\"><h4>Inscription</h4><form method=\"POST\"><input type=\"hidden\" name=\"action\" value=\"register\"><label>Nom</label><input type=\"text\" name=\"name\" required><label>Email</label><input type=\"email\" name=\"email\" required><label>Mot de passe</label><input type=\"password\" name=\"password\" required><button type=\"submit\">S'inscrire</button></form></div>";
}

$contents = file_get_contents($view);

// Replace the label and the auth content
$label = $_SERVER['SERVER_LABEL'] ?? 'Serveur';
$contents = str_replace('{{ $_SERVER[\'SERVER_LABEL\'] ?? \'Serveur\' }}', $label, $contents);
$contents = str_replace('{{ AUTH_CONTENT }}', $authContent, $contents);

echo $contents;
