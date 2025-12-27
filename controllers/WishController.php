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

        // 判斷是否顯示倒數計時用
        $isSummoned = FALSE;

        $userId = $_SESSION['user_id'];

        // 檢查今天是否已許願
        $hasWishedToday = $this->userModel->hasUsedDailySummon($userId);

        // 取得最新的願望(用於倒數計時)
        $latestWish = null;

        if ($hasWishedToday) {
            $latestWish = $this->wishModel->getLatestTodayWish($userId);

            if ($latestWish && $latestWish['status'] === 'summoned') {
                $isSummoned = true;
            }
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
            'errorMessage' => $errorMessage,
            'isSummoned' => $isSummoned
        ]);

    }

    /**
     * 顯示召喚/許願頁面（GET）
     */
    public function create()
    {

        $userId = $_SESSION['user_id'];

        $todayWish = $this->wishModel->getLatestTodayWish($userId);

        if ($todayWish && $todayWish['status'] === 'summoned') {
            // 取得行星資料
            $planetData = $this->planetModel->getById($todayWish['planet_id']);
            $categoryImg = $this->planetModel->getCategoryByKeywords($planetData['keywords']);
            
            // 暫存wish_id和行星資料
            $_SESSION['current_wish'] = [
                'wish_id' => $todayWish['id'],
                'planet_data' => $planetData
            ];
            
            // 顯示許願頁面(已召喚的行星)
            $this->view('wish/create', [
                'pageTitle' => 'Planets-Wish | 邂逅行星',
                'showPlanet' => true,
                'planetData' => $planetData,
                'categoryImg' => $categoryImg
            ]);
            return;
        }
        
        // 如果今天已經送出願望，重導向回wish/index
        if ($todayWish && $todayWish['status'] !== 'summoned') {
            $_SESSION['error_message'] = '今天已經許過願囉！';
            header('Location: /planets-wish/wish');
            exit;
        }

        // 檢查是否點擊召喚
        $showPlanet = isset($_GET['summon']) && $_GET['summon'] === 'true';
        
        $planetData = null;
        $categoryImg = '';
        
        if ($showPlanet) {
            // 隨機召喚行星
            $planetData = $this->planetModel->getRandom($userId);
            $categoryImg = $this->planetModel->getCategoryByKeywords($planetData['keywords']);

            // 暫存到 Session（之後提交許願時使用）
            // $_SESSION['current_planet'] = $planetData;

            // 存入table 
            $wishId = $this->wishModel->createTravelingWish(
                $userId,
                $planetData['id'],
            );

            if (!$wishId) {
                $_SESSION['error_message'] = '召喚失敗，請稍後再試';
                header('Location: /planets-wish/wish');
                exit;
            }
            
            // 更新每日召喚日期
            $this->userModel->updateDailySummonDate($userId);
            
            // 暫存行星資料和wish_id到Session(提交願望時更新)
            $_SESSION['current_wish'] = [
                'wish_id' => $wishId,
                'planet_data' => $planetData
            ];
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


        $input = json_decode(file_get_contents('php://input'), true);
        $wishContent = $input['wish_content'] ?? '';
        

        // 驗證許願內容
        if (empty($wishContent) || mb_strlen($wishContent) > 300) {
            return $this->json([
                'success' => false,
                'message' => '星願內容必須在1-300字之間'
            ], 400);
        }

        $wishData = $_SESSION['current_wish'];
        $wishId = $wishData['wish_id'];
        $planetData = $wishData['planet_data'];

        // $planetData = $_SESSION['current_planet'];

        try {
            // 根據光年計算抵達時間
            $distance = (float) $planetData['distance_ly'];
            $arrivalAt = $this->planetModel->calculateArrivalTime($distance);

            // 更新願望內容+抵達時間+計算成功/失敗
            $updated = $this->wishModel->updateWishContent(
                $wishId,
                $wishContent,
                $arrivalAt,
                $planetData['rpg_type']
            );

             if ($updated) {
                // 清除暫存
                unset($_SESSION['current_wish']);

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
     * 顯示召喚結果頁面
     */
    public function result()
    {
        $userId = $_SESSION['user_id'];
        
        // 取得最新的願望
        $latestWish = $this->wishModel->getLatestTodayWish($userId);
        
        // 檢查是否有願望且已抵達
        if (!$latestWish) {
            $_SESSION['error_message'] = '找不到願望紀錄';
            header('Location: /planets-wish/wish');
            exit;
        }
        
        // 檢查是否已抵達
        $now = new \DateTime();
        $arrivalTime = new \DateTime($latestWish['arrival_at']);
        
        if ($now < $arrivalTime) {
            $_SESSION['error_message'] = '行星還在旅途中，請耐心等待';
            header('Location: /planets-wish/wish');
            exit;
        }
        
        // 取得行星資料
        $planetData = $this->planetModel->getById($latestWish['planet_id']);
        $categoryImg = $this->planetModel->getCategoryByKeywords($planetData['keywords']);
        
        // 更新狀態為 'checked' (已查看)
        $this->wishModel->updateStatus($latestWish['id'], 'checked');
        
        $this->view('wish/result', [
            'pageTitle' => 'Planets-Wish | 召喚結果',
            'wish' => $latestWish,
            'planetData' => $planetData,
            'categoryImg' => $categoryImg
        ]);
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


    
}