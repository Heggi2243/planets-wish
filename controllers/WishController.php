<?php
namespace controllers;


require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/Users.php';
require_once __DIR__ . '/../models/Planets.php';
require_once __DIR__ . '/../models/Wishes.php';
require_once __DIR__ . '/BaseController.php';

use models\Users;
use models\Planets;
use models\Wishes;

class WishController extends BaseController
{
    private $userModel;
    private $planetModel;
    private $wishModel;

    public function __construct()
    {
        // 檢查是否登入
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/auth');
            exit;
        }

        $this->userModel = new Users();
        $this->planetModel = new Planets();
        $this->wishModel = new Wishes();
    }

    /**
     * 許願首頁（登入後主畫面）
     */
    public function index()
    {
         // 檢查是否已登入
        if (!isset($_SESSION['user_id'])) {
            header('Location: /planets-wish/auth/login');
            exit;
        }

        $userId = $_SESSION['user_id'];

        // 檢查今天是否已許願
        $hasWishedToday = $this->userModel->hasUsedDailySummon($userId);

        // 取得最新的願望(用於倒數計時)
        $latestWish = null;
        if ($hasWishedToday) {
            $latestWish = $this->wishModel->getLatestTodayWish($userId);
        }

        // 取得成功/錯誤訊息: 來自store()
        $successMessage = $_SESSION['success_message'] ?? null;
        $errorMessage = $_SESSION['error_message'] ?? null;
        unset($_SESSION['success_message'], $_SESSION['error_message']);

        $this->view('wish/index', [
            'pageTitle' => 'Planets-Wish | 航空站',
            'hasWishedToday' => $hasWishedToday,
            'latestWish' => $latestWish,
            'successMessage' => $successMessage,
            'errorMessage' => $errorMessage
        ]);
    }

    /**
     * 顯示召喚/許願頁面（GET）
     */
    public function create()
    {
        // 檢查是否點擊召喚
        $showPlanet = isset($_GET['summon']) && $_GET['summon'] === 'true';
        
        $planetData = null;

        $categoryImg = '';
        
        if ($showPlanet) {
            // 隨機召喚行星
            $planetData = $this->summonRandomPlanet();
            // var_dump($planetData['name']);
            $categoryImg = $this->planetModel->getCategoryByKeywords($planetData['keywords']);
            // var_dump($categoryImg);

            // 暫存到 Session（之後提交許願時使用）
            $_SESSION['current_planet'] = $planetData;
        }
        
        $this->view('wish/create', [
            'pageTitle' => 'Planets-Wish | 邂逅行星',
            'showPlanet' => $showPlanet,
            'planetData' => $planetData,
            'categoryImg' =>$categoryImg
        ]);
    }

    /**
     * 處理許願提交（POST）
     */
    public function store()
    {
        // 防呆
        if (!isset($_SESSION['current_planet'])) {
            return $this->json([
                'success' => false,
                'message' => '請先召喚行星'
            ], 400);
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $wishContent = $input['wish_content'] ?? '';

        // 驗證許願內容
        if (empty($wishContent) || mb_strlen($wishContent) > 300) {
            return $this->json([
                'success' => false,
                'message' => '星願內容必須在1-300字之間'
            ], 400);
        }

        $userId = $_SESSION['user_id'];
        $planetData = $_SESSION['current_planet'];

        try {
            // 根據光年計算抵達時間
            $distance = (float) $planetData['distance_ly'];
            $arrivalAt = $this->planetModel->calculateArrivalTime($distance);

            // 建立許願紀錄
            $wishId = $this->wishModel->create(
                $userId,                    
                $planetData['id'],          
                $wishContent,               
                $arrivalAt,               
                $planetData['rpg_type'] 
            );

             if ($wishId) {
                // 7. 更新每日召喚日期
                $this->userModel->updateDailySummonDate($userId);

                // 8. 清除當前行星
                unset($_SESSION['current_planet']);

                // 9. 回傳成功（前端會重導向到 /wish）
                return $this->json([
                    'success' => true,
                    'message' => '願望已送出！行星正在前來的路上...',
                    'wish_id' => $wishId,
                    'arrival_at' => $arrivalAt,
                    'planet_name' => $planetData['name']
                ]);
            } else {
                return $this->json([
                    'success' => false,
                    'message' => '許願失敗，請稍後再試'
                ], 500);
            }

        } catch (\Exception $e) {
            error_log("許願失敗: " . $e->getMessage());
            return $this->json([
                'success' => false,
                'message' => '系統錯誤',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * 許願紀錄頁面
     */
    public function record()
    {
        $userId = $_SESSION['user_id'];
        
        // 取得所有許願紀錄
        $wishes = $this->wishModel->getUserWishes($userId);
        
        $this->view('wish/record', [
            'pageTitle' => 'Planets-Wish | 許願紀錄',
            'wishes' => $wishes
        ]);
    }

    /**
     * 隨機召喚行星（私有方法）
     */
    private function summonRandomPlanet()
    {
        // TODO: 之後改成從資料庫隨機取得
        $planet = $this->planetModel->getRandom();

        
        // // 目前使用假資料
        // $planets = [
        //     [
        //         'id' => 1,
        //         'name' => '紫羅星',
        //         'name_en' => 'Violeta',
        //         'type' => '氣態巨行星',
        //         'image' => '/img/planet-purple.jpg',
        //         'temperature' => '-150°C',
        //         'distance_ly' => 4.2,
        //         'description' => '一顆擁有夢幻紫色大氣層的神秘行星，據說能感應到旅者內心最深處的渴望。',
        //         'suggestion' => '適合許下關於內心成長與自我探索的願望。'
        //     ],
        //     [
        //         'id' => 2,
        //         'name' => '碧海星',
        //         'name_en' => 'Aquamaris',
        //         'type' => '海洋行星',
        //         'image' => '/img/planet-blue.jpg',
        //         'temperature' => '22°C',
        //         'distance_ly' => 8.7,
        //         'description' => '表面被廣闊海洋覆蓋的蔚藍行星，象徵著情感的流動與心靈的平靜。',
        //         'suggestion' => '適合許下關於情感、人際關係與心靈療癒的願望。'
        //     ],
        //     [
        //         'id' => 3,
        //         'name' => '焰陽星',
        //         'name_en' => 'Pyrion',
        //         'type' => '熔岩行星',
        //         'image' => '/img/planet-red.jpg',
        //         'temperature' => '850°C',
        //         'distance_ly' => 12.3,
        //         'description' => '熾熱的熔岩在地表流動，象徵著熱情、勇氣與轉變的力量。',
        //         'suggestion' => '適合許下關於事業突破、勇氣挑戰與人生轉變的願望。'
        //     ]
        // ];
        
        // return $planets[array_rand($planets)];
        return $planet;
    }

    
}