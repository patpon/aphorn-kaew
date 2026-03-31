/* =============================================
   อาภรณ์แก้ว — Admin JavaScript
   Admin panel interactivity
   ============================================= */

// === Utility ===

function showToast(message, type = 'info') {
    const container = document.getElementById('toast-container');
    if (!container) return;
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.textContent = message;
    container.appendChild(toast);
    setTimeout(() => toast.remove(), 3500);
}

function formatDateThai(dateStr) {
    if (!dateStr) return '-';
    const d = new Date(dateStr);
    const months = ['ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.',
        'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'];
    return `${d.getDate()} ${months[d.getMonth()]} ${d.getFullYear() + 543}`;
}

function getStatusLabel(status) {
    return { borrowed: 'ยืมอยู่', returned: 'คืนแล้ว', overdue: 'เกินกำหนด' }[status] || status;
}

function getStatusClass(status) {
    return { borrowed: 'status-borrowed', returned: 'status-returned', overdue: 'status-overdue' }[status] || '';
}

function getDepositLabel(type, amount) {
    if (type === 'cash') return `เงินสด ${amount}฿`;
    return 'บัตร ปชช.';
}

// === Sidebar ===

function toggleSidebar() {
    document.getElementById('admin-sidebar').classList.toggle('open');
}

// === Admin Login ===

async function handleAdminLogin(e) {
    e.preventDefault();
    const btn = document.getElementById('admin-login-btn');
    if (btn) btn.disabled = true;

    const username = document.getElementById('admin-username').value.trim();
    const password = document.getElementById('admin-password').value;

    try {
        const res = await fetch('../api/admin_login.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ username, password }),
        });
        const result = await res.json();

        if (result.success) {
            sessionStorage.setItem('ak_admin', JSON.stringify(result.admin));
            showToast('เข้าสู่ระบบสำเร็จ', 'success');
            setTimeout(() => window.location.href = 'dashboard.php', 500);
        } else {
            showToast(result.error || 'เข้าสู่ระบบไม่สำเร็จ', 'error');
        }
    } catch (err) {
        showToast('ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้', 'error');
    }

    if (btn) btn.disabled = false;
}

function adminLogout() {
    sessionStorage.removeItem('ak_admin');
    window.location.href = 'index.php';
}

// === Dashboard ===

async function loadDashboard() {
    updateDateDisplay();

    try {
        const res = await fetch('../api/admin_borrows.php');
        const result = await res.json();

        const stats = result.stats || {};
        document.getElementById('stat-borrowed').textContent = stats.total_borrowed || 0;
        document.getElementById('stat-returned').textContent = stats.total_returned || 0;
        document.getElementById('stat-overdue').textContent = stats.total_overdue || 0;
        document.getElementById('stat-deposit').textContent = stats.deposit_pending || 0;

        // Recent borrows (top 10)
        const borrows = (result.borrows || []).slice(0, 10);
        const tbody = document.getElementById('recent-borrows-tbody');

        if (borrows.length === 0) {
            tbody.innerHTML = '<tr><td colspan="7" style="text-align:center;padding:40px;color:var(--gray-400);">ยังไม่มีรายการ</td></tr>';
            return;
        }

        tbody.innerHTML = borrows.map(b => `
      <tr>
        <td><strong style="color:var(--primary-600);">${b.ref_code}</strong></td>
        <td>${b.full_name}</td>
        <td>${b.suit_name}</td>
        <td>${b.size}</td>
        <td>
          <span class="deposit-badge ${b.deposit_type === 'cash' ? 'cash' : 'id-card'}">
            ${getDepositLabel(b.deposit_type, b.deposit_amount)}
          </span>
        </td>
        <td>${formatDateThai(b.due_date)}</td>
        <td><span class="status-badge ${getStatusClass(b.status)}">${getStatusLabel(b.status)}</span></td>
      </tr>
    `).join('');
    } catch (err) {
        showToast('ไม่สามารถโหลดข้อมูลได้', 'error');
    }
}

function updateDateDisplay() {
    const el = document.getElementById('current-date');
    if (!el) return;
    const now = new Date();
    const thaiDays = ['อาทิตย์', 'จันทร์', 'อังคาร', 'พุธ', 'พฤหัสบดี', 'ศุกร์', 'เสาร์'];
    const months = ['มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน',
        'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'];
    el.textContent = `วัน${thaiDays[now.getDay()]}ที่ ${now.getDate()} ${months[now.getMonth()]} ${now.getFullYear() + 543}`;
}

// === Admin Suits Management ===

async function loadSuitsAdmin() {
    try {
        const res = await fetch('../api/admin_suits.php');
        const result = await res.json();
        const suits = result.suits || [];
        const tbody = document.getElementById('suits-tbody');

        if (suits.length === 0) {
            tbody.innerHTML = '<tr><td colspan="9" style="text-align:center;padding:40px;color:var(--gray-400);">ยังไม่มีชุดในระบบ</td></tr>';
            return;
        }

        tbody.innerHTML = suits.map(suit => {
            // Parse stock_info: "S:5,M:8,L:10,XL:6,XXL:3"
            const stock = {};
            if (suit.stock_info) {
                suit.stock_info.split(',').forEach(item => {
                    const [size, qty] = item.split(':');
                    stock[size] = qty;
                });
            }

            return `
        <tr>
          <td><strong style="color:var(--primary-600);">${suit.suit_code}</strong></td>
          <td>${suit.suit_name}</td>
          <td style="max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">${suit.description || '-'}</td>
          <td>${stock['S'] || 0}</td>
          <td>${stock['M'] || 0}</td>
          <td>${stock['L'] || 0}</td>
          <td>${stock['XL'] || 0}</td>
          <td>${stock['XXL'] || 0}</td>
          <td>
            <div class="action-btns">
              <button class="btn-edit" onclick='editSuit(${JSON.stringify(suit).replace(/'/g, "&#39;")}, ${JSON.stringify(stock).replace(/'/g, "&#39;")})'>✏️ แก้ไข</button>
              <button class="btn-delete" onclick="deleteSuit(${suit.id})">🗑️ ลบ</button>
            </div>
          </td>
        </tr>
      `;
        }).join('');
    } catch (err) {
        showToast('ไม่สามารถโหลดข้อมูลได้', 'error');
    }
}

function openSuitModal() {
    document.getElementById('suit-modal-title').textContent = 'เพิ่มชุดใหม่';
    document.getElementById('suit-form').reset();
    document.getElementById('suit-edit-id').value = '';
    document.getElementById('suit-modal').style.display = 'flex';
}

function closeSuitModal() {
    document.getElementById('suit-modal').style.display = 'none';
}

function editSuit(suit, stock) {
    document.getElementById('suit-modal-title').textContent = 'แก้ไขชุด';
    document.getElementById('suit-edit-id').value = suit.id;
    document.getElementById('suit-code').value = suit.suit_code;
    document.getElementById('suit-name').value = suit.suit_name;
    document.getElementById('suit-desc').value = suit.description || '';

    ['S', 'M', 'L', 'XL', 'XXL'].forEach(size => {
        const input = document.getElementById('stock-' + size);
        if (input) input.value = stock[size] || 0;
    });

    document.getElementById('suit-modal').style.display = 'flex';
}

async function submitSuit(e) {
    e.preventDefault();

    const editId = document.getElementById('suit-edit-id').value;
    const data = {
        suit_code: document.getElementById('suit-code').value.trim(),
        suit_name: document.getElementById('suit-name').value.trim(),
        description: document.getElementById('suit-desc').value.trim(),
        stock: {},
    };

    ['S', 'M', 'L', 'XL', 'XXL'].forEach(size => {
        data.stock[size] = parseInt(document.getElementById('stock-' + size).value) || 0;
    });

    const method = editId ? 'PUT' : 'POST';
    if (editId) data.id = editId;

    try {
        const res = await fetch('../api/admin_suits.php', {
            method,
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data),
        });
        const result = await res.json();

        if (result.success) {
            showToast(result.message, 'success');
            closeSuitModal();
            loadSuitsAdmin();
        } else {
            showToast(result.error || 'เกิดข้อผิดพลาด', 'error');
        }
    } catch (err) {
        showToast('ไม่สามารถบันทึกได้', 'error');
    }
}

async function deleteSuit(id) {
    if (!confirm('ต้องการลบชุดนี้หรือไม่?')) return;

    try {
        const res = await fetch('../api/admin_suits.php', {
            method: 'DELETE',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id }),
        });
        const result = await res.json();

        if (result.success) {
            showToast(result.message, 'success');
            loadSuitsAdmin();
        } else {
            showToast(result.error || 'ไม่สามารถลบได้', 'error');
        }
    } catch (err) {
        showToast('เกิดข้อผิดพลาด', 'error');
    }
}

// === Admin Borrows ===

let searchTimeout;

function debounceSearch() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(loadBorrowsAdmin, 400);
}

async function loadBorrowsAdmin() {
    const search = document.getElementById('borrows-search')?.value || '';
    const status = document.getElementById('borrows-status-filter')?.value || '';

    const params = new URLSearchParams();
    if (search) params.set('search', search);
    if (status) params.set('status', status);

    try {
        const res = await fetch('../api/admin_borrows.php?' + params.toString());
        const result = await res.json();
        const borrows = result.borrows || [];
        const tbody = document.getElementById('borrows-tbody');

        if (borrows.length === 0) {
            tbody.innerHTML = '<tr><td colspan="9" style="text-align:center;padding:40px;color:var(--gray-400);">ไม่พบรายการ</td></tr>';
            return;
        }

        tbody.innerHTML = borrows.map(b => `
      <tr>
        <td><strong style="color:var(--primary-600);">${b.ref_code}</strong></td>
        <td>${b.full_name}</td>
        <td>${b.phone}</td>
        <td>${b.suit_name}</td>
        <td>${b.size}</td>
        <td>
          <span class="deposit-badge ${b.deposit_type === 'cash' ? 'cash' : 'id-card'}">
            ${getDepositLabel(b.deposit_type, b.deposit_amount)}
          </span>
        </td>
        <td>${formatDateThai(b.borrow_date)}</td>
        <td>${formatDateThai(b.due_date)}</td>
        <td><span class="status-badge ${getStatusClass(b.status)}">${getStatusLabel(b.status)}</span></td>
      </tr>
    `).join('');
    } catch (err) {
        showToast('ไม่สามารถโหลดข้อมูลได้', 'error');
    }
}

// === Export CSV ===

function exportCSV() {
    const search = document.getElementById('borrows-search')?.value || '';
    const status = document.getElementById('borrows-status-filter')?.value || '';

    const params = new URLSearchParams();
    if (search) params.set('search', search);
    if (status) params.set('status', status);

    window.location.href = '../api/admin_export.php?' + params.toString();
}

// === Init ===
document.addEventListener('DOMContentLoaded', () => {
    // Close sidebar on outside click (mobile)
    document.addEventListener('click', (e) => {
        const sidebar = document.getElementById('admin-sidebar');
        const hamburger = document.getElementById('hamburger');
        if (sidebar && sidebar.classList.contains('open')) {
            if (!sidebar.contains(e.target) && e.target !== hamburger) {
                sidebar.classList.remove('open');
            }
        }
    });
});
