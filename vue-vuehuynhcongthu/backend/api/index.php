<?php
declare(strict_types=1);
require __DIR__ . '/config.php';

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, OPTIONS');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') exit;

function respond(mixed $data, int $status = 200): never {
    http_response_code($status);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}
function body(): array {
    $data = json_decode(file_get_contents('php://input'), true);
    return is_array($data) ? $data : [];
}
function required(array $data, string ...$keys): void {
    foreach ($keys as $key) if (!isset($data[$key]) || trim((string)$data[$key]) === '') respond(['message' => "Thiếu trường: $key"], 422);
}
function publicUser(array $user): array {
    return ['id' => (int)$user['id'], 'fullname' => $user['fullname'], 'email' => $user['email'], 'role' => $user['role']];
}

$method = $_SERVER['REQUEST_METHOD'];
$route = trim($_GET['route'] ?? '', '/');
$pdo = db();

try {
    if ($method === 'POST' && $route === 'auth/register') {
        $data = body(); required($data, 'fullname', 'email', 'password');
        $email = strtolower(trim($data['email']));
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) respond(['message' => 'Email không hợp lệ.'], 422);
        if (strlen($data['password']) < 6) respond(['message' => 'Mật khẩu phải có ít nhất 6 ký tự.'], 422);
        $check = $pdo->prepare('SELECT id FROM users WHERE email = ?'); $check->execute([$email]);
        if ($check->fetch()) respond(['message' => 'Email này đã được đăng ký.'], 409);
        $stmt = $pdo->prepare('INSERT INTO users (fullname, email, password_hash, role) VALUES (?, ?, ?, ?)');
        $stmt->execute([trim($data['fullname']), $email, password_hash($data['password'], PASSWORD_DEFAULT), 'customer']);
        $id = (int)$pdo->lastInsertId();
        respond(['message' => 'Đăng ký thành công.', 'user' => ['id' => $id, 'fullname' => trim($data['fullname']), 'email' => $email, 'role' => 'customer']], 201);
    }

    if ($method === 'POST' && $route === 'auth/login') {
        $data = body(); required($data, 'email', 'password');
        $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?'); $stmt->execute([strtolower(trim($data['email']))]);
        $user = $stmt->fetch();
        if (!$user || !password_verify($data['password'], $user['password_hash'])) respond(['message' => 'Email hoặc mật khẩu không đúng.'], 401);
        respond(['message' => 'Đăng nhập thành công.', 'user' => publicUser($user)]);
    }

    if ($method === 'GET' && $route === 'plants') {
        $stmt = $pdo->query('SELECT p.id, p.name, p.latin_name, p.short_note, p.description, p.planting_guide, p.care_guide, p.image_url, p.image_source_url, p.price, c.name AS category FROM plants p JOIN plant_categories c ON c.id=p.category_id WHERE p.is_active=1 ORDER BY p.id');
        respond(['plants' => $stmt->fetchAll()]);
    }

    if ($method === 'GET' && $route === 'lots') {
        $stmt = $pdo->query("SELECT l.id, l.lot_code, p.name AS plant_name, a.name AS area, l.sowed_date, l.initial_quantity, l.remaining_quantity, l.status, DATEDIFF(CURDATE(), l.sowed_date) AS age_days FROM nursery_lots l JOIN plants p ON p.id=l.plant_id JOIN nursery_areas a ON a.id=l.area_id ORDER BY l.sowed_date");
        respond(['lots' => $stmt->fetchAll()]);
    }

    if ($method === 'PATCH' && preg_match('#^lots/(\\d+)/status$#', $route, $matches)) {
        $data = body(); required($data, 'status');
        $allowed = ['growing', 'ready', 'needs_check', 'sold_out'];
        if (!in_array($data['status'], $allowed, true)) respond(['message' => 'Trạng thái không hợp lệ.'], 422);
        $stmt = $pdo->prepare('UPDATE nursery_lots SET status = ? WHERE id = ?'); $stmt->execute([$data['status'], $matches[1]]);
        if (!$stmt->rowCount()) respond(['message' => 'Không tìm thấy lô cây.'], 404);
        respond(['message' => 'Đã cập nhật trạng thái lô cây.']);
    }

    if ($method === 'POST' && $route === 'orders') {
        $data = body(); required($data, 'customer_name', 'customer_email', 'items', 'payment_proof', 'order_code');
        if (!preg_match('/^VX[0-9]{8}$/', (string)$data['order_code'])) respond(['message' => 'Mã đơn hàng không hợp lệ.'], 422);
        if (!is_array($data['items']) || !$data['items']) respond(['message' => 'Đơn hàng chưa có sản phẩm.'], 422);
        if (empty($data['user_id'])) respond(['message' => 'Vui lòng đăng nhập để đặt hàng.'], 401);
        if (!preg_match('#^data:image/(jpeg|png|webp);base64,#', $data['payment_proof'], $proofType)) respond(['message' => 'Ảnh thanh toán không hợp lệ.'], 422);
        $proofBytes = base64_decode(substr($data['payment_proof'], strpos($data['payment_proof'], ',') + 1), true);
        if ($proofBytes === false || strlen($proofBytes) > 5 * 1024 * 1024) respond(['message' => 'Ảnh hóa đơn tối đa 5 MB.'], 422);
        $owner = $pdo->prepare('SELECT id, fullname, email FROM users WHERE id = ? AND email = ?');
        $owner->execute([(int)$data['user_id'], strtolower(trim($data['customer_email']))]);
        $customer = $owner->fetch();
        if (!$customer) respond(['message' => 'Không xác minh được tài khoản đặt hàng.'], 403);
        $pdo->beginTransaction();
        $code = (string)$data['order_code'];
        $existing = $pdo->prepare('SELECT id FROM orders WHERE order_code = ?'); $existing->execute([$code]);
        if ($existing->fetch()) { $pdo->rollBack(); respond(['message' => 'Mã đơn đã được sử dụng. Vui lòng tải lại trang và thử lại.'], 409); }
        $order = $pdo->prepare('INSERT INTO orders (order_code, user_id, customer_name, customer_email, customer_phone, delivery_address, subtotal, payment_status) VALUES (?, ?, ?, ?, ?, ?, 0, ?)');
        $order->execute([$code, $customer['id'], trim($data['customer_name']), $customer['email'], $data['customer_phone'] ?? null, $data['delivery_address'] ?? null, 'proof_submitted']);
        $orderId = (int)$pdo->lastInsertId(); $total = 0;
        $plantQuery = $pdo->prepare('SELECT id, price FROM plants WHERE name = ? AND is_active = 1');
        $itemQuery = $pdo->prepare('INSERT INTO order_items (order_id, plant_id, quantity, unit_price, line_total) VALUES (?, ?, ?, ?, ?)');
        foreach ($data['items'] as $item) {
            $quantity = (int)($item['quantity'] ?? 0); $plantName = trim((string)($item['plant_name'] ?? ''));
            if ($quantity < 1 || $plantName === '') throw new RuntimeException('Sản phẩm hoặc số lượng không hợp lệ.');
            $plantQuery->execute([$plantName]); $plant = $plantQuery->fetch();
            if (!$plant) throw new RuntimeException('Sản phẩm không còn kinh doanh.');
            $lineTotal = $quantity * (float)$plant['price']; $total += $lineTotal;
            $itemQuery->execute([$orderId, $plant['id'], $quantity, $plant['price'], $lineTotal]);
        }
        $extension = $proofType[1] === 'jpeg' ? 'jpg' : $proofType[1];
        $directory = __DIR__ . '/uploads/payment-proofs';
        if (!is_dir($directory) && !mkdir($directory, 0755, true)) throw new RuntimeException('Không tạo được thư mục lưu hóa đơn.');
        $fileName = $code . '.' . $extension;
        if (file_put_contents($directory . '/' . $fileName, $proofBytes, LOCK_EX) === false) throw new RuntimeException('Không lưu được ảnh hóa đơn.');
        $proof = $pdo->prepare('INSERT INTO payment_proofs (order_id, file_name, file_path) VALUES (?, ?, ?)');
        $proof->execute([$orderId, $fileName, 'uploads/payment-proofs/' . $fileName]);
        $pdo->prepare('UPDATE orders SET subtotal = ? WHERE id = ?')->execute([$total, $orderId]);
        $pdo->commit(); respond(['message' => 'Đã tạo đơn hàng, chờ quản trị viên duyệt.', 'order_code' => $code], 201);
    }

    if ($method === 'GET' && $route === 'admin/orders') {
        $stmt = $pdo->query("SELECT o.id, o.order_code, o.customer_name, o.customer_email, o.subtotal, o.status, o.payment_status, o.created_at, pp.file_path, GROUP_CONCAT(CONCAT(p.name, ' × ', oi.quantity) SEPARATOR ', ') AS items FROM orders o JOIN order_items oi ON oi.order_id=o.id JOIN plants p ON p.id=oi.plant_id LEFT JOIN payment_proofs pp ON pp.order_id=o.id GROUP BY o.id ORDER BY o.created_at DESC");
        respond(['orders' => $stmt->fetchAll()]);
    }

    if ($method === 'PATCH' && preg_match('#^admin/orders/(\\d+)$#', $route, $matches)) {
        $data = body(); required($data, 'status');
        if (!in_array($data['status'], ['approved', 'rejected'], true)) respond(['message' => 'Trạng thái đơn không hợp lệ.'], 422);
        $stmt = $pdo->prepare('UPDATE orders SET status = ? WHERE id = ?'); $stmt->execute([$data['status'], $matches[1]]);
        if (!$stmt->rowCount()) respond(['message' => 'Không tìm thấy đơn hàng hoặc trạng thái không đổi.'], 404);
        respond(['message' => 'Đã cập nhật trạng thái đơn hàng.']);
    }

    if ($method === 'GET' && $route === 'customer/orders') {
        $userId = (int)($_GET['user_id'] ?? 0); $email = strtolower(trim($_GET['email'] ?? ''));
        $stmt = $pdo->prepare("SELECT o.id, o.order_code, o.subtotal, o.status, o.payment_status, o.created_at, pp.file_path, GROUP_CONCAT(CONCAT(p.name, ' × ', oi.quantity) SEPARATOR ', ') AS items FROM orders o JOIN order_items oi ON oi.order_id=o.id JOIN plants p ON p.id=oi.plant_id LEFT JOIN payment_proofs pp ON pp.order_id=o.id WHERE o.user_id=? AND o.customer_email=? GROUP BY o.id ORDER BY o.created_at DESC");
        $stmt->execute([$userId, $email]); respond(['orders' => $stmt->fetchAll()]);
    }

    respond(['message' => 'Không tìm thấy API.'], 404);
} catch (Throwable $exception) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    error_log($exception->getMessage());
    respond(['message' => 'Có lỗi máy chủ. Kiểm tra cấu hình MySQL và nhật ký PHP.'], 500);
}
