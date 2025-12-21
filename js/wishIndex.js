document.addEventListener('DOMContentLoaded', function() {
        const summonBtn = document.getElementById('summon-btn');
        const modal = document.getElementById('daily-limit-modal');
        const closeBtn = document.getElementById('close-modal-btn');
        
        // 點擊傳送門 → 顯示提示
        summonBtn?.addEventListener('click', function() {
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        });
        
        // 關閉 Modal
        function closeModal() {
            modal.classList.add('hidden');
            document.body.style.overflow = '';
        }
        
        closeBtn.addEventListener('click', closeModal);
        modal.addEventListener('click', function(e) {
            if (e.target === modal) closeModal();
        });
    });