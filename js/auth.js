/**
 * auth/index.php的JS
 * 處理登入/註冊功能
 */

// DOM Elements
const loginView = document.getElementById('login-view');
const registerView = document.getElementById('register-view');

// 取得根目錄
const BASE_URL = window.location.origin;

console.log('🔧 DEBUG: BASE_URL =', BASE_URL);

// View Switching Logic
function switchView(viewName) {
    const activeView = viewName === 'login' ? registerView : loginView;
    const nextView = viewName === 'login' ? loginView : registerView;

    activeView.style.opacity = '0';
    
    setTimeout(() => {
        activeView.classList.add('hidden');
        nextView.classList.remove('hidden');
        void nextView.offsetWidth;
        nextView.style.opacity = '0';
        
        requestAnimationFrame(() => {
            nextView.style.transition = 'opacity 0.5s ease';
            nextView.style.opacity = '1';
        });
    }, 300);
}

// Handle Login
async function handleLogin(e) {
    e.preventDefault();

    const form = e.target;
    const btn = e.target.querySelector('button[type="submit"]');
    const originalText = btn.innerText;
    const username = form.querySelector('input[type="text"]').value;
    const password = form.querySelector('input[type="password"]').value;

    btn.innerText = '認證中...';
    btn.classList.add('opacity-75', 'cursor-wait');
    btn.disabled = true;

    const apiUrl = `${BASE_URL}/auth/login`; 

    try {
        const response = await fetch(apiUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ username, password })
        });

        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            const text = await response.text();
            console.error('非JSON回應:', text);
            showDetailedError('登入失敗', text);
            throw new Error('伺服器回應不是JSON格式');
        }

        const result = JSON.parse(responseText);

        if (result.success) {
            // 不能直接訪問/views/wish/index.php，會繞過路由導致wishController變數傳不到view
            window.location.href = '/wish';
        } else if (result.needs_verification) {
            showVerificationNeeded(result.email);
        } else {
            alert(result.message || '登入失敗，請檢查帳號密碼');
        }

    } catch (error) {
        console.error('登入錯誤:', error);
        alert('系統錯誤：' + error.message);
    } finally {
        btn.innerText = originalText;
        btn.classList.remove('opacity-75', 'cursor-wait');
        btn.disabled = false;
    }
}

// Handle Register
async function handleRegister(e) {
    e.preventDefault();

    const form = e.target;
    const btn = form.querySelector('button[type="submit"]');
    const originalText = btn.innerText;

    const inputs = form.querySelectorAll('input');
    const username = inputs[0].value;
    const email = inputs[1].value;
    const password = inputs[2].value;
    const confirmPassword = inputs[3].value;

    if (password !== confirmPassword) {
        alert('兩次輸入的密碼不一致！');
        return;
    }

    if (password.length < 6) {
        alert('密碼長度至少需要6個字元');
        return;
    }

    btn.innerText = '處理中...';
    btn.classList.add('opacity-75', 'cursor-wait');
    btn.disabled = true;

    const apiUrl = `${BASE_URL}/auth/register`; 

    try {
        const response = await fetch(apiUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ username, email, password })
        });

        const contentType = response.headers.get('content-type');
        const responseText = await response.text();

        if (!contentType || !contentType.includes('application/json')) {
            showDetailedError('註冊失敗', responseText);
            throw new Error('伺服器回應不是JSON格式');
        }

        const result = JSON.parse(responseText);

        if (result.success) {
            showVerificationModal(result.email || email);
            form.reset();
        } else {
            alert(result.message || '註冊失敗');
        }

    } catch (error) {
        console.error('註冊錯誤:', error);
        alert('系統錯誤：' + error.message);
    } finally {
        btn.innerText = originalText;
        btn.classList.remove('opacity-75', 'cursor-wait');
        btn.disabled = false;
    }
}

// 顯示註冊成功 Modal
function showVerificationModal(email) {
    const existingModal = document.getElementById('verification-modal');
    if (existingModal) existingModal.remove();

    const modal = document.createElement('div');
    modal.id = 'verification-modal';
    modal.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        width: 100vw;
        height: 100vh;
        background: rgba(0, 0, 0, 0.7);
        backdrop-filter: blur(8px);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        padding: 1rem;
    `;
    
    modal.innerHTML = `
        <div class="glass-panel glass-panel-card">
            <h2 class="font-orbitron text-2xl text-white mb-3">註冊成功！</h2>
            <p class="text-white mb-2">請至您的信箱收取驗證信</p>
            <p class="text-sm text-gray-400 mb-6">${escapeHtml(email)}</p>
            <button onclick="closeModal('verification-modal'); switchView('login')" class="btn-primary-gradient">
                返回登入頁
            </button>
        </div>
    `;
    document.body.appendChild(modal);
}

// 顯示需要驗證提示
function showVerificationNeeded(email) {
    const existingModal = document.getElementById('verification-modal');
    if (existingModal) existingModal.remove();

    const modal = document.createElement('div');
    modal.id = 'verification-modal';
    modal.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        width: 100vw;
        height: 100vh;
        background: rgba(0, 0, 0, 0.7);
        backdrop-filter: blur(8px);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        padding: 1rem;
    `;
    
    modal.innerHTML = `
        <div class="glass-panel glass-panel-card">
            <div class="line-neon-top"></div>
            <div class="line-neon-bottom"></div>
            <h2 class="font-orbitron text-2xl text-white mb-3">未完成驗證</h2>
            <p class="text-white mb-2">您的帳號尚未完成Email驗證</p>
            <p class="text-sm text-gray-400 mb-6">請先至信箱完成驗證後再登入</p>
            
            <div class="space-y-3">
                <button onclick="resendVerificationEmail('${escapeHtml(email)}')" class="mb-2 btn-secondary-cyan">
                    重新發送驗證信
                </button>
                <button onclick="closeModal('verification-modal')" class="btn-primary-gradient">
                    關閉
                </button>
            </div>
            <p id="resend-message" class="mt-4 text-white"></p>
        </div>
    `;
    document.body.appendChild(modal);
}

// 關閉 Modal
function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.opacity = '0';
        setTimeout(() => modal.remove(), 300);
    }
}

// 重新發送驗證信
async function resendVerificationEmail(email) {
    const messageEl = document.getElementById('resend-message');
    const btn = event.target;
    const originalText = btn.innerText;
    
    btn.disabled = true;
    btn.innerText = '發送中...';
    messageEl.textContent = '';
    
    try {
        // ✅ 修正：使用路由 URL
        const response = await fetch(`${BASE_URL}/auth/resend-verification`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ email })
        });
        
        const result = JSON.parse(responseText);
        
        messageEl.textContent = result.message;
        messageEl.className = result.success ? 
            'mt-4 text-sm text-green-400' : 
            'mt-4 text-sm text-red-400';
            
    } catch (error) {
        messageEl.textContent = '發送失敗，請稍後再試';
        messageEl.className = 'mt-4 text-sm text-red-400';
    } finally {
        btn.disabled = false;
        btn.innerText = originalText;
    }
}

// 顯示詳細錯誤
function showDetailedError(title, content) {
    const errorDiv = document.createElement('div');
    errorDiv.style.cssText = `
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: white;
        border: 3px solid red;
        padding: 20px;
        max-width: 90%;
        max-height: 80%;
        overflow: auto;
        z-index: 10000;
        box-shadow: 0 4px 20px rgba(0,0,0,0.5);
        font-family: monospace;
        font-size: 12px;
    `;
    
    errorDiv.innerHTML = `
        <div style="color: red; font-weight: bold; font-size: 16px; margin-bottom: 10px;">
            ${escapeHtml(title)}
        </div>
        <pre style="background: #f5f5f5; padding: 10px; border: 1px solid #ddd; white-space: pre-wrap; word-wrap: break-word;">${escapeHtml(content)}</pre>
        <button onclick="this.parentElement.remove()" style="margin-top: 10px; padding: 10px 20px; background: red; color: white; border: none; cursor: pointer; font-weight: bold;">
            關閉
        </button>
    `;
    document.body.appendChild(errorDiv);
}

// HTML 轉義
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// 初始化
document.addEventListener('DOMContentLoaded', function() {
    const style = document.createElement('style');
    style.textContent = `
        @keyframes scale-in {
            from { opacity: 0; transform: scale(0.9); }
            to { opacity: 1; transform: scale(1); }
        }
        .animate-scale-in { animation: scale-in 0.3s ease-out; }
        #verification-modal { transition: opacity 0.3s ease-out; }
    `;
    document.head.appendChild(style);

    loginView.style.transition = 'opacity 0.5s ease';
    registerView.style.transition = 'opacity 0.5s ease';
});