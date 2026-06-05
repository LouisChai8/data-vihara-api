<?php
header("Access-Control-Allow-Origin: https://data-vihara.vercel.app");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Accept, Authorization, X-Requested-With");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require 'config.php';

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

if ($method === 'POST' && $action === 'login') {
    $data = json_decode(file_get_contents("php://input"), true);

    $email    = trim($data['email']    ?? '');
    $password = trim($data['password'] ?? '');

    if (!$email || !$password) {
        http_response_code(400);
        echo json_encode(["error" => "Email dan password wajib diisi"]);
        exit();
    }

    $stmt = $pdo->prepare("SELECT * FROM tbl_admin WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password'])) {
        http_response_code(401);
        echo json_encode(["error" => "Email atau password salah"]);
        exit();
    }

    echo json_encode([
        "success" => true,
        "message" => "Login berhasil",
        "user"    => [
            "id"    => (int) $user['id'],
            "nama"  => $user['nama'],
            "email" => $user['email'],
        ]
    ]);
}
?>