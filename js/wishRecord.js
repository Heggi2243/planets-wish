
// Mock Data (模擬 MySQL wishes table 資料)
const MOCK_WISHES = [
  {
    id: 1,
    user_id: 101,
    wish_type: '事業',
    wish_content: '希望在年底前能成功轉職到心儀的科技公司，並且薪資漲幅達到 30%。我會持續精進前端技術與演算法能力。',
    status: 'traveling',
    created_at: '2024-03-20 14:30:00'
  },
  {
    id: 2,
    user_id: 101,
    wish_type: '財富',
    wish_content: '希望能達成被動收入大於生活支出，實現初步的財務自由，讓家人過上更穩定的生活。',
    status: 'arrived',
    created_at: '2023-12-05 09:15:00'
  },
  {
    id: 3,
    user_id: 101,
    wish_type: '健康',
    wish_content: '希望全家身體健康，今年能達成每週運動三次的目標，並減重 5 公斤。',
    status: 'traveling',
    created_at: '2024-05-12 21:00:00'
  },
  {
    id: 4,
    user_id: 101,
    wish_type: '感情',
    wish_content: '希望能遇到一個三觀相合、能互相成長的伴侶，一起去世界各地旅行。',
    status: 'failed',
    created_at: '2023-01-10 18:45:00'
  },
  {
    id: 5,
    user_id: 101,
    wish_type: '事業',
    wish_content: '希望創業專案能在三個月內拿到種子輪融資，並吸引優秀的人才加入團隊。',
    status: 'traveling',
    created_at: '2024-02-15 10:20:00'
  },
  {
    id: 6,
    user_id: 101,
    wish_type: '財富',
    wish_content: '希望能買下人生第一套屬於自己的房子，擁有一個溫馨的空間。',
    status: 'arrived',
    created_at: '2023-06-25 15:55:00'
  }
];

const STATUS_CONFIG = {
  traveling: {
    label: '星際航行中',
    color: 'text-cyan-400',
    border: 'from-cyan-500/0 via-cyan-400 to-cyan-500/0',
    glow: 'shadow-[0_0_20px_rgba(34,211,238,0.2)]',
    dot: 'bg-cyan-400 shadow-[0_0_8px_rgba(34,211,238,0.8)]'
  },
  arrived: {
    label: '已抵達星球',
    color: 'text-purple-400',
    border: 'from-purple-500/0 via-purple-400 to-purple-500/0',
    glow: 'shadow-[0_0_20px_rgba(168,85,247,0.2)]',
    dot: 'bg-purple-400 shadow-[0_0_8px_rgba(168,85,247,0.8)]'
  },
  failed: {
    label: '航行失敗',
    color: 'text-gray-400',
    border: 'from-gray-500/0 via-gray-400 to-gray-500/0',
    glow: 'shadow-none',
    dot: 'bg-gray-400'
  }
};

const TYPE_COLORS = {
  '健康': 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30',
  '事業': 'bg-blue-500/20 text-blue-400 border-blue-500/30',
  '財富': 'bg-amber-500/20 text-amber-400 border-amber-500/30',
  '感情': 'bg-rose-500/20 text-rose-400 border-rose-500/30'
};

let currentFilter = 'all';

/**
 * 核心渲染函數
 */
function renderWishes(data) {
  const container = document.getElementById('wishes-container');
  const emptyState = document.getElementById('empty-state');
  
  const filteredData = currentFilter === 'all' 
    ? data 
    : data.filter(w => w.status === currentFilter);

  if (filteredData.length === 0) {
    container.classList.add('hidden');
    emptyState.classList.remove('hidden');
    return;
  }

  container.classList.remove('hidden');
  emptyState.classList.add('hidden');

  container.innerHTML = filteredData.map(wish => {
    const status = STATUS_CONFIG[wish.status];
    const typeStyle = TYPE_COLORS[wish.wish_type];
    const dateStr = wish.created_at.split(' ')[0];

    return `
      <div onclick="window.showWishDetail(${wish.id})" class="relative group cursor-pointer transition-all duration-300 hover:scale-[1.02] active:scale-[0.98]
        bg-white/10 backdrop-blur-lg border border-white/10 rounded-2xl p-6 overflow-hidden flex flex-col gap-4 ${status.glow}">
        
        <!-- Top Neon Line -->
        <div class="absolute top-0 left-0 w-full h-[2px] bg-gradient-to-r ${status.border} opacity-50 group-hover:opacity-100 transition-opacity"></div>
        
        <div class="flex justify-between items-start">
          <span class="px-3 py-1 text-xs font-medium rounded-full border ${typeStyle}">
            ${wish.wish_type}
          </span>
          <span class="text-gray-400 text-xs font-mono">
            ${dateStr}
          </span>
        </div>

        <div class="flex-grow">
          <p class="text-gray-200 text-sm leading-relaxed line-clamp-3">
            ${wish.wish_content}
          </p>
        </div>

        <div class="flex items-center gap-2 pt-2">
          <div class="w-2 h-2 rounded-full ${status.dot} ${wish.status !== 'failed' ? 'animate-pulse' : ''}"></div>
          <span class="text-xs font-bold tracking-wider uppercase ${status.color}">
            ${status.label}
          </span>
        </div>

        <!-- Bottom Neon Line -->
        <div class="absolute bottom-0 left-0 w-full h-[2px] bg-gradient-to-r ${status.border} opacity-50 group-hover:opacity-100 transition-opacity"></div>
      </div>
    `;
  }).join('');
}

/**
 * 彈窗邏輯
 */
// // Fix: Use type assertion on window to satisfy TypeScript compiler when adding dynamic properties
// (window as any).showWishDetail = (id: number) => {
//   const wish = MOCK_WISHES.find(w => w.id === id);
//   if (!wish) return;

//   const modal = document.getElementById('wish-modal');
//   const modalContent = document.getElementById('modal-content');
//   const status = STATUS_CONFIG[wish.status];
//   const typeStyle = TYPE_COLORS[wish.wish_type];

//   // Inject content
//   document.getElementById('modal-accent').className = `h-1 w-full bg-gradient-to-r ${status.border}`;
//   document.getElementById('modal-header-info').innerHTML = `
//     <span class="px-4 py-1.5 text-sm font-bold rounded-full border ${typeStyle}">
//       ${wish.wish_type}
//     </span>
//     <div class="flex items-center gap-2">
//       <div class="w-2 h-2 rounded-full ${status.dot}"></div>
//       <span class="text-xs font-bold ${status.color}">${status.label}</span>
//     </div>
//   `;
//   document.getElementById('modal-date').textContent = `許願日期：${wish.created_at}`;
//   document.getElementById('modal-text').textContent = `「${wish.wish_content}」`;

//   // Show animation
//   modal.classList.remove('hidden');
//   setTimeout(() => {
//     modalContent.classList.remove('scale-95', 'opacity-0');
//     modalContent.classList.add('scale-100', 'opacity-100');
//   }, 10);
// };

function closeModal() {
  const modal = document.getElementById('wish-modal');
  const modalContent = document.getElementById('modal-content');
  
  modalContent.classList.remove('scale-100', 'opacity-100');
  modalContent.classList.add('scale-95', 'opacity-0');
  
  setTimeout(() => {
    modal.classList.add('hidden');
  }, 300);
}

/**
 * 事件監聽初始化
 */
document.addEventListener('DOMContentLoaded', () => {
  // 1. 初始化渲染
  renderWishes(MOCK_WISHES);

  // 2. 濾鏡功能
  const filterBtns = document.querySelectorAll('.filter-btn');
  filterBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      // UI update
      filterBtns.forEach(b => {
        b.classList.remove('bg-white/20', 'border-white/40', 'shadow-lg', 'scale-105');
        b.classList.add('bg-white/5', 'border-white/10', 'text-gray-400');
      });
      btn.classList.add('bg-white/20', 'border-white/40', 'shadow-lg', 'scale-105');
      btn.classList.remove('bg-white/5', 'border-white/10', 'text-gray-400');

      // Logic update
      currentFilter = btn.getAttribute('data-filter');
      renderWishes(MOCK_WISHES);
    });
  });

  // 3. 關閉彈窗
  document.getElementById('close-modal').addEventListener('click', closeModal);
  document.getElementById('close-modal-btn').addEventListener('click', closeModal);
  document.getElementById('modal-backdrop').addEventListener('click', closeModal);
});
