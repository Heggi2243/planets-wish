<?php
/**
 * 
 * 從API Ninjas抓行星資料並存入資料庫
 * 
 */

require_once 'models/Database.php';
require_once 'models/Planets.php';

// 載入環境變數
if (file_exists(__DIR__ . '/.env')) {
    $env = parse_ini_file(__DIR__ . '/.env');
    foreach ($env as $key => $value) {
        $_ENV[$key] = $value;
    }
}

$apiKey = $_ENV['API_NINJAS_KEY'] ?? '';
if (empty($apiKey)) {
    die("沒找到.env檔案中的API_NINJAS_KEY\n");
}

// 連接資料庫
// try {
    $database = new Database();
    $db = $database->getConnection();
    $planetModel = new Planets($db);
// } catch (Exception $e) {
//     die("資料庫連接失敗: " . $e->getMessage() . "\n");
// }

/**
 * 從API Ninjas抓取行星資料
 *
 * @param string $apiKey API金鑰
 * @param array $params 查詢參數:
 *                      ['name' => 'Mars']
 *                      ['min_mass' => 0.5, 'max_mass' => 2.0]
 *                      ['min_distance_light_year' => 0, 'max_distance_light_year' => 100]
 * @return array 行星資料ary
 */
function fetchPlanetsFromAPI($apiKey, $params)
{
    if (empty($params)) {
        throw new Exception("至少要提供一個查詢參數");
    }

    $url = 'https://api.api-ninjas.com/v1/planets';
    $queryString = http_build_query($params);
    $url .= '?' . $queryString;
    
     // cURL是命令工具，可用於restful API，發送HTTP請求
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);                //要請求的URL
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);     //讓curl_exec()回傳結果而不是直接輸出
    curl_setopt($ch, CURLOPT_HTTPHEADER, [              //HTTP標頭
        'X-Api-Key: ' . $apiKey                         //API Ninjas需要在標頭帶入API Key
    ]);

     // 停用SSL驗證，不然叫不出來
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);


    $response = curl_exec($ch);

    //200=成功, 500=伺服器錯誤
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    if (curl_errno($ch)) {
        $error = curl_error($ch);
        // curl_close($ch);
        throw new Exception("cURL錯誤: $error");
    }

    if ($httpCode !== 200) {
        throw new Exception("API請求失敗 (HTTP $httpCode): $response");
    }

    //轉PHP ary
    return json_decode($response, true);
    
}

/**
 * 撈過的資料
 * 
 */

// 要抓的行星
$queries = [
    // ['min_mass' => 1, 'max_mass' => 2],
    ['min_radius' => 0.01, 'max_radius' => 1],
    // ['max_mass' => 0.05, 'max_radius' => 0.5],
    //  ['min_radius' => 0.5],
    // ['min_radius' => 1.0],
    // ['min_radius' => 1.5],
    
    // 測試高溫
    // ['min_temperature' => 500],
    // ['min_temperature' => 1000],
    // ['min_temperature' => 1500],
    
    // 測試組合
    // ['min_radius' => 0.3, 'max_mass' => 0.1],   //智慧
];

$allPlanets = [];

foreach ($queries as $query) {
    
    try {
        $planets = fetchPlanetsFromAPI($apiKey, $query);
        
        if (!empty($planets)) {
            $allPlanets = array_merge($allPlanets, $planets);
            echo "成功 (找到 " . count($planets) . " 筆)\n";
        } else {
            echo "無資料\n";
        }
        
        usleep(200000); // 延遲 0.2 秒
        
    } catch (Exception $e) {
        echo "失敗: {$e->getMessage()}\n";
    }
}

echo "\n儲存到資料庫...\n";

$result = $planetModel->createBatch($allPlanets);

echo "\n完成!\n";
echo "成功新增: {$result['success']} 筆\n";
echo "已存在跳過: {$result['skipped']} 筆\n";


// php fetch_planets.php