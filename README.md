![showing](https://github.com/user-attachments/assets/f6d0937f-86f8-4883-bd4b-6e97abac3710)


# Planets-Wish (星願) 🌟

使用PHP+MySQL開發。一個結合天文資料與許願機制的互動式網頁應用，讓使用者透過召喚真實行星來許願，並透過RPG屬性系統判定願望成功率。

## 📖 特色功能
- 透過API Ninjas取得真實系外行星資料
- 每個行星都有獨特的溫度、距離、質量等天文屬性
- 根據行星特性自動生成RPG屬性(力量、敏捷、幸運、智慧)

## 🎲 判定系統

![getRandom](https://github.com/user-attachments/assets/cdb99029-0db0-4f75-bae0-69402438935a)

![planetArrival](https://github.com/user-attachments/assets/e2fca33f-b8f9-4b85-bdfc-7a1d55dd3574)


### RPG屬性計算
行星的RPG屬性根據真實天文數據計算：
- **力量** = f(質量)：行星質量越大，力量越高
- **敏捷** = f(公轉週期)：公轉週期越短，敏捷越高
- **幸運** = f(溫度)：溫度相關的隨機屬性
- **智慧** = f(半徑)：行星半徑越大，智慧越高

### 許願關鍵字匹配
- 使用者許願內容會與行星屬性進行關鍵字匹配
- 力量型行星：適合「健康」「理想」「突破」等願望
- 敏捷型行星：適合「感情」「生活」「關係」等願望
- 幸運型行星：適合「運勢」「財富」「驚喜」等願望
- 智慧型行星：適合「學習」「考試」「事業」等願望
- 匹配成功率提升，否則基礎成功率30%

## ⏱️ 倒數計時機制
- 根據行星距離(光年)計算抵達時間
- 實時倒數計時顯示
- 倒數結束後自動刷新頁面，顯示召喚結果


## 🎨 星種蒐集系統
<img width="1912" height="1074" alt="wish_record" src="https://github.com/user-attachments/assets/d916daff-07a5-4813-b62b-2185d50765ac" />

- 28種不同的行星圖片類型
- 展示櫃：玩家可查看已蒐集的星種
- 蒐集進度條：顯示蒐集完成度

## 📧 Email驗證系統
- 使用PHPMailer發送驗證信
- 重新發送驗證信功能
- Token24小時過期機制

## 🎮 每日召喚限制
- 每位使用者每天只能免費召喚一次行星
- 召喚後即存入資料庫，避免重新整理刷新


## 🛠️ 技術棧
- **後端**：PHP 8
- **資料庫**：MySQL 8
- **架構**：MVC
- **外部 API**：[API Ninjas - Planets API](https://api-ninjas.com/api/planets)
- **CSS 框架**：Tailwind CSS
- **其他工具**： PHPMailer、API Ninjas - Planets API


## 📁 專案結構
```
planets-wish/
├── config/
│   ├── config.php         # 配置檔案    
│   ├── Database.php       # 資料庫連線類別
├── controllers/           # 控制器
│   ├── AuthController.php # 登入/註冊
│   ├── BaseController.php # 基礎控制器 
│   ├── HomeController.php # 首頁控制器
│   └── WishController.php # 許願控制器
├── models/               # 模型
│   ├── Inventory.php     # 背包(玩家持有道具)
│   ├── Items.php         # 商店
│   ├── Planets.php       # 行星
│   ├── Users.php         # 會員
│   └── Wish.php          # 許願
├── views/                # 視圖
│   ├── auth/             # 登入/註冊
│   └── wish/             # 許願相關頁面
├── css/
│   └── input.css
├── js/
│   ├── auth.js           # 登入/註冊處理
│   └── verify-email.js   # email驗證
├── layouts/              # 版面設置
├── services/
│   └── EmailService.php  # Email驗證信
├── src/
│   └── output.css        # Tailwind編譯後的CSS
├── vendor/
│   └── composer/
│   └── phpmailer/
│   └── autoload.php
├── index.php             # 路由
├── .env                  # 環境變數
└── .gitignore
└── verify-email.php      # Email驗證頁面
└── fetch_planets.php     # 使用API抓取行星資料
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
| suggestion | TEXT | 許願建議 |
| mass | FLOAT | 行星質量 |
| radius | FLOAT | 行星半徑 |
| period | FLOAT | 公轉週期 (天) |
| temperature | INT | 表面溫度 (K) |
| semi_major_axis | FLOAT | 軌道半長軸 (AU) |

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
| status | ENUM | 狀態(traveling/arrived/failed) |
| created_at | DATETIME | 召喚時間 |
| arrival_at | DATETIME | 預計抵達時間 |
| is_success | TINYINT | 是否成功收集 |


## 🚀 環境需求

- PHP 8.0 或以上
- MySQL 8.0 或以上
- Composer（選用）
- cURL擴展（用於 API 請求）

## 功能展示
- Email驗證：
<img width="1200" height="824" alt="mail google com_mail_u_0__hl=zh-TW" src="https://github.com/user-attachments/assets/1ac32a13-4214-4fdc-a19a-f019a564f5b7" />


## 📊 開發進度

- [x] 資料庫設計
- [x] 行星資料抓取系統
- [x] RPG 屬性計算邏輯
- [x] phpmailer信箱驗證
- [x] 使用者註冊/登入
- [x] 許願系統
- [x] 屬性匹配演算法
- [x] 收集紀錄展示


## 🚀 未來展望

- 💰 **金幣系統**：收集行星獲得金幣，可購買道具
- 🎒 **道具系統**：使用道具增加召喚次數或指定行星屬性

## 📄 版權聲明 (Copyright & License)
- 本專案僅供閱覽、學習與技術交流參考，未開放任何形式的授權。
- 未經作者許可，禁止複製、修改、分發或用於任何商業用途。
- All rights reserved. This project is for viewing and educational purposes only. No reuse or redistribution is permitted.
