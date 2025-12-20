<?php
/**
 * Layout 主檔案
 * 
 * 使用方式:
 * 在你的頁面中引入此檔案，並傳入必要的變數
 * 
 * 範例:
 * $pageTitle = '許願頁面';
 * $pageContent = function() {
 *     // 你的頁面內容
 * };
 * require_once __DIR__ . '/../layouts/layout.php';
 */

// 預設值
$pageTitle = $pageTitle ?? 'Planets-Wish | 星願';
$showHeader = $showHeader ?? true;
$showFooter = $showFooter ?? true;
$additionalCSS = $additionalCSS ?? [];
$additionalJS = $additionalJS ?? [];
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    
    <!-- 固定的 CSS -->
    <link rel="stylesheet" href="../css/input.css">
    <link href="../src/output.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&family=Noto+Sans+TC:wght@300;400;700&display=swap" rel="stylesheet">
    
    <!-- 額外的 CSS -->
    <?php foreach ($additionalCSS as $css): ?>
        <link rel="stylesheet" href="<?php echo htmlspecialchars($css); ?>">
    <?php endforeach; ?>
</head>
<body class="min-h-screen flex flex-col relative">
    
    <!-- 星空背景層 -->
    <div class="stars"></div>
    <div class="twinkling"></div>

    <?php if ($showHeader): ?>
        <!-- Header (始終顯示) -->
        <header class="w-full p-6 text-center z-20 relative">
            <h1 class="font-orbitron text-2xl md:text-4xl font-bold tracking-[0.2em] text-transparent bg-clip-text bg-gradient-to-r from-neon-cyan to-neon-purple drop-shadow-[0_0_10px_rgba(0,242,255,0.5)]">
                PLANETS WISH
            </h1>
        </header>
    <?php endif; ?>

    <!-- 主要內容區 -->
    <main class="flex-grow z-10">
        <?php 
        // 執行頁面內容函數
        if (isset($pageContent) && is_callable($pageContent)) {
            $pageContent();
        }
        ?>
    </main>

    <?php if ($showFooter): ?>
        <!-- Footer -->
        <footer class="text-center py-4 text-gray-600 text-xs font-mono relative z-10">
            星願 Planets-Wish © 2025
        </footer>
    <?php endif; ?>

    <!-- 額外的 JS -->
    <?php foreach ($additionalJS as $js): ?>
        <script src="<?php echo htmlspecialchars($js); ?>"></script>
    <?php endforeach; ?>
    
    <!-- 頁面專屬 JS -->
    <?php if (isset($pageScript) && is_callable($pageScript)): ?>
        <script>
            <?php $pageScript(); ?>
        </script>
    <?php endif; ?>
</body>
</html>