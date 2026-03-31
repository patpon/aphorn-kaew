<?php session_start();
require_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>คืนชุด —
        <?= SITE_NAME ?>
    </title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <header class="app-header">
        <div class="container">
            <div class="header-brand">
                <a href="index.php" class="back-btn" title="กลับหน้าหลัก">←</a>
                <div>
                    <h1 class="site-title">คืนชุด</h1>
                    <p class="site-subtitle">
                        <?= SITE_NAME ?>
                    </p>
                </div>
            </div>
        </div>
    </header>

    <main class="main-content">
        <div class="container">
            <!-- ค้นหาด้วยเบอร์โทร -->
            <section class="search-section glass-card" id="search-section">
                <h2>🔍 ค้นหารายการยืม</h2>
                <p>กรอกเบอร์โทรศัพท์เพื่อค้นหารายการที่ยืมอยู่</p>
                <form onsubmit="searchBorrows(event)" class="search-form">
                    <div class="search-input-group">
                        <input type="tel" id="return-phone" placeholder="กรอกเบอร์โทรศัพท์" pattern="[0-9]{9,10}"
                            required>
                        <button type="submit" class="btn btn-primary">ค้นหา</button>
                    </div>
                </form>
            </section>

            <!-- รายการที่ยืมอยู่ -->
            <section class="borrow-list-section" id="borrow-list-section" style="display:none;">
                <div class="section-title">
                    <h2>📋 รายการที่ยืมอยู่</h2>
                    <span class="badge" id="borrow-count">0</span>
                </div>
                <div id="borrow-list"></div>
            </section>
        </div>
    </main>

    <!-- Return Modal -->
    <div class="modal-overlay" id="return-modal" style="display:none;">
        <div class="modal-card glass-card">
            <h2>คืนชุด</h2>
            <div id="return-modal-info"></div>
            <form id="return-form" onsubmit="submitReturn(event)">
                <input type="hidden" id="return-borrow-id" name="borrow_id">

                <div class="form-group">
                    <label>สภาพชุดเมื่อคืน</label>
                    <div class="condition-options">
                        <label class="condition-option">
                            <input type="radio" name="condition" value="good" checked>
                            <div class="condition-card">
                                <span>👍</span>
                                <span>ดี</span>
                            </div>
                        </label>
                        <label class="condition-option">
                            <input type="radio" name="condition" value="dirty">
                            <div class="condition-card">
                                <span>🧹</span>
                                <span>สกปรก</span>
                            </div>
                        </label>
                        <label class="condition-option">
                            <input type="radio" name="condition" value="damaged">
                            <div class="condition-card">
                                <span>⚠️</span>
                                <span>เสียหาย</span>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label for="return-note">หมายเหตุ (ถ้ามี)</label>
                    <textarea id="return-note" name="note" rows="3" placeholder="ระบุรายละเอียดเพิ่มเติม..."></textarea>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn btn-outline" onclick="closeReturnModal()">ยกเลิก</button>
                    <button type="submit" class="btn btn-primary" id="return-submit-btn">
                        <span>ยืนยันการคืน</span>
                        <span class="btn-loader" style="display:none;"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Success Modal -->
    <div class="modal-overlay" id="return-success-modal" style="display:none;">
        <div class="modal-card glass-card">
            <div class="modal-icon success">✅</div>
            <h2>คืนชุดสำเร็จ!</h2>
            <div id="return-success-info"></div>
            <div class="modal-actions">
                <a href="index.php" class="btn btn-primary btn-block">กลับหน้าหลัก</a>
            </div>
        </div>
    </div>

    <div class="toast-container" id="toast-container"></div>

    <script src="assets/js/main.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            prefillReturnPhone();
        });
    </script>
</body>

</html>