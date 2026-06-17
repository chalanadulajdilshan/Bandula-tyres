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

$US = new User($_SESSION['id']);
$company = new CompanyProfile($US->company_id);

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$BC = new BatteryCharging($id);

if (!$BC->id) {
    echo "Record not found";
    exit;
}

function f($v) { return number_format((float)$v, 2); }
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Battery Charging Bill <?php echo htmlspecialchars($BC->invoice_no); ?></title>
    <style>
        @page { margin: 10mm; }
        * { box-sizing: border-box; }
        body {
            font-family: 'Iskoola Pota', 'Noto Sans Sinhala', Arial, sans-serif;
            color: #000;
            margin: 0;
            padding: 15px;
            font-size: 13px;
        }
        .bill {
            max-width: 700px;
            margin: 0 auto;
            border: 1px solid #000;
            padding: 15px 20px;
        }
        .header { text-align: center; margin-bottom: 8px; }
        .shop-name {
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 4px;
        }
        .branches {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            margin-bottom: 10px;
        }
        .branches > div { width: 48%; }
        .branches p { margin: 1px 0; }
        .date-row {
            text-align: right;
            font-size: 12px;
            margin: 6px 0 10px;
        }
        .field {
            display: flex;
            margin: 5px 0;
            font-size: 13px;
        }
        .field .label {
            min-width: 180px;
            font-weight: 600;
        }
        .field .value {
            flex: 1;
            border-bottom: 1px dotted #555;
            padding-left: 6px;
        }
        .field-sin { font-size: 11px; color: #444; }
        table.items {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
        }
        table.items td, table.items th {
            border: 1px solid #000;
            padding: 5px 8px;
            font-size: 13px;
        }
        table.items th { background: #f3f3f3; }
        .text-end { text-align: right; }
        .signatures {
            display: flex;
            justify-content: space-between;
            margin-top: 35px;
            font-size: 12px;
        }
        .sig-block { width: 40%; text-align: center; border-top: 1px solid #000; padding-top: 3px; }
        .inv-no {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            margin: 8px 0;
        }
        .note {
            text-align: center;
            font-size: 10px;
            color: #555;
            margin-top: 15px;
            border-top: 1px dashed #999;
            padding-top: 6px;
        }
        .print-btn {
            position: fixed;
            top: 10px;
            right: 10px;
        }
        @media print {
            .print-btn { display: none; }
            body { padding: 0; }
            .bill { border: none; }
        }
    </style>
</head>
<body>

<button class="print-btn" onclick="window.print()">Print</button>

<div class="bill">

    <div class="header">
        <div class="shop-name"><?php echo htmlspecialchars($company->name); ?></div>
    </div>

    <div class="branches">
        <div>
            <p><strong>Main Branch</strong></p>
            <p><?php echo htmlspecialchars($company->address ?? ''); ?></p>
            <p>Tel: <?php echo htmlspecialchars($company->phone ?? ''); ?></p>
        </div>
        <div style="text-align:right;">
            <p><strong>Invoice No</strong></p>
            <p class="inv-no" style="margin:0; text-align:right;"><?php echo htmlspecialchars($BC->invoice_no); ?></p>
        </div>
    </div>

    <div class="date-row">
        Date: <?php echo htmlspecialchars($BC->bill_date ?: date('Y-m-d')); ?>
    </div>

    <div class="field">
        <div class="label">Name :</div>
        <div class="value"><?php echo htmlspecialchars($BC->customer_name); ?></div>
    </div>
    <div class="field">
        <div class="label">Address :</div>
        <div class="value"><?php echo htmlspecialchars($BC->address); ?></div>
    </div>

    <div class="field">
        <div class="label">Deposit Amount<br><span class="field-sin">තැන්පත් මුදල</span></div>
        <div class="value"><?php echo f($BC->deposit_amount); ?></div>
    </div>
    <div class="field">
        <div class="label">Loan Hire per Day<br><span class="field-sin">දිනකට ලෝන් මුදල</span></div>
        <div class="value"><?php echo f($BC->loan_hire_per_day); ?></div>
    </div>
    <div class="field">
        <div class="label">The Battery will be Ready on<br><span class="field-sin">බැටරිය නැවත බාරදෙන දිනය</span></div>
        <div class="value"><?php echo htmlspecialchars($BC->ready_date); ?></div>
    </div>

    <table class="items">
        <tr>
            <td style="width:50%;">
                <div><strong>Make :</strong> <?php echo htmlspecialchars($BC->make); ?></div>
            </td>
            <td style="width:25%;">Acid</td>
            <td style="width:25%;" class="text-end"><?php echo f($BC->acid); ?></td>
        </tr>
        <tr>
            <td><strong>Voltage :</strong> <?php echo htmlspecialchars($BC->voltage); ?></td>
            <td>Repairs</td>
            <td class="text-end"><?php echo f($BC->repairs); ?></td>
        </tr>
        <tr>
            <td><strong>Battery No :</strong> <?php echo htmlspecialchars($BC->battery_no); ?></td>
            <td>Charging</td>
            <td class="text-end"><?php echo f($BC->charging); ?></td>
        </tr>
        <tr>
            <td><strong>Loan Battery :</strong> <?php echo htmlspecialchars($BC->loan_battery); ?></td>
            <td><strong>TOTAL</strong></td>
            <td class="text-end"><strong><?php echo f($BC->total); ?></strong></td>
        </tr>
    </table>

    <div class="signatures">
        <div class="sig-block">Dealer</div>
        <div class="sig-block">Customer</div>
    </div>

    <div class="note">
        මෙම රිසිට්පත නොමැතිව බාර දෙනු නොලැබේ.<br>
        මාසයක් තුළ රැගෙන නොයන බැටරි ගැන වගකියනු නොලැබේ.
    </div>

</div>

<script>
    window.onload = function () {
        setTimeout(function(){ window.print(); }, 400);
    };
</script>

</body>
</html>
