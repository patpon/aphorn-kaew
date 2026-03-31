<?php session_start();
require_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ยืมชุด —
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
                    <h1 class="site-title">ยืมชุด</h1>
                    <p class="site-subtitle">
                        <?= SITE_NAME ?>
                    </p>
                </div>
            </div>
            <div class="header-actions" id="header-actions">
                <span class="user-name" id="header-user-name"></span>
            </div>
        </div>
    </header>

    <main class="main-content">
        <div class="container">
            <!-- Step 1: เลือกชุด -->
            <section class="borrow-step" id="step-1">
                <div class="step-header glass-card">
                    <div class="step-number">1</div>
                    <div>
                        <h2>เลือกชุดที่ต้องการยืม</h2>
                        <p>เลือกชุดและไซส์ที่ต้องการ</p>
                    </div>
                </div>

                <div class="suits-grid" id="suits-grid">
                    <div class="loading-spinner">
                        <div class="spinner"></div>
                        <p>กำลังโหลดข้อมูลชุด...</p>
                    </div>
                </div>
            </section>

            <!-- Step 2: กรอกข้อมูล -->
            <section class="borrow-step" id="step-2" style="display:none;">
                <div class="step-header glass-card">
                    <div class="step-number">2</div>
                    <div>
                        <h2>กรอกข้อมูลการยืม</h2>
                        <p>กรอกรายละเอียดและเลือกประเภทมัดจำ</p>
                    </div>
                </div>

                <div class="selected-suit glass-card" id="selected-suit-info">
                    <!-- จะถูกเติมด้วย JS -->
                </div>

                <form class="borrow-form glass-card" id="borrow-form" onsubmit="submitBorrow(event)">
                    <input type="hidden" id="borrow-suit-id" name="suit_id">
                    <input type="hidden" id="borrow-size" name="size">

                    <div class="form-row">
                        <div class="form-group">
                            <label for="borrow-name">ชื่อ-นามสกุล</label>
                            <input type="text" id="borrow-name" name="full_name" required>
                        </div>
                        <div class="form-group">
                            <label for="borrow-phone">เบอร์โทรศัพท์</label>
                            <input type="tel" id="borrow-phone" name="phone" pattern="[0-9]{9,10}" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="borrow-due-date">กำหนดวันคืน</label>
                        <input type="date" id="borrow-due-date" name="due_date" required>
                    </div>

                    <div class="form-group">
                        <label>ประเภทมัดจำ</label>
                        <div class="deposit-options">
                            <label class="deposit-option" id="deposit-cash-option">
                                <input type="radio" name="deposit_type" value="cash" checked>
                                <div class="deposit-card">
                                    <span class="deposit-icon">💰</span>
                                    <div>
                                        <strong>วางเงินสด</strong>
                                        <span class="deposit-amount">
                                            <?= DEPOSIT_CASH ?> บาท
                                        </span>
                                        <small>คืนเมื่อส่งคืนชุด</small>
                                    </div>
                                </div>
                            </label>
                            <label class="deposit-option" id="deposit-idcard-option">
                                <input type="radio" name="deposit_type" value="id_card">
                                <div class="deposit-card">
                                    <span class="deposit-icon">🪪</span>
                                    <div>
                                        <strong>ฝากบัตรประชาชน</strong>
                                        <span class="deposit-amount">ไม่เสียค่าใช้จ่าย</span>
                                        <small>รับคืนเมื่อส่งชุดคืน</small>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="button" class="btn btn-outline" onclick="goToStep(1)">← ย้อนกลับ</button>
                        <button type="submit" class="btn btn-primary" id="borrow-submit-btn">
                            <span>ยืนยันการยืม</span>
                            <span class="btn-loader" style="display:none;"></span>
                        </button>
                    </div>
                </form>
            </section>
        </div>
    </main>

    <!-- Success Modal -->
    <div class="modal-overlay" id="success-modal" style="display:none;">
        <div class="modal-card glass-card">
            <div class="modal-icon success">✅</div>
            <h2>ยืมชุดสำเร็จ!</h2>
            <p>รหัสอ้างอิงของคุณ</p>
            <div class="ref-code" id="ref-code"></div>
            <p class="modal-note">กรุณาจดรหัสนี้ไว้เพื่อใช้ในการคืนชุด</p>
            <div class="modal-actions">
                <a href="index.php" class="btn btn-primary btn-block">กลับหน้าหลัก</a>
            </div>
        </div>
    </div>

    <div class="toast-container" id="toast-container"></div>

    <script src="assets/js/main.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            loadSuits();
            prefillUserInfo();
        });
    </script>
</body>

</html>