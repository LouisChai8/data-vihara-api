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
$id     = $_GET['id'] ?? null;

if ($method === 'GET') {
    if ($id) {
        $stmt = $pdo->prepare("SELECT * FROM tbl_anggota WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        echo json_encode($row ?: null);
    } else {
        $search = $_GET['search'] ?? '';
        $stmt   = $pdo->prepare("
            SELECT * FROM tbl_anggota
            WHERE nama_lengkap LIKE ?
            ORDER BY created_at DESC
        ");
        $stmt->execute(["%$search%"]);
        echo json_encode($stmt->fetchAll());
    }
}

if ($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    if (empty($data['nama_lengkap'])) {
        http_response_code(400);
        echo json_encode(["error" => "nama_lengkap wajib diisi"]);
        exit();
    }

    $stmt = $pdo->prepare("
        INSERT INTO tbl_anggota (
            nama_lengkap, nama_baptis, jenis_kelamin,
            tanggal_lahir, chiu_tao_sejak,
            alamat, no_telepon, email,
            guru_pengajak, guru_penanggung, pandita,
            peran, status, foto
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->execute([
        $data['nama_lengkap'],
        $data['nama_baptis']     ?? null,
        $data['jenis_kelamin']   ?? null,
        $data['tanggal_lahir']   ?? null,
        $data['chiu_tao_sejak']  ?? null,
        $data['alamat']          ?? null,
        $data['no_telepon']      ?? null,
        $data['email']           ?? null,
        $data['guru_pengajak']   ?? null,
        $data['guru_penanggung'] ?? null,
        $data['pandita']         ?? null,
        $data['peran']           ?? 'Chiang Se',
        $data['status']          ?? 'Aktif',
        $data['foto']            ?? null,
    ]);

    echo json_encode([
        "success" => true,
        "id"      => (int) $pdo->lastInsertId(),
    ]);
}

if ($method === 'PUT') {
    if (!$id) {
        http_response_code(400);
        echo json_encode(["error" => "id diperlukan untuk update"]);
        exit();
    }

    $data = json_decode(file_get_contents("php://input"), true);

    if (empty($data['nama_lengkap'])) {
        http_response_code(400);
        echo json_encode(["error" => "nama_lengkap wajib diisi"]);
        exit();
    }

    $stmt = $pdo->prepare("
        UPDATE tbl_anggota SET
            nama_lengkap    = ?,
            nama_baptis     = ?,
            jenis_kelamin   = ?,
            tanggal_lahir   = ?,
            chiu_tao_sejak  = ?,
            alamat          = ?,
            no_telepon      = ?,
            email           = ?,
            guru_pengajak   = ?,
            guru_penanggung = ?,
            pandita         = ?,
            peran           = ?,
            status          = ?,
            foto            = ?
        WHERE id = ?
    ");

    $stmt->execute([
        $data['nama_lengkap'],
        $data['nama_baptis']     ?? null,
        $data['jenis_kelamin']   ?? null,
        $data['tanggal_lahir']   ?? null,
        $data['chiu_tao_sejak']  ?? null,
        $data['alamat']          ?? null,
        $data['no_telepon']      ?? null,
        $data['email']           ?? null,
        $data['guru_pengajak']   ?? null,
        $data['guru_penanggung'] ?? null,
        $data['pandita']         ?? null,
        $data['peran']           ?? 'Chiang Se',
        $data['status']          ?? 'Aktif',
        $data['foto']            ?? null,
        $id,
    ]);

    echo json_encode(["success" => true]);
}

if ($method === 'DELETE') {
    if (!$id) {
        http_response_code(400);
        echo json_encode(["error" => "id diperlukan untuk delete"]);
        exit();
    }

    $stmt = $pdo->prepare("DELETE FROM tbl_anggota WHERE id = ?");
    $stmt->execute([$id]);

    echo json_encode(["success" => true]);
}
?>