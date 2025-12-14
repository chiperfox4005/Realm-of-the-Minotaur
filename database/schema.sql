-- Realm of the Minotaur - From Scratch
-- MySQL/MariaDB

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('admin','user') NOT NULL DEFAULT 'user',
  gacha_tokens INT NOT NULL DEFAULT 2,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS characters (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL UNIQUE,
  name VARCHAR(80) NOT NULL,
  gender ENUM('Cowok','Cewek') NOT NULL,
  ability ENUM('Attacker','Defender','Healing') NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_char_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS pets (
  id INT AUTO_INCREMENT PRIMARY KEY,
  code VARCHAR(60) NOT NULL UNIQUE,
  name VARCHAR(120) NOT NULL,
  element VARCHAR(80) DEFAULT NULL,
  rarity ENUM('S','A','B','F') NOT NULL DEFAULT 'F',
  image_path VARCHAR(255) NOT NULL,
  lore TEXT,
  weight INT NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS user_pets (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  pet_id INT NOT NULL,
  nickname VARCHAR(120) DEFAULT NULL,
  obtained_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  source ENUM('gacha','admin','other') NOT NULL DEFAULT 'gacha',
  CONSTRAINT fk_up_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_up_pet FOREIGN KEY (pet_id) REFERENCES pets(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Max 2 slot (konsep awal)
CREATE TABLE IF NOT EXISTS user_pet_slots (
  user_id INT NOT NULL,
  slot_no TINYINT NOT NULL,
  user_pet_id INT DEFAULT NULL,
  PRIMARY KEY (user_id, slot_no),
  CONSTRAINT fk_slots_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_slots_userpet FOREIGN KEY (user_pet_id) REFERENCES user_pets(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS gacha_history (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  pet_id INT NOT NULL,
  rolled_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_gh_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_gh_pet FOREIGN KEY (pet_id) REFERENCES pets(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed (akun + pet contoh)
INSERT INTO users (username, password, role, gacha_tokens) VALUES
('admin', 'admin123', 'admin', 999),
('player', 'player123', 'user', 2)
ON DUPLICATE KEY UPDATE username=username;

-- Catatan: image_path ini kamu sesuaikan dengan folder aset kamu.
INSERT INTO pets (code, name, element, rarity, image_path, lore, weight) VALUES
('ox_plain', 'Oxwald the Plain', NULL, 'F', 'image/bateng biasa.jpg', 'Banteng biasa yang tersesat namun berjiwa petarung.', 20),
('vine_ox', 'Vinehorn Orchardbane', 'Alam', 'B', 'image/banteng buah.jpg', 'Penjaga kebun terlarang, membawa energi pemulih.', 8),
('ember_ox', 'Minothorn Emberjaw', 'Api', 'A', 'image/banteng api.jpg', 'Tanduknya menyimpan bara purba.', 3),
('frost_ox', 'Frostmane Auroxveil', 'Es', 'A', 'image/banteng es.jpg', 'Nafasnya membekukan tanah sekitar.', 3),
('gold_ox', 'Nocthorn Dreadcaller', 'Kegelapan', 'S', 'image/banteng emas.jpg', 'Makhluk jurang, memanggil roh banteng.', 2)
ON DUPLICATE KEY UPDATE code=code;
