/* =============================================
   อาภรณ์แก้ว — Main JavaScript
   User-facing interactivity
   ============================================= */

// === Utility Functions ===

function showToast(message, type = 'info') {
    const container = document.getElementById('toast-container');
    if (!container) return;
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.textContent = message;
    container.appendChild(toast);
    setTimeout(() => toast.remove(), 3500);
}

function setLoading(btnId, loading) {
    const btn = document.getElementById(btnId);
    if (!btn) return;
    const span = btn.querySelector('span:first-child');
    const loader = btn.querySelector('.btn-loader');
    if (loading) {
        btn.disabled = true;
        if (span) span.style.display = 'none';
        if (loader) loader.style.display = 'inline-block';
    } else {
        btn.disabled = false;
        if (span) span.style.display = 'inline';
        if (loader) loader.style.display = 'none';
    }
}

function formatDateThai(dateStr) {
    if (!dateStr) return '-';
    const d = new Date(dateStr);
    const months = ['ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.',
        'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'];
    return `${d.getDate()} ${months[d.getMonth()]} ${d.getFullYear() + 543}`;
}

function getDepositLabel(type, amount) {
    if (type === 'cash') return `💰 เงินสด ${amount} บาท`;
    return '🪪 บัตรประชาชน';
}

function getStatusLabel(status) {
    const labels = { borrowed: 'ยืมอยู่', returned: 'คืนแล้ว', overdue: 'เกินกำหนด' };
    return labels[status] || status;
}

function getStatusClass(status) {
    const classes = { borrowed: 'status-borrowed', returned: 'status-returned', overdue: 'status-overdue' };
    return classes[status] || '';
}

// === Session Management ===

function checkSession() {
    const user = sessionStorage.getItem('ak_user');
    if (user) {
        const userData = JSON.parse(user);
        showMainMenu(userData);
        return userData;
    }
    return null;
}

function showMainMenu(user) {
    const loginSection = document.getElementById('login-section');
    const menuSection = document.getElementById('menu-section');
    const headerActions = document.getElementById('header-actions');
    const welcomeName = document.getElementById('welcome-name');
    const headerUserName = document.getElementById('header-user-name');

    if (loginSection) loginSection.style.display = 'none';
    if (menuSection) menuSection.style.display = 'block';
    if (headerActions) headerActions.style.display = 'flex';
    if (welcomeName) welcomeName.textContent = user.full_name;
    if (headerUserName) headerUserName.textContent = user.full_name;
}

// === Login ===

async function handleLogin(e) {
    e.preventDefault();
    setLoading('login-btn', true);

    const full_name = document.getElementById('login-name').value.trim();
    const phone = document.getElementById('login-phone').value.trim();

    try {
        const res = await fetch('api/login.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ full_name, phone }),
        });
        const result = await res.json();

        if (result.success) {
            sessionStorage.setItem('ak_user', JSON.stringify(result.user));
            showToast('เข้าสู่ระบบสำเร็จ', 'success');
            showMainMenu(result.user);
        } else {
            showToast(result.error || 'เกิดข้อผิดพลาด', 'error');
        }
    } catch (err) {
        showToast('ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้', 'error');
    }

    setLoading('login-btn', false);
}

function userLogout() {
    sessionStorage.removeItem('ak_user');
    window.location.href = 'index.php';
}

// === Borrow Page ===

let suitsData = [];
let selectedSuit = null;
let selectedSize = null;

async function loadSuits() {
    const grid = document.getElementById('suits-grid');
    if (!grid) return;

    try {
        const res = await fetch('api/get_suits.php');
        const result = await res.json();
        suitsData = result.suits || [];

        if (suitsData.length === 0) {
            grid.innerHTML = '<div class="empty-state"><span>👔</span><p>ยังไม่มีชุดในระบบ</p></div>';
            return;
        }

        grid.innerHTML = suitsData.map(suit => {
            const suitEmojis = ['👔', '👕', '👗', '🎽', '🧥'];
            const emoji = suitEmojis[suit.id % suitEmojis.length];

            const sizesHTML = (suit.stock || []).map(s => {
                const isOut = s.quantity <= 0;
                return `<button type="button" 
          class="size-tag ${isOut ? 'out-of-stock' : ''}" 
          data-suit-id="${suit.id}" 
          data-size="${s.size}" 
          data-qty="${s.quantity}"
          ${isOut ? 'disabled' : ''}
          onclick="selectSize(this, ${suit.id}, '${s.size}')">
          ${s.size} <span class="size-qty">(${s.quantity})</span>
        </button>`;
            }).join('');

            return `
        <div class="suit-card glass-card" id="suit-card-${suit.id}">
          <div class="suit-card-header">
            <span class="suit-emoji">${emoji}</span>
            <span class="suit-code">${suit.suit_code}</span>
          </div>
          <h3>${suit.suit_name}</h3>
          <p>${suit.description || ''}</p>
          <div class="suit-sizes">${sizesHTML}</div>
        </div>
      `;
        }).join('');
    } catch (err) {
        grid.innerHTML = '<div class="empty-state"><span>❌</span><p>ไม่สามารถโหลดข้อมูลได้</p></div>';
    }
}

function selectSize(btn, suitId, size) {
    // Remove previous selection
    document.querySelectorAll('.size-tag.selected').forEach(el => el.classList.remove('selected'));
    btn.classList.add('selected');

    selectedSuit = suitsData.find(s => s.id === suitId || s.id === String(suitId));
    selectedSize = size;

    // Go to step 2
    goToStep(2);
}

function goToStep(step) {
    document.getElementById('step-1').style.display = step === 1 ? 'block' : 'none';
    document.getElementById('step-2').style.display = step === 2 ? 'block' : 'none';

    if (step === 2 && selectedSuit) {
        document.getElementById('borrow-suit-id').value = selectedSuit.id;
        document.getElementById('borrow-size').value = selectedSize;

        const suitInfo = document.getElementById('selected-suit-info');
        suitInfo.innerHTML = `
      <div style="font-size:2rem;">👔</div>
      <div>
        <strong>${selectedSuit.suit_name}</strong>
        <span class="suit-code" style="margin-left:8px;">${selectedSuit.suit_code}</span>
        <br>
        <span style="color:var(--primary-600);font-weight:600;">ไซส์: ${selectedSize}</span>
      </div>
    `;

        // Set min date to today
        const today = new Date().toISOString().split('T')[0];
        const dueDateInput = document.getElementById('borrow-due-date');
        dueDateInput.min = today;
        // Default to 7 days from now
        const defaultDue = new Date();
        defaultDue.setDate(defaultDue.getDate() + 7);
        dueDateInput.value = defaultDue.toISOString().split('T')[0];

        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
}

function prefillUserInfo() {
    const user = sessionStorage.getItem('ak_user');
    if (user) {
        const userData = JSON.parse(user);
        const nameInput = document.getElementById('borrow-name');
        const phoneInput = document.getElementById('borrow-phone');
        const headerName = document.getElementById('header-user-name');
        if (nameInput) nameInput.value = userData.full_name;
        if (phoneInput) phoneInput.value = userData.phone;
        if (headerName) headerName.textContent = userData.full_name;
    }
}

async function submitBorrow(e) {
    e.preventDefault();
    setLoading('borrow-submit-btn', true);

    const form = e.target;
    const data = {
        full_name: form.full_name.value,
        phone: form.phone.value,
        suit_id: form.suit_id.value,
        size: form.size.value,
        due_date: form.due_date.value,
        deposit_type: form.deposit_type.value,
    };

    try {
        const res = await fetch('api/create_borrow.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data),
        });
        const result = await res.json();

        if (result.success) {
            document.getElementById('ref-code').textContent = result.ref_code;
            document.getElementById('success-modal').style.display = 'flex';
        } else {
            showToast(result.error || 'เกิดข้อผิดพลาด', 'error');
        }
    } catch (err) {
        showToast('ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้', 'error');
    }

    setLoading('borrow-submit-btn', false);
}

// === Return Page ===

let currentBorrows = [];

function prefillReturnPhone() {
    const user = sessionStorage.getItem('ak_user');
    if (user) {
        const userData = JSON.parse(user);
        const phoneInput = document.getElementById('return-phone');
        if (phoneInput) phoneInput.value = userData.phone;
    }
}

async function searchBorrows(e) {
    e.preventDefault();
    const phone = document.getElementById('return-phone').value.trim();
    if (!phone) return;

    try {
        const res = await fetch(`api/get_history.php?phone=${encodeURIComponent(phone)}`);
        const result = await res.json();
        const history = result.history || [];

        // Filter only active borrows
        currentBorrows = history.filter(b => b.status === 'borrowed');

        const listSection = document.getElementById('borrow-list-section');
        const list = document.getElementById('borrow-list');
        const count = document.getElementById('borrow-count');

        listSection.style.display = 'block';
        count.textContent = currentBorrows.length;

        if (currentBorrows.length === 0) {
            list.innerHTML = '<div class="empty-state glass-card" style="padding:40px;"><span>📦</span><p>ไม่มีรายการที่ยืมอยู่</p></div>';
            return;
        }

        list.innerHTML = currentBorrows.map(b => `
      <div class="borrow-item glass-card">
        <div class="borrow-item-header">
          <div>
            <h3>${b.suit_name}</h3>
            <span class="suit-code">${b.suit_code}</span>
          </div>
          <span class="status-badge ${getStatusClass(b.status)}">${getStatusLabel(b.status)}</span>
        </div>
        <div class="borrow-item-details">
          <div class="borrow-detail">
            <span class="label">รหัสอ้างอิง</span>
            <span class="value">${b.ref_code}</span>
          </div>
          <div class="borrow-detail">
            <span class="label">ไซส์</span>
            <span class="value">${b.size}</span>
          </div>
          <div class="borrow-detail">
            <span class="label">วันที่ยืม</span>
            <span class="value">${formatDateThai(b.borrow_date)}</span>
          </div>
          <div class="borrow-detail">
            <span class="label">กำหนดคืน</span>
            <span class="value">${formatDateThai(b.due_date)}</span>
          </div>
          <div class="borrow-detail">
            <span class="label">มัดจำ</span>
            <span class="deposit-badge ${b.deposit_type === 'cash' ? 'cash' : 'id-card'}">
              ${getDepositLabel(b.deposit_type, b.deposit_amount)}
            </span>
          </div>
        </div>
        <button class="btn btn-primary btn-block" onclick="openReturnModal(${b.id})">คืนชุดนี้</button>
      </div>
    `).join('');
    } catch (err) {
        showToast('ไม่สามารถโหลดข้อมูลได้', 'error');
    }
}

function openReturnModal(borrowId) {
    const borrow = currentBorrows.find(b => b.id == borrowId);
    if (!borrow) return;

    document.getElementById('return-borrow-id').value = borrowId;
    const info = document.getElementById('return-modal-info');
    info.innerHTML = `
    <div style="margin:16px 0; padding:16px; background:var(--gray-50); border-radius:var(--radius-md); text-align:left;">
      <p><strong>${borrow.suit_name}</strong> (${borrow.suit_code}) — ไซส์ ${borrow.size}</p>
      <p style="margin-top:8px; color:var(--gray-500); font-size:0.85rem;">
        มัดจำ: <strong>${getDepositLabel(borrow.deposit_type, borrow.deposit_amount)}</strong>
      </p>
      <p style="color:var(--success); font-size:0.85rem; margin-top:4px;">
        ✔ ${borrow.deposit_type === 'cash' ? 'คืนเงินมัดจำ ' + borrow.deposit_amount + ' บาท' : 'คืนบัตรประชาชน'}
      </p>
    </div>
  `;

    document.getElementById('return-modal').style.display = 'flex';
}

function closeReturnModal() {
    document.getElementById('return-modal').style.display = 'none';
}

async function submitReturn(e) {
    e.preventDefault();
    setLoading('return-submit-btn', true);

    const borrowId = document.getElementById('return-borrow-id').value;
    const condition = document.querySelector('input[name="condition"]:checked').value;
    const note = document.getElementById('return-note').value;

    const borrow = currentBorrows.find(b => b.id == borrowId);

    try {
        const res = await fetch('api/return_suit.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ borrow_id: borrowId, condition, note }),
        });
        const result = await res.json();

        if (result.success) {
            closeReturnModal();
            const successInfo = document.getElementById('return-success-info');
            successInfo.innerHTML = `
        <p style="margin:12px 0; color:var(--gray-600);">
          ${borrow ? `${borrow.suit_name} — ไซส์ ${borrow.size}` : ''}
        </p>
        <p style="color:var(--success); font-weight:600;">
          ${borrow && borrow.deposit_type === 'cash' ? '💰 คืนเงินมัดจำ ' + borrow.deposit_amount + ' บาท' : '🪪 คืนบัตรประชาชน'}
        </p>
      `;
            document.getElementById('return-success-modal').style.display = 'flex';
        } else {
            showToast(result.error || 'เกิดข้อผิดพลาด', 'error');
        }
    } catch (err) {
        showToast('ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้', 'error');
    }

    setLoading('return-submit-btn', false);
}

// === History Page ===

function prefillHistoryPhone() {
    const user = sessionStorage.getItem('ak_user');
    if (user) {
        const userData = JSON.parse(user);
        const phoneInput = document.getElementById('history-phone');
        if (phoneInput) phoneInput.value = userData.phone;
    }
}

async function searchHistory(e) {
    e.preventDefault();
    const phone = document.getElementById('history-phone').value.trim();
    if (!phone) return;

    try {
        const res = await fetch(`api/get_history.php?phone=${encodeURIComponent(phone)}`);
        const result = await res.json();
        const history = result.history || [];

        const section = document.getElementById('history-section');
        const count = document.getElementById('history-count');
        const tbody = document.getElementById('history-tbody');
        const cards = document.getElementById('history-cards');

        section.style.display = 'block';
        count.textContent = history.length;

        if (history.length === 0) {
            tbody.innerHTML = '<tr><td colspan="8" class="empty-state"><span>📋</span><p>ไม่พบประวัติ</p></td></tr>';
            cards.innerHTML = '<div class="empty-state glass-card" style="padding:40px;"><span>📋</span><p>ไม่พบประวัติ</p></div>';
            return;
        }

        // Desktop table
        tbody.innerHTML = history.map(b => `
      <tr>
        <td><strong>${b.ref_code}</strong></td>
        <td>${b.suit_name}</td>
        <td>${b.size}</td>
        <td>${formatDateThai(b.borrow_date)}</td>
        <td>${formatDateThai(b.due_date)}</td>
        <td>${b.return_date ? formatDateThai(b.return_date) : '-'}</td>
        <td>
          <span class="deposit-badge ${b.deposit_type === 'cash' ? 'cash' : 'id-card'}">
            ${b.deposit_type === 'cash' ? '💰 ' + b.deposit_amount + '฿' : '🪪 บัตร'}
          </span>
        </td>
        <td><span class="status-badge ${getStatusClass(b.status)}">${getStatusLabel(b.status)}</span></td>
      </tr>
    `).join('');

        // Mobile cards
        cards.innerHTML = history.map(b => `
      <div class="history-card glass-card">
        <div class="history-card-header">
          <strong>${b.ref_code}</strong>
          <span class="status-badge ${getStatusClass(b.status)}">${getStatusLabel(b.status)}</span>
        </div>
        <div class="history-card-details">
          <div><span class="label">ชุด</span><br>${b.suit_name} (${b.size})</div>
          <div><span class="label">มัดจำ</span><br>${b.deposit_type === 'cash' ? b.deposit_amount + '฿' : 'บัตร ปชช.'}</div>
          <div><span class="label">วันที่ยืม</span><br>${formatDateThai(b.borrow_date)}</div>
          <div><span class="label">กำหนดคืน</span><br>${formatDateThai(b.due_date)}</div>
        </div>
      </div>
    `).join('');
    } catch (err) {
        showToast('ไม่สามารถโหลดข้อมูลได้', 'error');
    }
}

// === Init ===
document.addEventListener('DOMContentLoaded', () => {
    checkSession();
});
