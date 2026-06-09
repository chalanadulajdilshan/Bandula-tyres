<!doctype html>
<?php
include 'class/include.php';

if (!isset($_SESSION)) {
    session_start();
}

// Require authenticated user
$USER_AUTH = new User(NULL);
if (!$USER_AUTH->authenticate()) {
    header('Location: login.php');
    exit();
}

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

$PURCHASE_ORDER = new PurchaseOrder($id);

if (!$PURCHASE_ORDER->id) {
    echo "Purchase order not found.";
    exit();
}

// Only approved POs can be printed
if ((int) $PURCHASE_ORDER->status !== 1) {
    echo "This purchase order has not been approved yet. Printing is not allowed.";
    exit();
}

$US = new User($_SESSION['id']);
$COMPANY_PROFILE = new CompanyProfile($US->company_id);

$SUPPLIER = new CustomerMaster($PURCHASE_ORDER->supplier_id);
$DEPARTMENT = new DepartmentMaster($PURCHASE_ORDER->department);

$po_date = !empty($PURCHASE_ORDER->order_date) ? date('d/m/Y', strtotime($PURCHASE_ORDER->order_date)) : '';

$PURCHASE_ORDER_ITEM = new PurchaseOrderItem(null);
$po_items = $PURCHASE_ORDER_ITEM->getByPurchaseOrderId($id);
?>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Purchase Order <?php echo htmlspecialchars($PURCHASE_ORDER->po_number); ?> | <?php echo htmlspecialchars($COMPANY_PROFILE->name); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        * { box-sizing: border-box; }
        body { font-family: Arial, Helvetica, sans-serif; color: #000; margin: 0; padding: 0; background: #f5f5f5; }

        .sheet {
            background: #fff;
            width: 210mm;
            min-height: 297mm;
            margin: 20px auto;
            padding: 18mm 16mm;
            box-shadow: 0 0 6px rgba(0, 0, 0, 0.15);
            position: relative;
            overflow: hidden;
        }

        .sheet::before {
            content: "";
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background-image: url('uploads/bglogo.jpeg');
            background-repeat: no-repeat;
            background-position: center center;
            background-size: 60% auto;
            opacity: 0.10;
            z-index: 0;
            pointer-events: none;
        }

        .sheet > * { position: relative; z-index: 1; }

        .toolbar {
            max-width: 210mm;
            margin: 16px auto 0;
            display: flex;
            justify-content: flex-end;
            gap: 8px;
            padding: 0 16mm;
        }
        .toolbar button {
            padding: 6px 14px;
            font-size: 14px;
            border: 1px solid #888;
            background: #fff;
            cursor: pointer;
            border-radius: 3px;
        }
        .toolbar button.print { background: #28a745; color: #fff; border-color: #28a745; }

        .header { text-align: center; position: relative; }
        .header h1 { font-size: 30px; font-weight: 900; letter-spacing: 1px; margin: 0 0 6px 0; text-transform: uppercase; }
        .header .tagline { font-style: italic; font-weight: bold; font-size: 16px; margin: 0 0 4px 0; }
        .header .addr { font-size: 13px; margin: 2px 0; }
        .header .logo-img { position: absolute; right: 0; top: 28px; max-height: 70px; max-width: 130px; }

        .doc-title {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            letter-spacing: 2px;
            margin: 8px 0 6px 0;
            text-transform: uppercase;
        }

        hr.divider { border: none; border-top: 1px solid #000; margin: 10px 0 14px 0; }

        .meta-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 6px;
            font-size: 13px;
        }
        .meta-row .label { font-weight: bold; }
        .meta-row .col { width: 48%; }
        .meta-row .col p { margin: 2px 0; }

        table.items { width: 100%; border-collapse: collapse; margin: 12px 0 16px 0; }
        table.items th { text-align: left; font-size: 14px; padding: 6px 4px; border-bottom: 1px solid #000; }
        table.items td { font-size: 13px; padding: 5px 4px; border-bottom: 1px dotted #000; height: 22px; }
        table.items th.num, table.items td.num { text-align: right; }

        .totals { width: 50%; margin-left: auto; font-size: 14px; }
        .totals .row { display: flex; justify-content: space-between; padding: 3px 0; }
        .totals .row.total { border-top: 1px solid #000; margin-top: 4px; padding-top: 6px; font-weight: bold; }

        .signature-grid {
            display: flex;
            justify-content: space-between;
            margin-top: 60px;
            font-size: 13px;
        }
        .signature-grid .sigbox { width: 30%; text-align: center; }
        .signature-grid .sigline { border-top: 1px solid #000; margin-bottom: 4px; }

        .approved-stamp {
            position: absolute;
            top: 40%;
            right: 14mm;
            transform: rotate(-18deg);
            border: 3px solid #28a745;
            color: #28a745;
            padding: 6px 18px;
            font-size: 22px;
            font-weight: bold;
            letter-spacing: 2px;
            opacity: 0.55;
            border-radius: 6px;
            z-index: 2;
        }

        @media print {
            body { background: #fff; }
            .sheet::before { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .no-print { display: none !important; }
            html, body { height: auto; }
            .sheet { margin: 0; box-shadow: none; width: auto; min-height: 0; height: auto; padding: 10mm 12mm; }
            .header h1 { font-size: 22px; }
            .header .addr { font-size: 11px; }
            .header .logo-img { max-height: 50px; top: 10px; }
            table.items th { font-size: 12px; padding: 3px 4px; }
            table.items td { font-size: 11px; padding: 3px 4px; height: 18px; }
            .totals { font-size: 12px; }
            .signature-grid { margin-top: 30px; font-size: 11px; }
            .approved-stamp { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
    </style>
</head>

<body>

    <div class="toolbar no-print">
        <button class="print" onclick="window.print()">Print</button>
    </div>

    <div class="sheet">

        <div class="approved-stamp">APPROVED</div>

        <div class="header">
            <?php
            $logoPath = 'assets/images/logo.png';
            if (!empty($COMPANY_PROFILE->image_name) && file_exists('uploads/company-logos/' . $COMPANY_PROFILE->image_name)) {
                $logoPath = 'uploads/company-logos/' . $COMPANY_PROFILE->image_name;
            } elseif (file_exists('assets/images/logo.jpg')) {
                $logoPath = 'assets/images/logo.jpg';
            }
            ?>
            <img class="logo-img" src="<?php echo $logoPath; ?>" alt="logo">
            <h1><?php echo htmlspecialchars($COMPANY_PROFILE->name) ?></h1>
            <?php if (property_exists($COMPANY_PROFILE, 'tagline') && !empty($COMPANY_PROFILE->tagline)) { ?>
                <p class="tagline"><?php echo htmlspecialchars($COMPANY_PROFILE->tagline) ?></p>
            <?php } ?>
            <p class="addr"><?php echo htmlspecialchars($COMPANY_PROFILE->address) ?></p>
            <p class="addr">
                Tel :
                <?php
                echo htmlspecialchars($COMPANY_PROFILE->mobile_number_1);
                if (!empty($COMPANY_PROFILE->mobile_number_2)) {
                    echo ' / ' . htmlspecialchars($COMPANY_PROFILE->mobile_number_2);
                }
                ?>
            </p>
        </div>

        <div class="doc-title">Purchase Order</div>
        <hr class="divider">

        <div class="meta-row">
            <div class="col">
                <p><span class="label">To (Supplier):</span> <?php echo htmlspecialchars($SUPPLIER->name); ?></p>
                <p><?php echo nl2br(htmlspecialchars($SUPPLIER->address ?? '')); ?></p>
                <?php if (!empty($SUPPLIER->mobile_number)) { ?>
                    <p>Tel: <?php echo htmlspecialchars($SUPPLIER->mobile_number); ?></p>
                <?php } ?>
            </div>
            <div class="col" style="text-align: right;">
                <p><span class="label">PO No:</span> <?php echo htmlspecialchars($PURCHASE_ORDER->po_number); ?></p>
                <p><span class="label">Date:</span> <?php echo htmlspecialchars($po_date); ?></p>
                <?php if (!empty($DEPARTMENT->name)) { ?>
                    <p><span class="label">Branch:</span> <?php echo htmlspecialchars($DEPARTMENT->name); ?></p>
                <?php } ?>
                <?php if (!empty($PURCHASE_ORDER->invoice_no)) { ?>
                    <p><span class="label">Invoice No:</span> <?php echo htmlspecialchars($PURCHASE_ORDER->invoice_no); ?></p>
                <?php } ?>
            </div>
        </div>

        <p style="margin: 14px 0 6px 0;">Dear Sir / Madam,</p>
        <p style="margin: 0 0 8px 0;">Please supply the following items:</p>

        <table class="items">
            <thead>
                <tr>
                    <th style="width:8%;">No.</th>
                    <th style="width:20%;">Code</th>
                    <th style="width:57%;">Description</th>
                    <th class="num" style="width:15%;">Qty</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($po_items as $key => $row) {
                    $key++;
                    $qty = (float) $row['quantity'];
                    $ITEM = new ItemMaster($row['item_id']);
                ?>
                    <tr>
                        <td><?php echo str_pad($key, 2, '0', STR_PAD_LEFT); ?></td>
                        <td><?php echo htmlspecialchars($ITEM->code); ?></td>
                        <td><?php echo htmlspecialchars($ITEM->name); ?></td>
                        <td class="num"><?php echo rtrim(rtrim(number_format($qty, 2, '.', ''), '0'), '.'); ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <?php if (!empty($PURCHASE_ORDER->remarks)) { ?>
            <p style="margin-top: 18px;"><span class="label" style="font-weight:bold;">Remarks:</span> <?php echo nl2br(htmlspecialchars($PURCHASE_ORDER->remarks)); ?></p>
        <?php } ?>

        <div class="signature-grid">
            <div class="sigbox">
                <div class="sigline"></div>
                Prepared By
            </div>
            <div class="sigbox">
                <div class="sigline"></div>
                Approved By (Head Office)
            </div>
            <div class="sigbox">
                <div class="sigline"></div>
                Authorized Signature
            </div>
        </div>

    </div>

    <script>
        document.addEventListener("keydown", function (e) {
            if (e.key === "Enter") { window.print(); }
        });
    </script>
</body>

</html>
