<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../css/input.css">
    <link href="../src/output.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&family=Noto+Sans+TC:wght@300;400;700&display=swap" rel="stylesheet">
</head>
<body class="min-h-screen flex flex-col relative">
    <div id="astronaut-intro" class="flex flex-col items-center justify-center animate-fade-in">
        <a href="/planets-wish/wish/create?summon=true" class="relative group cursor-pointer">
            <div class="absolute -inset-4 rounded-full blur-xl opacity-75 group-hover:opacity-100 transition duration-500 animate-pulse"></div>
            
            <div class="relative w-64 h-64 md:w-90 md:h-90 transform transition-all duration-500 group-hover:scale-110 group-hover:-translate-y-4">
                <img 
                    src="/img/astronaut03.png" 
                    alt="Astronaut" 
                    class="w-full h-full object-contain drop-shadow-2xl"
                />
            </div>
        </a>
        
        <p class="mt-8 text-gray-400 text-center animate-bounce">
            <span class="block text-lg md:text-xl font-orbitron text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 to-purple-400 font-bold mb-2">
                準備好了嗎？
            </span>
            <span class="text-sm md:text-base">
                點擊太空人，邂逅你的星願
            </span>
        </p>
        
        <div class="absolute top-20 left-1/4 w-2 h-2 bg-yellow-400 rounded-full animate-ping"></div>
        <div class="absolute top-40 right-1/3 w-3 h-3 bg-cyan-400 rounded-full animate-pulse"></div>
        <div class="absolute bottom-32 left-1/3 w-2 h-2 bg-purple-400 rounded-full animate-ping" style="animation-delay: 0.5s;"></div>
    </div>
</body>
</html>