
CREATE DATABASE IF NOT EXISTS `planets_wish` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;


USE `planets_wish`;

-- 建立會員表 (Users)
CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL COMMENT '帳號',
  `password_hash` varchar(255) NOT NULL COMMENT '加密密碼',
  `email` VARCHAR(100) NOT NULL UNIQUE COMMENT '電子郵件地址',
  `coins` int DEFAULT 0 COMMENT '持有金幣',
  `last_daily_summon_date` date DEFAULT NULL COMMENT '最後一次免費召喚日期',
  `email_verified` TINYINT DEFAULT 0 COMMENT '0:未驗證, 1:已驗證',
  `verification_token` VARCHAR(64) DEFAULT NULL COMMENT '驗證 Token',
  `token_expires_at` DATETIME DEFAULT NULL COMMENT 'Token 過期時間',
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 建立行星表 (Planets) 
CREATE TABLE `planets` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL COMMENT '行星名稱',
  `rpg_type` enum('力量','敏捷','幸運','智慧') DEFAULT '幸運' COMMENT '行星屬性分類',
  `power_stat` int DEFAULT 0 COMMENT '力量值',
  `dex_stat` int DEFAULT 0 COMMENT '敏捷/速度值',
  `luck_stat` int DEFAULT 0 COMMENT '幸運值',
  `intel_stat` int DEFAULT 0 COMMENT '智慧值',
  `distance_ly` float DEFAULT 0 COMMENT '距離(光年)',
  `description` text DEFAULT NULL COMMENT '行星描述',
  `suggestion` text DEFAULT NULL COMMENT '許願建議',
  `keywords` varchar(255) DEFAULT NULL,
  `mass` float DEFAULT 0 COMMENT '行星質量',
  `radius` float DEFAULT 0 COMMENT '行星半徑',
  `period` float DEFAULT 0 COMMENT '公轉週期 (天)',
  `temperature` int DEFAULT 0 COMMENT '表面溫度 (K)',
  `semi_major_axis` float DEFAULT 0 COMMENT '軌道半長軸 (AU)',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- 建立道具表 (Items)
CREATE TABLE `items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `code` varchar(50) NOT NULL COMMENT '道具代碼',
  `price` int NOT NULL DEFAULT 0,
  `description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 建立背包表 (Inventory)
CREATE TABLE `inventory` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `item_id` int NOT NULL,
  `quantity` int NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `item_id` (`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb

-- 建立許願/召喚紀錄表 (Wishes)
CREATE TABLE `wishes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `planet_id` int NOT NULL,
  `wish_content` text NOT NULL COMMENT '許願內容',
  `status` ENUM('summoned', 'traveling', 'arrived', 'checked') NOT NULL DEFAULT 'summoned' COMMENT '狀態',
  `created_at` datetime DEFAULT current_timestamp() COMMENT '召喚時間',
  `arrival_at` datetime DEFAULT NULL COMMENT '預計抵達時間',
  `is_success` tinyint DEFAULT 0 COMMENT '屬性是否相符',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `planet_id` (`planet_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



INSERT INTO items (name, code, price, description) VALUES
('召喚行星券', 'summon_ticket', 100, '可以額外召喚一次行星'),
('指定行星券', 'specific_ticket', 300, '可以指定召喚特定屬性的行星');