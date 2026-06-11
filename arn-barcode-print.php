<?php
include 'class/include.php';

if (session_status() === PHP_SESSION_NONE) {
    @session_start();
}

$USER = new User(NULL);
if (!$USER->authenticate()) {
    header('Location: login.php');
    exit();
}

$systemStatus = isset($_SESSION['system_down_status']) ? (int)$_SESSION['system_down_status'] : 0;
if ($systemStatus === 1) {
    header('Location: system-payment-required.php');
    exit();
}

$arn_id = isset($_GET['arn_id']) ? (int)$_GET['arn_id'] : 0;
if ($arn_id <= 0) {
    die('Invalid ARN id.');
}

$ARN = new ArnMaster($arn_id);
if (empty($ARN->id)) {
    die('ARN not found.');
}

$barcodes = ArnItemBarcode::getByArnId($arn_id);
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>ARN Barcodes - <?php echo htmlspecialchars($ARN->arn_no); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            margin: 0;
            padding: 16px;
        }
        .toolbar {
            margin-bottom: 12px;
        }
        .toolbar button {
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            margin-right: 8px;
        }
        .btn-print { background: #2c7be5; color: #fff; }
        .btn-back { background: #6c757d; color: #fff; }
        .sheet {
            background: #fff;
            padding: 10px;
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }
        .barcode-cell {
            width: 60mm;
            min-height: 32mm;
            border: 1px dashed #bbb;
            padding: 4px 6px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            page-break-inside: avoid;
        }
        .barcode-cell .top-line {
            font-weight: bold;
            font-size: 14px;
            letter-spacing: 1px;
        }
        .barcode-cell .arn-line {
            font-size: 12px;
            margin: 2px 0;
            word-break: break-all;
        }
        .barcode-cell .date-line {
            font-size: 11px;
            color: #333;
        }
        .barcode-cell svg {
            max-width: 100%;
            height: 36px;
        }
        @media print {
            body { background: #fff; padding: 0; }
            .toolbar { display: none; }
            .barcode-cell { border: none; }
            .sheet { gap: 0; padding: 0; }
        }
    </style>
</head>
<body>
    <div class="toolbar">
        <button class="btn-print" onclick="window.print()">Print</button>
        <button class="btn-back" onclick="window.close();">Close</button>
        <strong style="margin-left:8px;">
            ARN: <?php echo htmlspecialchars($ARN->arn_no); ?>
            &nbsp;|&nbsp; Total Barcodes: <?php echo count($barcodes); ?>
        </strong>
    </div>

    <div class="sheet">
        <?php if (empty($barcodes)): ?>
            <p style="padding:20px;">No barcodes generated for this ARN.</p>
        <?php else: ?>
            <?php foreach ($barcodes as $i => $bc):
                $topLine = htmlspecialchars($bc['month_letter'] . $bc['year_code']);
                $unitSeq = str_pad((string)$bc['unit_seq'], 2, '0', STR_PAD_LEFT);
                $arnLine = htmlspecialchars($bc['arn_no']);
                $itemLine = htmlspecialchars(($bc['item_code'] ?? '')) . ' - ' . $unitSeq;
                $dateLine = htmlspecialchars($bc['entry_date']);
                $barcodeVal = htmlspecialchars($bc['barcode']);
            ?>
                <div class="barcode-cell">
                    <div class="top-line"><?php echo $topLine; ?></div>
                    <div class="arn-line"><?php echo $arnLine; ?></div>
                    <div class="arn-line"><?php echo $itemLine; ?></div>
                    <svg class="barcode" data-value="<?php echo $barcodeVal; ?>"></svg>
                    <div class="date-line"><?php echo $dateLine; ?></div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <script>
        document.querySelectorAll('svg.barcode').forEach(function (el) {
            try {
                JsBarcode(el, el.getAttribute('data-value'), {
                    format: 'CODE128',
                    displayValue: true,
                    fontSize: 10,
                    height: 36,
                    margin: 2
                });
            } catch (e) {
                console.error('Barcode render error', e);
            }
        });
    </script>
</body>
</html>
