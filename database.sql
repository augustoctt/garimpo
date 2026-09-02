CREATE DATABASE IF NOT EXISTS agenda_viva CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE agenda_viva;

CREATE TABLE users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(160) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  reset_token VARCHAR(64) NULL,
  reset_expires_at DATETIME NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE contacts (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NOT NULL,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(160) NULL,
  phone VARCHAR(30) NULL,
  is_supplier BOOLEAN NOT NULL DEFAULT FALSE,
  is_buyer BOOLEAN NOT NULL DEFAULT FALSE,
  notes TEXT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_contacts_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  INDEX idx_contacts_roles (user_id, is_supplier, is_buyer)
);

CREATE TABLE products (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NOT NULL,
  code VARCHAR(20) NOT NULL,
  name VARCHAR(120) NOT NULL,
  category VARCHAR(80) NOT NULL,
  subcategory VARCHAR(80) NOT NULL,
  size VARCHAR(30) NOT NULL,
  condition_grade VARCHAR(40) NOT NULL,
  price DECIMAL(10,2) NOT NULL DEFAULT 0,
  entry_date DATE NOT NULL,
  origin VARCHAR(120) NOT NULL,
  contact_id INT UNSIGNED NULL,
  status ENUM('Disponível','Vendido','Reservado') NOT NULL DEFAULT 'Disponível',
  notes TEXT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_products_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_products_contact FOREIGN KEY (contact_id) REFERENCES contacts(id) ON DELETE SET NULL,
  INDEX idx_products_date (user_id, entry_date),
  INDEX idx_products_status (user_id, status)
);
