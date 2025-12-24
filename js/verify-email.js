/**
 * verify-email.php的JS
 * 重新發送驗證信
 */

const BASE_URL = window.location.origin;

function showResendForm() {
    document.getElementById('action-buttons').classList.add('hidden');
    document.getElementById('resend-form').classList.remove('hidden');
    document.querySelector('#failMsg h2').textContent = "重新驗證信箱";
    document.querySelector('#failMsg p').classList.add('hidden');
}


async function resendVerification(e) {
    e.preventDefault();
    
    const email = document.getElementById('resend-email').value;
    const btn = e.target.querySelector('button');
    const message = document.getElementById('resend-message');
    
    btn.disabled = true;
    btn.innerText = '發送中...';
    message.textContent = '';
    
    try {
        const response = await fetch(`${BASE_URL}/controllers/AuthController.php?action=resend-verification`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ email })
        });
        
        const result = JSON.parse(text);
        
        message.textContent = result.message;
        message.className = result.success ? 
            'mt-4 text-sm text-green-400' : 
            'mt-4 text-sm text-red-400';
            
        if (result.success) {
            e.target.reset();
        }
        
    } catch (error) {
        message.textContent = '發送失敗，請稍後再試';
        message.className = 'mt-4 text-sm text-red-400';
    } finally {
        btn.disabled = false;
        btn.innerText = '發送驗證信';
    }
}