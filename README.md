<img width="1912" height="904" alt="wishHome" src="https://github.com/user-attachments/assets/a432e18e-1d8c-48cd-b33a-9879d8beb749" />

# Planets-Wish (星願) 🌟

一個結合天文資料與RPG遊戲機制的許願收集遊戲，使用PHP+MySQL開發。

## 📖 專案簡介

玩家可以透過許願的方式召喚宇宙中的行星。當行星的屬性與你的願望產生共鳴時，你就能成功收集該行星！


## 🧩 核心玩法

- 🌍 **行星召喚**：每天可進行免費召喚，使用道具可額外召喚
- ✨ **許願系統**：輸入你的願望，系統會分析願望屬性
- 🎯 **屬性匹配**：行星的 RPG 屬性與願望呼應時即可成功收集

## 🛠️ 技術棧

- **後端**：PHP 8
- **資料庫**：MySQL 8
- **架構**：MVC 模式
- **外部 API**：[API Ninjas - Planets API](https://api-ninjas.com/api/planets)
- **CSS 框架**：Tailwind CSS
- **其他工具**： PHPMailer

## 📁 專案結構

```
planets-wish/
├── controllers/           # 控制器
│   ├── AuthController.php
│   ├── HomeController.php
│   └── WishController.php
├── models/               # 模型
│   ├── Database.php
│   ├── Emailservice.php  #處理email相關功能
│   ├── Inventory.php     # 背包(玩家持有道具)
│   ├── Items.php         # 商店商品
│   ├── Planets.php       # 行星
│   ├── Users.php         # 會員
│   └── Wish.php          # 許願紀錄
├── views/                # 視圖
│   ├── welcome.php
│   └── wishCreate.php
├── css/
│   └── input.css
├── src/
│   └── output.css        # Tailwind編譯後的CSS
├── lib/
│   └── PHPMailer         # email發送工具
├── index.php             # 入口檔案
├── config.php            # 設定檔
├── .env                  # 環境變數
└── .gitignore
```

## 🗄️ 資料庫結構

### Users (會員表)

| 欄位 | 類型 | 說明 |
|------|------|------|
| id | INT | 主鍵 |
| username | VARCHAR(50) | 帳號 |
| password_hash | VARCHAR(255) | 加密密碼 |
| email | VARCHAR(100) | 電子郵件 |
| coins | INT | 持有金幣 |
| last_daily_summon_date | DATE | 最後免費召喚日期 |
| email_verified | TINYINT | 郵件驗證狀態 |
| verification_token | VARCHAR(64) | 驗證 Token |
| token_expires_at | DATETIME | Token 過期時間 |
| created_at | DATETIME | 建立時間 |

### Planets (行星表)

| 欄位 | 類型 | 說明 |
|------|------|------|
| id | INT | 主鍵 |
| name | VARCHAR(100) | 行星名稱 |
| rpg_type | ENUM | 屬性分類（力量/敏捷/幸運/智慧） |
| power_stat | INT | 力量值 (0-3) |
| dex_stat | INT | 敏捷值 (0-3) |
| luck_stat | INT | 幸運值 (0-3) |
| intel_stat | INT | 智慧值 (0-3) |
| distance_ly | FLOAT | 距離（光年） |
| description | TEXT | 描述 |

### Items (道具表)

| 欄位 | 類型 | 說明 |
|------|------|------|
| id | INT | 主鍵 |
| name | VARCHAR(100) | 道具名稱 |
| code | VARCHAR(50) | 道具代碼 |
| price | INT | 價格 |
| description | VARCHAR(255) | 描述 |

**預設道具：**
- `summon_ticket` - 召喚行星券（100 金幣）：額外召喚一次
- `specific_ticket` - 指定行星券（300 金幣）：指定召喚特定屬性

### Inventory (背包表)

| 欄位 | 類型 | 說明 |
|------|------|------|
| id | INT | 主鍵 |
| user_id | INT | 使用者 ID |
| item_id | INT | 道具 ID |
| quantity | INT | 數量 |

### Wishes (許願紀錄表)

| 欄位 | 類型 | 說明 |
|------|------|------|
| id | INT | 主鍵 |
| user_id | INT | 使用者 ID |
| planet_id | INT | 行星 ID |
| wish_content | TEXT | 許願內容 |
| status | ENUM | 狀態（traveling/arrived/failed） |
| created_at | DATETIME | 召喚時間 |
| arrival_at | DATETIME | 預計抵達時間 |
| is_success | TINYINT | 是否成功收集 |

## 🎮 遊戲機制

### RPG 屬性計算

行星的 RPG 屬性根據真實天文數據計算：

- **力量** = f(質量)：行星質量越大，力量越高
- **敏捷** = f(公轉週期)：公轉週期越短，敏捷越高
- **幸運** = f(溫度)：溫度相關的隨機屬性
- **智慧** = f(半徑)：行星半徑越大，智慧越高

每個屬性值為 0-3 分，四項屬性總和為 1-8 分。

### 行星抵達時間

行星的抵達時間根據距離計算：
- 計算公式：`距離(光年) × 10 分鐘`
- 最短：10 分鐘
- 最長：60 分鐘

## 🚀 安裝與設定

### 環境需求

- PHP 8.0 或以上
- MySQL 8.0 或以上
- Composer（選用）
- cURL 擴展（用於 API 請求）

## 功能展示
- Email驗證：
<img width="1200" height="824" alt="mail google com_mail_u_0__hl=zh-TW" src="https://github.com/user-attachments/assets/1ac32a13-4214-4fdc-a19a-f019a564f5b7" />


## 📊 開發進度

- [x] 資料庫設計
- [x] 行星資料抓取系統
- [x] RPG 屬性計算邏輯
- [x] phpmailer信箱驗證
- [x] 使用者註冊/登入
- [ ] 許願系統
- [ ] 屬性匹配演算法
- [ ] 道具商店
- [ ] 背包系統
- [ ] 收集紀錄展示


## 🚀 未來展望

- 💰 **金幣系統**：收集行星獲得金幣，可購買道具
- 🎒 **道具系統**：使用道具增加召喚次數或指定行星屬性
