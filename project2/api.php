<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$dataFile = __DIR__ . '/data_informasi.json';

// Buat data dummy awal jika file JSON belum ada
if (!file_exists($dataFile)) {
    $dummyAwal = [
        [
            "id" => 1,
            "kategori" => "Produk & Teknologi",
            "tanggal" => "15 Oktober 2026",
            "judul" => "Peluncuran Platform Digital Nusantara v2.0",
            "ringkasan" => "Peningkatan kecepatan layanan dan keamanan data bagi seluruh mitra bisnis.",
            "konten" => "Platform Digital Nusantara v2.0 hadir dengan antarmuka lebih cepat, sistem autentikasi berlapis, serta integrasi pelaporan real-time untuk mendukung operasional mitra bisnis di berbagai daerah."
        ],
        [
            "id" => 2,
            "kategori" => "CSR & Karir",
            "tanggal" => "02 November 2026",
            "judul" => "Program Beasiswa & Pelatihan Industri TI",
            "ringkasan" => "Membuka kesempatan magang dan pelatihan web modern bagi mahasiswa.",
            "konten" => "Program ini dirancang untuk membekali mahasiswa dengan keterampilan pengembangan aplikasi web modern, kolaborasi tim, dan proyek industri."
        ],
        [
            "id" => 3,
            "kategori" => "Berita Perusahaan",
            "tanggal" => "18 November 2026",
            "judul" => "Kemitraan Strategis Logistik Nasional",
            "ringkasan" => "Memperluas jaringan distribusi ke 12 kota besar di Indonesia.",
            "konten" => "Melalui kemitraan strategis ini, pengiriman layanan dan distribusi operasional diperluas ke 12 kota besar di Indonesia."
        ]
    ];
    file_put_contents($dataFile, json_encode($dummyAwal, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

$items = json_decode(file_get_contents($dataFile), true);
if (!is_array($items)) {
    $items = [];
}

$method = $_SERVER['REQUEST_METHOD'];
$id = isset($_GET['id']) ? (int) $_GET['id'] : null;

switch ($method) {
    case 'GET':
        if ($id !== null) {
            foreach ($items as $item) {
                if ((int) $item['id'] === $id) {
                    echo json_encode(["status" => "success", "data" => $item], JSON_UNESCAPED_UNICODE);
                    exit;
                }
            }
            http_response_code(404);
            echo json_encode(["status" => "error", "message" => "Data tidak ditemukan"], JSON_UNESCAPED_UNICODE);
            exit;
        }
        echo json_encode(["status" => "success", "total" => count($items), "data" => $items], JSON_UNESCAPED_UNICODE);
        break;

    case 'POST':
        $input = json_decode(file_get_contents("php://input"), true);
        if (!is_array($input) || empty($input['judul'])) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Field judul wajib diisi"], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $maxId = 0;
        foreach ($items as $item) {
            if ((int) $item['id'] > $maxId) {
                $maxId = (int) $item['id'];
            }
        }

        $baru = [
            "id" => $maxId + 1,
            "kategori" => $input['kategori'] ?? "Berita Perusahaan",
            "tanggal" => $input['tanggal'] ?? date("d F Y"),
            "judul" => $input['judul'],
            "ringkasan" => $input['ringkasan'] ?? "",
            "konten" => $input['konten'] ?? ""
        ];

        $items[] = $baru;
        file_put_contents($dataFile, json_encode($items, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        http_response_code(201);
        echo json_encode(["status" => "success", "message" => "Data berhasil ditambahkan", "data" => $baru], JSON_UNESCAPED_UNICODE);
        break;

    case 'PUT':
    case 'PATCH':
        if ($id === null) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Parameter id wajib disertakan di URL"], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $input = json_decode(file_get_contents("php://input"), true);
        if (!is_array($input)) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Format JSON tidak valid"], JSON_UNESCAPED_UNICODE);
            exit;
        }

        foreach ($items as &$item) {
            if ((int) $item['id'] === $id) {
                if (isset($input['kategori'])) $item['kategori'] = $input['kategori'];
                if (isset($input['tanggal'])) $item['tanggal'] = $input['tanggal'];
                if (isset($input['judul'])) $item['judul'] = $input['judul'];
                if (isset($input['ringkasan'])) $item['ringkasan'] = $input['ringkasan'];
                if (isset($input['konten'])) $item['konten'] = $input['konten'];

                file_put_contents($dataFile, json_encode($items, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
                echo json_encode(["status" => "success", "message" => "Data berhasil diperbarui", "data" => $item], JSON_UNESCAPED_UNICODE);
                exit;
            }
        }

        http_response_code(404);
        echo json_encode(["status" => "error", "message" => "Data tidak ditemukan"], JSON_UNESCAPED_UNICODE);
        break;

    case 'DELETE':
        if ($id === null) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Parameter id wajib disertakan di URL"], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $ditemukan = false;
        $sisaData = [];
        foreach ($items as $item) {
            if ((int) $item['id'] === $id) {
                $ditemukan = true;
                continue;
            }
            $sisaData[] = $item;
        }

        if (!$ditemukan) {
            http_response_code(404);
            echo json_encode(["status" => "error", "message" => "Data tidak ditemukan"], JSON_UNESCAPED_UNICODE);
            exit;
        }

        file_put_contents($dataFile, json_encode(array_values($sisaData), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        echo json_encode(["status" => "success", "message" => "Data berhasil dihapus"], JSON_UNESCAPED_UNICODE);
        break;

    default:
        http_response_code(405);
        echo json_encode(["status" => "error", "message" => "Method tidak diizinkan"], JSON_UNESCAPED_UNICODE);
        break;
}