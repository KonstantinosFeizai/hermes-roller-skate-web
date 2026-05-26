<?php
// admin/generate_receipt.php  — printable HTML receipt
if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../access_control.php';

restrict_access('admin');

$payment_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$payment_id) die('Μη έγκυρο ID.');

try {
    $stmt = $pdo->prepare("
        SELECT p.*,
               CONCAT(a.first_name, ' ', a.last_name) AS athlete_name,
               a.phone AS athlete_phone,
               loc.name AS location_name
        FROM payments p
        JOIN athletes a   ON p.athlete_id    = a.id
        LEFT JOIN locations loc ON a.location_id = loc.id
        WHERE p.id = ?
    ");
    $stmt->execute([$payment_id]);
    $p = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$p) die('Η πληρωμή δεν βρέθηκε.');
} catch (PDOException $e) {
    die('Σφάλμα βάσης.');
}

$typeLabels   = ['prepaid' => 'Προπληρωμή', 'free' => 'Δωρεάν', 'gift' => 'Δώρο'];
$methodLabels = ['cash' => 'Μετρητά', 'card' => 'Κάρτα', 'transfer' => 'Τραπεζική Μεταφορά', 'other' => 'Άλλο'];
$typeLabel    = $typeLabels[$p['payment_type']]     ?? $p['payment_type'];
$methodLabel  = $methodLabels[$p['payment_method']] ?? $p['payment_method'];
$issueDate    = date('d/m/Y', strtotime($p['payment_date']));
$pricePerLesson = $p['lessons_purchased'] > 0 ? number_format($p['amount'] / $p['lessons_purchased'], 2) : '0.00';

// VAT breakdown (24% included in price)
$vatRate   = 0.24;
$total     = (float)$p['amount'];
$netAmount = $total / (1 + $vatRate);
$vatAmount = $total - $netAmount;
?>
<!DOCTYPE html>
<html lang="el">

<head>
    <meta charset="UTF-8">
    <title>Απόδειξη <?php echo htmlspecialchars($p['receipt_number']); ?></title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #f5f7fb;
            display: flex;
            justify-content: center;
            padding: 40px 20px;
        }

        .receipt {
            background: #fff;
            width: 480px;
            border-radius: 16px;
            box-shadow: 0 4px 24px rgba(0, 0, 0, .12);
            overflow: hidden;
        }

        .receipt-header {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            color: #fff;
            padding: 28px 32px;
        }

        .receipt-header h1 {
            font-size: 1.4rem;
            font-weight: 800;
            letter-spacing: .5px;
        }

        .receipt-header .rn {
            font-size: 0.85rem;
            opacity: .85;
            margin-top: 4px;
        }

        .receipt-body {
            padding: 28px 32px;
        }

        .section {
            margin-bottom: 22px;
        }

        .section-title {
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #64748b;
            margin-bottom: 10px;
            padding-bottom: 6px;
            border-bottom: 1px solid #e2e8f0;
        }

        .row {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            padding: 5px 0;
            font-size: 0.88rem;
        }

        .row .label {
            color: #475569;
        }

        .row .val {
            font-weight: 600;
            color: #1e293b;
        }

        .total-row {
            background: #f0f9ff;
            border-radius: 10px;
            padding: 14px 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 10px;
        }

        .total-row .label {
            font-size: 0.95rem;
            font-weight: 700;
            color: #1e293b;
        }

        .total-row .val {
            font-size: 1.3rem;
            font-weight: 800;
            color: #2563eb;
        }

        .badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
        }

        .badge-prepaid {
            background: #eff6ff;
            color: #2563eb;
        }

        .badge-free {
            background: #f0fdf4;
            color: #16a34a;
        }

        .badge-gift {
            background: #fdf4ff;
            color: #9333ea;
        }

        .receipt-footer {
            background: #f8fafc;
            border-top: 1px solid #e2e8f0;
            padding: 16px 32px;
            font-size: 0.78rem;
            color: #94a3b8;
            text-align: center;
        }

        .print-btn {
            display: block;
            text-align: center;
            margin: 24px auto 0;
            padding: 12px 32px;
            background: #2563eb;
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: 0.95rem;
            font-weight: 700;
            cursor: pointer;
            width: 200px;
        }

        @media print {
            body {
                background: #fff;
                padding: 0;
            }

            .receipt {
                box-shadow: none;
                border-radius: 0;
                width: 100%;
            }

            .print-btn {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="receipt">
        <div class="receipt-header">
            <h1>🛼 Hermes Rollerskate</h1>
            <div class="rn">Απόδειξη <?php echo htmlspecialchars($p['receipt_number']); ?> · <?php echo $issueDate; ?></div>
        </div>

        <div class="receipt-body">
            <div class="section">
                <div class="section-title">Αθλητής</div>
                <div class="row">
                    <span class="label">Ονοματεπώνυμο</span>
                    <span class="val"><?php echo htmlspecialchars($p['athlete_name']); ?></span>
                </div>
                <?php if ($p['location_name']): ?>
                    <div class="row">
                        <span class="label">Τοποθεσία</span>
                        <span class="val"><?php echo htmlspecialchars($p['location_name']); ?></span>
                    </div>
                <?php endif; ?>
            </div>

            <div class="section">
                <div class="section-title">Πακέτο Μαθημάτων</div>
                <div class="row">
                    <span class="label">Αριθμός μαθημάτων</span>
                    <span class="val"><?php echo $p['lessons_purchased']; ?> μαθήματα</span>
                </div>
                <div class="row">
                    <span class="label">Τιμή ανά μάθημα</span>
                    <span class="val"><?php echo $pricePerLesson; ?> €</span>
                </div>
                <div class="row">
                    <span class="label">Τύπος</span>
                    <span class="val"><span class="badge badge-<?php echo $p['payment_type']; ?>"><?php echo $typeLabel; ?></span></span>
                </div>
                <div class="row">
                    <span class="label">Τρόπος πληρωμής</span>
                    <span class="val"><?php echo $methodLabel; ?></span>
                </div>
                <?php if ($p['notes']): ?>
                    <div class="row">
                        <span class="label">Σημειώσεις</span>
                        <span class="val"><?php echo htmlspecialchars($p['notes']); ?></span>
                    </div>
                <?php endif; ?>
            </div>

            <?php if ($p['payment_type'] === 'prepaid'): ?>
                <div class="section">
                    <div class="section-title">Ανάλυση Ποσού</div>
                    <div class="row">
                        <span class="label">Καθαρή Αξία</span>
                        <span class="val"><?php echo number_format($netAmount, 2); ?> €</span>
                    </div>
                    <div class="row">
                        <span class="label">ΦΠΑ (24%)</span>
                        <span class="val"><?php echo number_format($vatAmount, 2); ?> €</span>
                    </div>
                </div>
                <div class="total-row">
                    <span class="label">Συνολικό Ποσό</span>
                    <span class="val"><?php echo number_format($total, 2); ?> €</span>
                </div>
            <?php else: ?>
                <div class="total-row" style="background:#f0fdf4;">
                    <span class="label">Δωρεάν παροχή</span>
                    <span class="val" style="color:#16a34a;">0.00 €</span>
                </div>
            <?php endif; ?>
        </div>

        <div class="receipt-footer">
            Εκδόθηκε: <?php echo date('d/m/Y H:i', strtotime($p['receipt_issued_at'] ?? 'now')); ?> · Hermes Rollerskate
        </div>
    </div>

    <button class="print-btn" onclick="window.print()">🖨️ Εκτύπωση / PDF</button>
</body>

</html>