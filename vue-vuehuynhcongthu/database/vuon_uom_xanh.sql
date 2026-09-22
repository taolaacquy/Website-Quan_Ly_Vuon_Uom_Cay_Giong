-- Vườn Ươm Xanh - MySQL/MariaDB (XAMPP)
-- Import file này trong phpMyAdmin (tab Import) hoặc chạy bằng MySQL console.

CREATE DATABASE IF NOT EXISTS vuon_uom_xanh
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;
USE vuon_uom_xanh;

CREATE TABLE users (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  fullname VARCHAR(120) NOT NULL,
  email VARCHAR(190) NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('customer', 'admin', 'staff') NOT NULL DEFAULT 'customer',
  phone VARCHAR(20) NULL,
  address VARCHAR(255) NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uk_users_email (email)
) ENGINE=InnoDB;

CREATE TABLE plant_categories (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  slug VARCHAR(120) NOT NULL,
  UNIQUE KEY uk_categories_name (name),
  UNIQUE KEY uk_categories_slug (slug)
) ENGINE=InnoDB;

CREATE TABLE plants (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  category_id BIGINT UNSIGNED NOT NULL,
  name VARCHAR(150) NOT NULL,
  latin_name VARCHAR(180) NULL,
  short_note VARCHAR(255) NULL,
  description TEXT NULL,
  planting_guide TEXT NULL,
  care_guide TEXT NULL,
  image_url VARCHAR(500) NULL,
  image_source_url VARCHAR(500) NULL,
  price DECIMAL(12,2) NOT NULL DEFAULT 0,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_plants_category FOREIGN KEY (category_id) REFERENCES plant_categories(id),
  KEY idx_plants_category (category_id),
  KEY idx_plants_active (is_active)
) ENGINE=InnoDB;

CREATE TABLE nursery_areas (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(80) NOT NULL,
  description VARCHAR(255) NULL,
  UNIQUE KEY uk_nursery_areas_name (name)
) ENGINE=InnoDB;

CREATE TABLE nursery_lots (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  lot_code VARCHAR(40) NOT NULL,
  plant_id BIGINT UNSIGNED NOT NULL,
  area_id BIGINT UNSIGNED NOT NULL,
  sowed_date DATE NOT NULL,
  initial_quantity INT UNSIGNED NOT NULL,
  remaining_quantity INT UNSIGNED NOT NULL,
  status ENUM('growing', 'ready', 'needs_check', 'sold_out') NOT NULL DEFAULT 'growing',
  note TEXT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uk_nursery_lots_code (lot_code),
  CONSTRAINT fk_lots_plant FOREIGN KEY (plant_id) REFERENCES plants(id),
  CONSTRAINT fk_lots_area FOREIGN KEY (area_id) REFERENCES nursery_areas(id),
  KEY idx_lots_status (status)
) ENGINE=InnoDB;

CREATE TABLE care_tasks (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  lot_id BIGINT UNSIGNED NULL,
  area_id BIGINT UNSIGNED NULL,
  assigned_user_id BIGINT UNSIGNED NULL,
  title VARCHAR(180) NOT NULL,
  detail TEXT NULL,
  scheduled_at DATETIME NOT NULL,
  status ENUM('pending', 'completed', 'cancelled') NOT NULL DEFAULT 'pending',
  completed_at DATETIME NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_tasks_lot FOREIGN KEY (lot_id) REFERENCES nursery_lots(id) ON DELETE SET NULL,
  CONSTRAINT fk_tasks_area FOREIGN KEY (area_id) REFERENCES nursery_areas(id) ON DELETE SET NULL,
  CONSTRAINT fk_tasks_user FOREIGN KEY (assigned_user_id) REFERENCES users(id) ON DELETE SET NULL,
  KEY idx_tasks_schedule_status (scheduled_at, status)
) ENGINE=InnoDB;

CREATE TABLE orders (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  order_code VARCHAR(40) NOT NULL,
  user_id BIGINT UNSIGNED NULL,
  customer_name VARCHAR(120) NOT NULL,
  customer_email VARCHAR(190) NULL,
  customer_phone VARCHAR(20) NULL,
  delivery_address VARCHAR(255) NULL,
  subtotal DECIMAL(12,2) NOT NULL DEFAULT 0,
  status ENUM('pending', 'approved', 'rejected', 'shipping', 'completed', 'cancelled') NOT NULL DEFAULT 'pending',
  payment_status ENUM('unpaid', 'proof_submitted', 'paid', 'refunded') NOT NULL DEFAULT 'unpaid',
  note TEXT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uk_orders_code (order_code),
  CONSTRAINT fk_orders_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
  KEY idx_orders_status (status),
  KEY idx_orders_created_at (created_at)
) ENGINE=InnoDB;

CREATE TABLE order_items (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  order_id BIGINT UNSIGNED NOT NULL,
  plant_id BIGINT UNSIGNED NOT NULL,
  quantity INT UNSIGNED NOT NULL,
  unit_price DECIMAL(12,2) NOT NULL,
  line_total DECIMAL(12,2) NOT NULL,
  CONSTRAINT fk_order_items_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
  CONSTRAINT fk_order_items_plant FOREIGN KEY (plant_id) REFERENCES plants(id),
  KEY idx_order_items_order (order_id)
) ENGINE=InnoDB;

CREATE TABLE payment_proofs (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  order_id BIGINT UNSIGNED NOT NULL,
  file_name VARCHAR(255) NOT NULL,
  file_path VARCHAR(500) NOT NULL,
  uploaded_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  verified_at DATETIME NULL,
  verified_by BIGINT UNSIGNED NULL,
  CONSTRAINT fk_payment_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
  CONSTRAINT fk_payment_verifier FOREIGN KEY (verified_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE plant_price_history (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  plant_id BIGINT UNSIGNED NOT NULL,
  price DECIMAL(12,2) NOT NULL,
  changed_by BIGINT UNSIGNED NULL,
  effective_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_price_history_plant FOREIGN KEY (plant_id) REFERENCES plants(id) ON DELETE CASCADE,
  CONSTRAINT fk_price_history_user FOREIGN KEY (changed_by) REFERENCES users(id) ON DELETE SET NULL,
  KEY idx_price_history_plant_date (plant_id, effective_at)
) ENGINE=InnoDB;

-- Không tạo sẵn tài khoản có mật khẩu mặc định. Backend phải tạo password_hash
-- bằng password_hash() (PHP) hoặc bcrypt/argon2 (Node.js) khi người dùng đăng ký.

INSERT INTO plant_categories (name, slug) VALUES
('Cây ăn quả', 'cay-an-qua'),
('Cây lấy gỗ', 'cay-lay-go'),
('Cây công nghiệp', 'cay-cong-nghiep');

INSERT INTO nursery_areas (name, description) VALUES
('Khu A', 'Khu cây giống sẵn sàng bán'),
('Khu B', 'Khu cây đang ươm'),
('Khu C', 'Khu theo dõi và kiểm tra');

INSERT INTO plants (category_id, name, latin_name, short_note, price) VALUES
(1, 'Cây cam sành', 'Citrus reticulata × sinensis', 'Sai quả · Dễ chăm', 25000),
(1, 'Cây xoài keo', 'Mangifera indica', 'Năng suất cao', 30000),
(1, 'Cây mít Thái', 'Artocarpus heterophyllus', 'Quả to · Cơm dày', 30000),
(1, 'Cây bưởi da xanh', 'Citrus maxima', 'Thơm ngon · Ít hạt', 30000),
(2, 'Cây keo', 'Acacia mangium', 'Sinh trưởng nhanh', 5000),
(2, 'Cây xà cừ', 'Khaya senegalensis', 'Tán rộng · Bền vững', 15000),
(3, 'Cây cà phê', 'Coffea canephora', 'Hạt thơm · Năng suất', 8000),
(3, 'Cây hồ tiêu', 'Piper nigrum', 'Phù hợp khí hậu Việt', 35000);

INSERT INTO nursery_lots (lot_code, plant_id, area_id, sowed_date, initial_quantity, remaining_quantity, status) VALUES
('LO-01', 1, 1, '2026-05-01', 500, 500, 'ready'),
('LO-02', 3, 2, '2026-05-20', 800, 785, 'growing'),
('LO-03', 2, 1, '2026-05-16', 400, 400, 'ready'),
('LO-04', 4, 2, '2026-06-15', 600, 600, 'growing'),
('LO-05', 4, 3, '2026-07-01', 350, 320, 'needs_check');

-- Xem danh sách lô cùng tuổi cây (tính theo ngày hiện tại):
-- SELECT l.lot_code, p.name, a.name AS area, l.remaining_quantity, l.status,
--        DATEDIFF(CURDATE(), l.sowed_date) AS age_days
-- FROM nursery_lots l JOIN plants p ON p.id=l.plant_id JOIN nursery_areas a ON a.id=l.area_id;
