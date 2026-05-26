<!doctype html>
<?php
include 'class/include.php';

if (!isset($_SESSION)) {
    session_start();
}

$invoice_param = $_GET['invoice_no'];
$US = new User($_SESSION['id']);
$COMPANY_PROFILE = new CompanyProfile($US->company_id);

// Handle both invoice ID and invoice number
if (is_numeric($invoice_param)) {
    // It's an ID - use it directly
    $SALES_INVOICE = new SalesInvoice($invoice_param);
    $invoice_id = $invoice_param;
} else {
    // It's an invoice number - look it up
    $SALES_INVOICE_TEMP = new SalesInvoice(null);
    $invoice_data = $SALES_INVOICE_TEMP->getInvoiceByNo($invoice_param);

    if ($invoice_data) {
        $SALES_INVOICE = new SalesInvoice($invoice_data['id']);
        $invoice_id = $invoice_data['id'];
    } else {
        die('Invoice not found: ' . $invoice_param);
    }
}

// Verify invoice exists
if (!$SALES_INVOICE->id) {
    die('Invoice not found');
}

$COMPANY_PROFILE = new CompanyProfile($SALES_INVOICE->company_id);
$CUSTOMER_MASTER = new CustomerMaster($SALES_INVOICE->customer_id);
$INVOICE_BRANCH = new DepartmentMaster($SALES_INVOICE->department_id);

// Generate public PDF URL
$pdfBaseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
$pdfUrl = $pdfBaseUrl . $_SERVER['REQUEST_URI'];
$pdfUrl = preg_replace('/\?.*/', '', $pdfUrl); // Remove existing query parameters
$pdfUrl .= '?pdf=1&invoice_no=' . urlencode($SALES_INVOICE->invoice_no);

// Get customer mobile number for WhatsApp
$customerMobile = !empty($SALES_INVOICE->customer_mobile) ? $SALES_INVOICE->customer_mobile : '';
if (!empty($customerMobile)) {
    // Remove all non-numeric characters
    $customerMobile = preg_replace('/\D/', '', $customerMobile);
    // Add country code if not present (assuming Sri Lanka +94 if 10 digits)
    if (strlen($customerMobile) == 10) {
        $customerMobile = '94' . substr($customerMobile, 1);
    }
}
?>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Invoice Details </title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include 'main-css.php' ?>
    <link href="https://unicons.iconscout.com/release/v4.0.8/css/line.css" rel="stylesheet">

    <style>
        @media print {
            .no-print { display: none !important; }
            body, html { width: 100%; margin: 0; padding: 0; }
            #invoice-content, .card {
                width: 100% !important;
                max-width: 100% !important;
                box-shadow: none;
                border: none !important;
                page-break-inside: avoid;
                break-inside: avoid;
            }
            .container { width: 100% !important; max-width: 100% !important; padding: 0 !important; }
            @page { size: A4 portrait; margin: 10mm; }
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }

            /* Layout tweaks for A4 — comfortable sizing, still fits one page */
            .card-body { padding: 12px !important; }
            .bt-title-en { font-size: 24px !important; }
            .bt-title-si { font-size: 16px !important; }
            .bt-tagline, .bt-brands { font-size: 12px !important; margin-bottom: 3px !important; }
            .bt-addr-table { font-size: 11px !important; line-height: 1.4 !important; }
            .bt-meta { font-size: 12px !important; margin-top: 6px !important; }
            .bt-ms { font-size: 12px !important; margin-top: 6px !important; }
            .bt-ms .dots { height: 16px !important; margin-bottom: 2px !important; }
            .bt-items { font-size: 12px !important; margin-top: 8px !important; }
            .bt-items th, .bt-items td { padding: 3px 5px !important; }
            /* Spacer rows — comfortable for A4 */
            .bt-items tbody tr.spacer td { height: 18px !important; }
            .bt-foot-table { font-size: 11px !important; margin-top: 6px !important; }
            .bt-foot-line { min-width: 150px !important; }
            .bt-total-box { font-size: 15px !important; padding: 5px 8px !important; }
            .bt-footer-tag { font-size: 14px !important; margin-top: 8px !important; padding-top: 6px !important; }
        }
        /* Landscape override – applied when <html> has class 'print-landscape' */
        html.print-landscape {
            --page-orientation: landscape;
        }
        @media print {
            html.print-landscape body,
            html.print-landscape html { width: 100%; }
        }
        html.print-landscape { }
        /* We inject a dynamic <style> tag via JS to override @page */

        /* Toggle button styles */
        .orientation-btn {
            border: 1px solid #6c757d;
            background: #fff;
            color: #6c757d;
            padding: 4px 12px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 13px;
            transition: background 0.15s, color 0.15s;
        }
        .orientation-btn.active {
            background: #495057;
            color: #fff;
            border-color: #495057;
        }

        #invoice-content {
            color: #b40000;
            font-family: "Arial", "Helvetica", sans-serif;
        }

        .bt-title {
            text-align: center;
            font-weight: 900;
            letter-spacing: 1px;
            margin: 0;
            line-height: 1.1;
        }
        .bt-title-en { font-size: 26px; }
        .bt-title-si { font-size: 18px; font-family: "Iskoola Pota", "Noto Sans Sinhala", sans-serif; }
        .bt-tagline { text-align: center; font-size: 13px; font-weight: 700; margin: 2px 0 0 0; }
        .bt-brands  { text-align: center; font-size: 13px; font-weight: 700; margin: 0 0 8px 0; }

        .bt-addr-table { width: 100%; font-size: 12px; line-height: 1.45; }
        .bt-addr-table td { vertical-align: top; padding: 0 4px; }
        .bt-addr-table td.right { text-align: right; }

        .bt-meta { text-align: right; font-size: 13px; margin-top: 6px; }
        .bt-meta .line { display: inline-block; border-bottom: 1px solid #b40000; min-width: 110px; padding: 0 6px; }

        .bt-ms { margin-top: 6px; font-size: 13px; }
        .bt-ms .dots {
            display: block;
            border-bottom: 1px solid #b40000;
            height: 18px;
            margin-bottom: 2px;
        }

        .bt-items { width: 100%; border-collapse: collapse; margin-top: 8px; font-size: 13px; }
        .bt-items th, .bt-items td {
            border: 1px solid #b40000;
            padding: 4px 6px;
            color: #b40000;
        }
        .bt-items th { font-weight: 700; text-align: center; background: #fff; }
        .bt-items td.num { text-align: right; }
        .bt-items td.c   { text-align: center; }

        .bt-items .col-qty   { width: 8%;  text-align: center; }
        .bt-items .col-desc  { width: 42%; }
        .bt-items .col-volt  { width: 8%;  text-align: center; }
        .bt-items .col-amp   { width: 8%;  text-align: center; }
        .bt-items .col-rs    { width: 22%; text-align: right; }
        .bt-items .col-cts   { width: 12%; text-align: right; }

        .bt-items tbody tr.spacer td { height: 22px; border-left: 1px solid #b40000; border-right: 1px solid #b40000; border-top: none; border-bottom: none; }
        .bt-items tbody tr.spacer td.with-bottom { border-bottom: 1px solid #b40000; }

        .bt-foot-table { width: 100%; font-size: 12px; margin-top: 6px; }
        .bt-foot-table td { vertical-align: top; padding: 2px 4px; }
        .bt-foot-label { white-space: nowrap; font-weight: 700; }
        .bt-foot-line { display: inline-block; border-bottom: 1px solid #b40000; min-width: 180px; padding: 0 4px; }

        .bt-total-box {
            border: 1px solid #b40000;
            text-align: center;
            font-weight: 900;
            font-size: 16px;
            padding: 6px 10px;
        }

        .bt-footer-tag {
            text-align: center;
            font-weight: 900;
            font-size: 16px;
            margin-top: 10px;
            border-top: 1px solid #b40000;
            padding-top: 6px;
        }
    </style>

</head>

<body data-layout="horizontal" data-topbar="colored">

    <div class="container mt-4">
        <div
            class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-3 no-print gap-2">
            <h4 class="mb-0">Invoice</h4>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="" id="toggleOutstanding" <?php echo ($SALES_INVOICE->payment_type == 2) ? 'checked' : 'disabled'; ?>>
                    <label class="form-check-label" for="toggleOutstanding">
                        Show customer outstanding
                    </label>
                </div>
                <!-- Orientation toggle -->
                <div class="d-flex align-items-center gap-1 ms-2" title="Select print / PDF orientation">
                    <button id="btn-portrait" class="orientation-btn active" onclick="setOrientation('portrait')" title="Portrait">
                        &#x1F4C4; Portrait
                    </button>
                    <button id="btn-landscape" class="orientation-btn" onclick="setOrientation('landscape')" title="Landscape">
                        &#x1F5C3; Landscape
                    </button>
                </div>
                <button onclick="window.print()" class="btn btn-success ms-2">Print</button>
                <button onclick="downloadPDF()" class="btn btn-primary ms-2">PDF</button>
                <button onclick="shareViaWhatsApp()" class="btn btn-success ms-2 no-print">
                    <i class="uil uil-whatsapp"></i> WhatsApp
                </button>
            </div>
        </div>

        <?php
        function formatPhone($number)
        {
            $number = preg_replace('/\D/', '', $number);
            if (strlen($number) == 10) {
                return sprintf("(%s) %s-%s", substr($number, 0, 3), substr($number, 3, 3), substr($number, 6));
            }
            return $number;
        }

        // Pre-compute items + totals so we can render the receipt-style table
        $TEMP_SALES_ITEM = new SalesInvoiceItem(null);
        $temp_items_list = $SALES_INVOICE->invoice_type == 'INV'
            ? $TEMP_SALES_ITEM->getItemsByInvoiceId($invoice_id)
            : [];
        $print_subtotal = 0;
        foreach ($temp_items_list as $ti) {
            $print_subtotal += floatval($ti['price']) * (int)$ti['quantity'];
        }
        $print_grand = floatval($SALES_INVOICE->grand_total);
        if ($print_grand <= 0) {
            $print_grand = $print_subtotal;
        }
        $print_rs  = floor($print_grand);
        $print_cts = round(($print_grand - $print_rs) * 100);

        // Pull Telephone / Battery / Vehicle numbers if present on the invoice
        $tel_no     = isset($SALES_INVOICE->customer_mobile) ? $SALES_INVOICE->customer_mobile : '';
        $vehicle_no = isset($SALES_INVOICE->vehicle_no) ? $SALES_INVOICE->vehicle_no : '';
        $battery_no = '';
        foreach ($temp_items_list as $ti) {
            if (!empty($ti['serial_no'])) {
                $battery_no = $ti['serial_no'];
                break;
            }
        }

        $branch_name = strtolower($INVOICE_BRANCH->name ?? '');
        $is_mt_lavinia = strpos($branch_name, 'lavinia') !== false || strpos($branch_name, 'mount') !== false;
        ?>

        <div class="card" id="invoice-content">
            <div class="card-body">
                <?php
                $logoPath = 'assets/images/logo.png';
                if (!empty($COMPANY_PROFILE->image_name) && file_exists('uploads/company-logos/' . $COMPANY_PROFILE->image_name)) {
                    $logoPath = 'uploads/company-logos/' . $COMPANY_PROFILE->image_name;
                } elseif (file_exists('assets/images/logo.jpg')) {
                    $logoPath = 'assets/images/logo.jpg';
                }
                $vat_no = '';
                if (!empty($SALES_INVOICE->customer_id)) {
                    $vat_no = $CUSTOMER_MASTER->vat_no ?? '';
                }
                $customerEmail = $CUSTOMER_MASTER->email ?? '';
                ?>

                <!-- Branded header with logo (left) and title (centered) -->
                <table style="width:100%; margin-bottom:6px; border-collapse:collapse;">
                    <tr>
                        <td style="width:90px; vertical-align:middle;">
                            <img src="<?php echo $logoPath; ?>" alt="logo" style="max-height:75px; max-width:90px; object-fit:contain;">
                        </td>
                        <td style="text-align:center; vertical-align:middle;">
                            <h2 class="bt-title bt-title-en">BANDULA BATTERY SALES &amp; SERVICE</h2>
                            <h3 class="bt-title bt-title-si">බන්දුල බැටරි සේල්ස් ඇන්ඩ් සර්විස්</h3>
                            <p class="bt-tagline" style="margin-top:3px;">Dealers in all kinds of Local and Imported Batteries</p>
                        </td>
                        <td style="width:90px;"></td>
                    </tr>
                </table>

                <table class="bt-addr-table">
                    <tr>
                        <td<?php echo $is_mt_lavinia ? '' : ' style="font-weight:700;"'; ?>>
                            No. 1 &amp; 5, Old Galle Road,<br>
                            Moratuwa.<br>
                            Tel : 2641053, 2644791, 4374430-31<br>
                            Mobile : 0772538789<br>
                            Email : bandulabattery@gmail.com
                        </td>
                        <td class="right"<?php echo $is_mt_lavinia ? ' style="font-weight:700;"' : ''; ?>>
                            Mount Lavinia Branch<br>
                            No. 221, Templar's Road,<br>
                            Mount Lavinia<br>
                            Tel : 011 2738074
                        </td>
                    </tr>
                </table>

                <!-- Bill To grid: customer details (left) + invoice meta (right) -->
                <table style="width:100%; margin-top:8px; font-size:13px; border-collapse:collapse;">
                    <tr>
                        <td style="width:60%; vertical-align:top; padding-right:12px;">
                            <div><strong>Bill To :</strong>
                                <span style="display:inline-block;min-width:60%;border-bottom:1px solid #b40000;padding:0 4px;">
                                    <?php echo htmlspecialchars($SALES_INVOICE->customer_name); ?>
                                </span>
                            </div>
                            <div style="margin-top:3px;"><strong>Mr. :</strong>
                                <span style="display:inline-block;min-width:60%;border-bottom:1px solid #b40000;padding:0 4px;">
                                    <?php echo htmlspecialchars($SALES_INVOICE->customer_address ?? ''); ?>
                                </span>
                            </div>
                            <div style="margin-top:3px;"><strong>Mobile :</strong>
                                <span style="display:inline-block;min-width:58%;border-bottom:1px solid #b40000;padding:0 4px;">
                                    <?php echo htmlspecialchars($SALES_INVOICE->customer_mobile ?? ''); ?>
                                </span>
                            </div>
                            <div style="margin-top:3px;"><strong>Email :</strong>
                                <span style="display:inline-block;min-width:60%;border-bottom:1px solid #b40000;padding:0 4px;">
                                    <?php echo htmlspecialchars($customerEmail); ?>
                                </span>
                            </div>
                            <div style="margin-top:3px;"><strong>Vehicle No :</strong>
                                <span style="display:inline-block;min-width:53%;border-bottom:1px solid #b40000;padding:0 4px;">
                                    <?php echo htmlspecialchars($SALES_INVOICE->vehicle_no ?? ''); ?>
                                </span>
                            </div>
                        </td>
                        <td style="width:40%; vertical-align:top;">
                            <div><strong>VAT No :</strong>
                                <span style="display:inline-block;min-width:55%;border-bottom:1px solid #b40000;padding:0 4px;">
                                    <?php echo htmlspecialchars($vat_no); ?>
                                </span>
                            </div>
                            <div style="margin-top:3px;"><strong>Invoice No :</strong>
                                <span style="display:inline-block;min-width:50%;border-bottom:1px solid #b40000;padding:0 4px;font-weight:700;">
                                    <?php echo htmlspecialchars($SALES_INVOICE->invoice_no); ?>
                                </span>
                            </div>
                            <div style="margin-top:3px;"><strong>Date :</strong>
                                <span style="display:inline-block;min-width:60%;border-bottom:1px solid #b40000;padding:0 4px;">
                                    <?php echo date('d/m/Y', strtotime($SALES_INVOICE->invoice_date)); ?>
                                </span>
                            </div>
                            <div style="margin-top:3px;"><strong>PO No :</strong>
                                <span style="display:inline-block;min-width:58%;border-bottom:1px solid #b40000;padding:0 4px;">
                                </span>
                            </div>
                            <div style="margin-top:3px;"><strong>Billed Department :</strong>
                                <span style="display:inline-block;min-width:42%;border-bottom:1px solid #b40000;padding:0 4px;">
                                    <?php echo htmlspecialchars($INVOICE_BRANCH->name ?? ''); ?>
                                </span>
                            </div>
                            <?php
                            $invoiced_user_name = '';
                            if (!empty($SALES_INVOICE->created_by)) {
                                $INVOICE_USER = new User($SALES_INVOICE->created_by);
                                $invoiced_user_name = $INVOICE_USER->name ?? '';
                            }
                            ?>
                            <div style="margin-top:3px;"><strong>Invoiced User :</strong>
                                <span style="display:inline-block;min-width:48%;border-bottom:1px solid #b40000;padding:0 4px;">
                                    <?php echo htmlspecialchars($invoiced_user_name); ?>
                                </span>
                            </div>
                            <?php if ($SALES_INVOICE->payment_type == 2 && $SALES_INVOICE->credit_period): ?>
                                <?php $CP = new CreditPeriod($SALES_INVOICE->credit_period); ?>
                                <div style="margin-top:3px;"><strong>Due Date :</strong>
                                    <span style="display:inline-block;min-width:53%;border-bottom:1px solid #b40000;padding:0 4px;">
                                        <?php echo date('d/m/Y', strtotime($SALES_INVOICE->due_date)); ?>
                                    </span>
                                </div>
                            <?php endif; ?>
                        </td>
                    </tr>
                </table>

                <!-- Items table: Qty | Description | Item | Volt | Ah. | Rs. -->
                <table class="bt-items" style="margin-top:8px;">
                    <thead>
                        <tr>
                            <th style="width:8%;">Qty</th>
                            <th style="width:36%;">Description</th>
                            <th style="width:18%;">Item</th>
                            <th style="width:8%;">Volt</th>
                            <th style="width:10%;">Ah.</th>
                            <th style="width:20%;">Rs.</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $rendered_rows = 0;
                        $sub_total = 0;
                        foreach ($temp_items_list as $ti) {
                            $qty   = (int) $ti['quantity'];
                            $price = floatval($ti['price']);
                            $line  = $price * $qty;
                            $sub_total += $line;

                            $volt = $ti['volt'] ?? ($ti['voltage'] ?? '');
                            $amp  = $ti['amp']  ?? ($ti['ampere']  ?? '');
                            $itemCode = $ti['item_code_name'] ?? ($ti['item_code'] ?? '');
                            $desc     = $ti['display_name'] ?? '';
                            if (!empty($ti['serial_no'])) {
                                $desc .= ' (S/N: ' . $ti['serial_no'] . ')';
                            }
                            ?>
                            <tr>
                                <td class="c"><?php echo str_pad($qty, 2, '0', STR_PAD_LEFT); ?></td>
                                <td><?php echo htmlspecialchars($desc); ?></td>
                                <td class="c"><?php echo htmlspecialchars($itemCode); ?></td>
                                <td class="c"><?php echo htmlspecialchars($volt); ?></td>
                                <td class="c"><?php echo htmlspecialchars($amp); ?></td>
                                <td class="num"><?php echo number_format($line, 2); ?></td>
                            </tr>
                            <?php
                            $rendered_rows++;
                        }
                        // Pad with empty ruled rows
                        $min_rows = 6;
                        for ($i = $rendered_rows; $i < $min_rows; $i++) {
                            $isLast = ($i === $min_rows - 1);
                            $bcls = $isLast ? ' class="with-bottom"' : '';
                            echo '<tr class="spacer"><td' . $bcls . '></td><td' . $bcls . '></td><td' . $bcls . '></td><td' . $bcls . '></td><td' . $bcls . '></td><td' . $bcls . '></td></tr>';
                        }
                        ?>
                    </tbody>
                </table>

                <?php
                $grand_total      = floatval($SALES_INVOICE->grand_total);
                if ($grand_total <= 0) $grand_total = $sub_total;
                $total_discount   = 0;
                foreach ($temp_items_list as $ti) {
                    $dp = (float)($ti['discount'] ?? 0);
                    $total_discount += (float)$ti['price'] * (int)$ti['quantity'] * ($dp / 100);
                }
                $vat_amount  = floatval($SALES_INVOICE->tax ?? 0);
                ?>

                <!-- Totals + Terms footer -->
                <table style="width:100%; margin-top:8px; border-collapse:collapse; font-size:12px;">
                    <tr>
                        <td style="width:60%; vertical-align:top; padding-right:10px;">
                            <strong>Terms &amp; Conditions :</strong>
                            <ol style="margin:4px 0 0 18px; padding:0; line-height:1.45;">
                                <li>All cheques to be "A/c Payee only" and drawn in favour of "Bandula Battery Sales &amp; Service".</li>
                                <li>Bandula Battery Sales &amp; Service reserves the right to take appropriate legal action to recover in full, if the invoice amount contained herein is not settled within the stipulated credit period.</li>
                                <li>I accept the above mentioned terms &amp; conditions and goods received in correct &amp; good condition.</li>
                            </ol>
                        </td>
                        <td style="width:40%; vertical-align:top;">
                            <table style="width:100%; border-collapse:collapse; font-size:13px;">
                                <tr>
                                    <td style="padding:2px 6px; text-align:right;"><strong>Sub Total :</strong></td>
                                    <td style="padding:2px 6px; text-align:right; min-width:90px; border-bottom:1px solid #b40000;">
                                        <?php echo number_format($sub_total, 2); ?>
                                    </td>
                                </tr>
                                <?php if ($total_discount > 0): ?>
                                <tr>
                                    <td style="padding:2px 6px; text-align:right;"><strong>Discount :</strong></td>
                                    <td style="padding:2px 6px; text-align:right; border-bottom:1px solid #b40000;">
                                        <?php echo number_format($total_discount, 2); ?>
                                    </td>
                                </tr>
                                <?php endif; ?>
                                <?php if ($vat_amount > 0): ?>
                                <tr>
                                    <td style="padding:2px 6px; text-align:right;"><strong>VAT :</strong></td>
                                    <td style="padding:2px 6px; text-align:right; border-bottom:1px solid #b40000;">
                                        <?php echo number_format($vat_amount, 2); ?>
                                    </td>
                                </tr>
                                <?php endif; ?>
                                <tr>
                                    <td style="padding:6px; text-align:right; font-size:15px; font-weight:900; border:1.5px solid #b40000;">TOTAL</td>
                                    <td style="padding:6px; text-align:right; font-size:15px; font-weight:900; border:1.5px solid #b40000;">
                                        <?php echo number_format($grand_total, 2); ?>
                                    </td>
                                </tr>
                                <?php if ($SALES_INVOICE->payment_type == 2): ?>
                                <tr>
                                    <td style="padding:2px 6px; text-align:right;"><strong>Paid :</strong></td>
                                    <td style="padding:2px 6px; text-align:right; border-bottom:1px solid #b40000;">
                                        <?php echo number_format($SALES_INVOICE->outstanding_settle_amount, 2); ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:2px 6px; text-align:right;"><strong>Balance :</strong></td>
                                    <td style="padding:2px 6px; text-align:right; border-bottom:1px solid #b40000;">
                                        <?php echo number_format($grand_total - floatval($SALES_INVOICE->outstanding_settle_amount), 2); ?>
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </table>
                        </td>
                    </tr>
                </table>

                <!-- Signature row -->
                <table style="width:100%; margin-top:30px; border-collapse:collapse; font-size:11px;">
                    <tr>
                        <td style="text-align:center; width:50%;">
                            <div style="border-top:1px solid #b40000; padding-top:3px; margin:0 30px;"><strong>Customer Signature</strong></div>
                        </td>
                        <td style="text-align:center; width:50%;">
                            <div style="border-top:1px solid #b40000; padding-top:3px; margin:0 30px;"><strong>Authorized Signature</strong></div>
                        </td>
                    </tr>
                </table>

                <?php if (false): /* legacy layout removed */ ?>
                        <!-- Header: Logo + Company Info (Left), Invoice Meta (Right) -->
                        <div class="col-12 d-flex justify-content-between align-items-start">
                            <!-- Left: Logo & Company -->
                            <div class="d-flex align-items-center gap-3">
                                <div class="flex-shrink-0">
                                    <?php
                                    $logoPath = 'assets/images/logo.png'; // Default
                                    if (!empty($COMPANY_PROFILE->image_name) && file_exists('uploads/company-logos/' . $COMPANY_PROFILE->image_name)) {
                                        $logoPath = 'uploads/company-logos/' . $COMPANY_PROFILE->image_name;
                                    } elseif (file_exists('assets/images/logo.jpg')) {
                                        $logoPath = 'assets/images/logo.jpg';
                                    }
                                    ?>
                                    <img src="<?php echo $logoPath; ?>" alt="Logo"
                                        style="max-height: 80px; max-width: 150px;">
                                </div>
                                <div>
                                    <h4 class="mb-1 text-uppercase" style="font-weight:900;">
                                        <?php echo $COMPANY_PROFILE->name ?>
                                    </h4>
                                    <p class="mb-1" style="font-size:13px;"><?php echo $COMPANY_PROFILE->address ?></p>
                                    <p class="mb-1" style="font-size:13px;">
                                        <?php echo formatPhone($COMPANY_PROFILE->mobile_number_1); ?>
                                        <?php if (!empty($COMPANY_PROFILE->email))
                                            echo ' | ' . $COMPANY_PROFILE->email; ?>
                                    </p>
                                    <?php if (!empty($COMPANY_PROFILE->vat_number)): ?>
                                        <p class="mb-1" style="font-size:13px;">VAT Reg No:
                                            <?php echo $COMPANY_PROFILE->vat_number ?>
                                        </p>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Right: Invoice Meta -->
                            <div class="text-end">
                                <p class="mb-1" style="font-size:14px;"><strong>Inv No:</strong>
                                    <?php echo $SALES_INVOICE->invoice_no ?></p>
                                <p class="mb-1" style="font-size:14px;"><strong>Inv Date:</strong>
                                    <?php echo date('d M, Y', strtotime($SALES_INVOICE->invoice_date)); ?></p>
                                <?php if (!empty($INVOICE_BRANCH->name)): ?>
                                    <p class="mb-1" style="font-size:14px;"><strong>Branch:</strong>
                                        <?php echo htmlspecialchars($INVOICE_BRANCH->name); ?></p>
                                <?php endif; ?>
                                <?php if (!empty($SALES_INVOICE->vehicle_no)): ?>
                                    <p class="mb-1" style="font-size:14px;"><strong>Vehicle No:</strong>
                                        <?php echo $SALES_INVOICE->vehicle_no ?></p>
                                <?php endif; ?>
                                <?php if ($SALES_INVOICE->payment_type == 2 && $SALES_INVOICE->credit_period): ?>
                                    <?php $CP = new CreditPeriod($SALES_INVOICE->credit_period); ?>
                                    <p class="mb-1" style="font-size:14px;"><strong>Credit Period:</strong>
                                        <?php echo $CP->days ?> Days</p>
                                    <p class="mb-1" style="font-size:14px;"><strong>Due Date:</strong>
                                        <?php echo date('d M, Y', strtotime($SALES_INVOICE->due_date)); ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <hr class="my-2" style="border-top: 1px solid #ccc;">

                    <!-- Title -->
                    <div class="row">
                        <div class="col-12 text-center">
                            <h3 style="font-weight:bold;font-size:22px; margin-top: 10px; margin-bottom: 20px;">

                            </h3>
                        </div>
                    </div>

                    <!-- Customer Details -->
                    <div class="row mb-3">
                        <div class="col-12">
                            <p class="mb-1" style="font-size:14px;"><strong>Customer:</strong>
                                <?php echo $SALES_INVOICE->customer_name ?></p>
                            <p class="mb-1" style="font-size:14px;"><strong>Contact No:</strong>
                                <?php
                                $contactParts = [];
                                if (!empty($SALES_INVOICE->customer_mobile)) {
                                    $contactParts[] = $SALES_INVOICE->customer_mobile;
                                }
                                if (!empty($SALES_INVOICE->customer_address)) {
                                    $contactParts[] = $SALES_INVOICE->customer_address;
                                }
                                echo !empty($contactParts) ? implode(' - ', $contactParts) : '.................................';
                                ?>
                            </p>
                            <p class="mb-1" style="font-size:14px;"><strong>VAT No:</strong>
                                <?php
                                if (!empty($SALES_INVOICE->customer_id)) {
                                    $CUSTOMER_MASTER = new CustomerMaster($SALES_INVOICE->customer_id);
                                    echo !empty($CUSTOMER_MASTER->vat_no) ? $CUSTOMER_MASTER->vat_no : '.................................';
                                } else {
                                    echo '.................................';
                                }
                                ?>
                            </p>
                        </div>
                    </div>

                    <?php if ($SALES_INVOICE->payment_type == 2): ?>
                        <div id="customer-outstanding" class="alert alert-warning py-2 px-3 mb-3" style="font-size:14px;">
                            <strong>Outstanding Balance:</strong>
                            <?php echo number_format((float) ($SALES_INVOICE->grand_total - $SALES_INVOICE->outstanding_settle_amount), 2); ?>
                        </div>
                    <?php endif; ?>

                    <!-- ITEM INVOICE PRINT -->
                    <?php if ($SALES_INVOICE->invoice_type == 'INV') { ?>
                        <div class="table-responsive">
                            <table class="table table-centered">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th colspan="3">Item Name</th>
                                        <th>Serial No</th>
                                        <th>Selling Price</th>
                                        <th>Qty</th>
                                        <?php if ($SALES_INVOICE->tax > 0): ?>
                                            <th class="text-center">VAT</th>
                                        <?php endif; ?>
                                        <th class="text-end">Total</th>
                                    </tr>
                                </thead>
                                <tbody style="font-size:14px;" class="font-bold">
                                    <?php
                                    $TEMP_SALES_ITEM = new SalesInvoiceItem(null);
                                    $temp_items_list = $TEMP_SALES_ITEM->getItemsByInvoiceId($invoice_id);
                                    $subtotal = 0;
                                    $total_discount = 0;

                                    foreach ($temp_items_list as $key => $temp_items) {
                                        $key++;
                                        $price = $temp_items['price'];
                                        $quantity = (int) $temp_items['quantity'];
                                        $discount_percentage = isset($temp_items['discount']) ? (float) $temp_items['discount'] : 0;
                                        $discount_per_item = $price * ($discount_percentage / 100);
                                        $selling_price = $price * $quantity;
                                        $line_total = $price * $quantity;
                                        $subtotal += $price * $quantity;
                                        $total_discount += $discount_per_item * $quantity;
                                        ?>
                                        <?php
                                        $item_vat = 0;
                                        if ($SALES_INVOICE->tax > 0) {
                                            $vat_percentage = $COMPANY_PROFILE->vat_percentage;
                                            $item_vat = $line_total * ($vat_percentage / (100 + $vat_percentage));
                                        }
                                        ?>
                                        <tr>
                                            <td>0<?php echo $key; ?></td>
                                            <td colspan="3">
                                                <?php echo $temp_items['item_code_name'] . ' ' . $temp_items['display_name']; ?>
                                                <?php if (!empty($temp_items['next_service_date']) && $temp_items['next_service_date'] !== '0000-00-00' && strtotime($temp_items['next_service_date']) > 0): ?>
                                                    <br><strong>Next Service Date:</strong>
                                                    <?php echo date('d M, Y', strtotime($temp_items['next_service_date'])); ?>
                                                <?php elseif (!empty($temp_items['current_km'])): ?>
                                                    <br><strong>Next Service Km:</strong>
                                                    <?php echo ($temp_items['current_km'] + 500); ?>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo isset($temp_items['serial_no']) ? $temp_items['serial_no'] : ''; ?>
                                            </td>
                                            <td><?php echo number_format($price, 2); ?></td>
                                            <td><?php echo $quantity; ?></td>
                                            <?php if ($SALES_INVOICE->tax > 0): ?>
                                                <td class="text-center"><?php echo number_format($item_vat, 2); ?></td>
                                            <?php endif; ?>
                                            <td class="text-end"><?php echo number_format($line_total, 2); ?></td>
                                        </tr>
                                    <?php } ?>
                                    <?php
                                    // Calculate rowspan based on visible rows + hidden discount row
                                    // Cash: Gross, Discount(hidden), Net (3 rows) - VAT is now hidden
                                    // Credit: Gross, Paid, Payable, Discount(hidden), Net (5 rows) - VAT is now hidden
                                    $rowSpan = ($SALES_INVOICE->payment_type == 2) ? 5 : 3;
                                    ?>
                                    <tr>
                                        <td colspan="4" rowspan="<?php echo $rowSpan; ?>" style="vertical-align:top;  ">
                                            <h6 style="margin-top:8px;"><strong>Terms & Conditions:</strong></h6>
                                            <ul style="padding-left:20px;margin-bottom:0;">
                                                <?php
                                                $invoiceRemark = new InvoiceRemark();
                                                $paymentRemarks = $invoiceRemark->getRemarkByPaymentType($SALES_INVOICE->payment_type);
                                                if (!empty($paymentRemarks)) {
                                                    foreach ($paymentRemarks as $remark) {
                                                        if (!empty($remark['remark'])) {
                                                            echo '<li>' . htmlspecialchars($remark['remark']) . '</li>';
                                                        }
                                                    }
                                                }
                                                ?>
                                            </ul>
                                        </td>
                                        <td colspan="<?php echo ($SALES_INVOICE->tax > 0) ? 4 : 3; ?>"
                                            class="text-end font-weight-bold"><strong>Gross Amount:-</strong>
                                        </td>
                                        <td class="text-end font-weight-bold">
                                            <strong><?php echo number_format($subtotal, 2); ?></strong>
                                        </td>
                                    </tr>
                                    <?php if ($SALES_INVOICE->payment_type == 2): // Credit payment 
                                                ?>
                                        <tr>
                                            <td colspan="<?php echo ($SALES_INVOICE->tax > 0) ? 4 : 3; ?>"
                                                class="text-end font-weight-bold"><strong>Paid Amount:-</strong>
                                            </td>
                                            <td class="text-end font-weight-bold">
                                                <strong><?php echo number_format($SALES_INVOICE->outstanding_settle_amount, 2); ?></strong>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="<?php echo ($SALES_INVOICE->tax > 0) ? 4 : 3; ?>"
                                                class="text-end font-weight-bold"><strong>Payable Amount:-</strong>
                                            </td>
                                            <td class="text-end font-weight-bold">
                                                <strong><?php echo number_format($SALES_INVOICE->grand_total - $SALES_INVOICE->outstanding_settle_amount, 2); ?></strong>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                    <tr hidden>
                                        <td colspan="4" class="text-end font-weight-bold">Discount:-</td>
                                        <td class="text-end font-weight-bold">-
                                            <?php echo number_format($total_discount, 2); ?>
                                        </td>
                                    </tr>
                                    <tr hidden>
                                        <td colspan="4" class="text-end font-weight-bold"><strong>VAT :-</strong></td>
                                        <td class="text-end font-weight-bold">
                                            <strong><?php echo number_format($SALES_INVOICE->tax, 2); ?></strong>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="<?php echo ($SALES_INVOICE->tax > 0) ? 4 : 3; ?>" class="text-end">
                                            <strong>Net Amount:-</strong>
                                        </td>
                                        <td class="text-end">
                                            <strong><?php echo number_format($subtotal, 2); ?></strong>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="5" style="padding-top:50px !important;">
                                            <table style="width:100%;">
                                                <tr>
                                                    <td style="text-align:center;">
                                                        _________________________<br><strong>Prepared By</strong></td>
                                                    <td style="text-align:center;">
                                                        _________________________<br><strong>Approved By</strong></td>
                                                    <td style="text-align:center;">
                                                        _________________________<br><strong>Received By</strong></td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    <?php } ?>
                <?php endif; /* legacy layout */ ?>

            </div>
        </div>

        <!-- JS -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
        <script>
            // ── Orientation management ──────────────────────────────────────────
            var _printOrientation = 'portrait'; // default
            var _dynamicPageStyle = null;

            function setOrientation(orientation) {
                _printOrientation = orientation;

                // Update toggle button appearance
                document.getElementById('btn-portrait').classList.toggle('active', orientation === 'portrait');
                document.getElementById('btn-landscape').classList.toggle('active', orientation === 'landscape');

                // Inject / update a dynamic <style> that overrides @page for the browser print dialog
                if (!_dynamicPageStyle) {
                    _dynamicPageStyle = document.createElement('style');
                    _dynamicPageStyle.id = 'dynamic-page-style';
                    document.head.appendChild(_dynamicPageStyle);
                }
                if (orientation === 'landscape') {
                    _dynamicPageStyle.textContent = '@media print { @page { size: A4 landscape; margin: 10mm; } }';
                } else {
                    _dynamicPageStyle.textContent = '@media print { @page { size: A4 portrait; margin: 10mm; } }';
                }
            }

            function downloadPDF() {
                const element = document.getElementById('invoice-content');
                const opt = {
                    margin: 0.5,
                    filename: 'Invoice_<?php echo $SALES_INVOICE->invoice_no ?>.pdf',
                    image: {
                        type: 'jpeg',
                        quality: 0.98
                    },
                    html2canvas: {
                        scale: 2
                    },
                    jsPDF: {
                        unit: 'mm',
                        format: 'a4',
                        orientation: _printOrientation   // 'portrait' or 'landscape'
                    }
                };
                html2pdf().set(opt).from(element).save();
            }

            function shareViaWhatsApp() {
                const customerMobile = '<?php echo $customerMobile; ?>';
                const invoiceNo = '<?php echo $SALES_INVOICE->invoice_no; ?>';
                const customerName = '<?php echo addslashes($SALES_INVOICE->customer_name); ?>';
                const companyName = '<?php echo addslashes($COMPANY_PROFILE->name); ?>';
                const pdfUrl = '<?php echo $pdfUrl; ?>';

                // Create WhatsApp message
                const message = `Dear ${customerName},\n\nYour invoice ${invoiceNo} from ${companyName} is ready.\n\nYou can download the PDF here: ${pdfUrl}\n\nThank you for your business!`;

                // URL encode the message
                const encodedMessage = encodeURIComponent(message);

                // Create WhatsApp URL using wa.me format
                let whatsappUrl;
                if (customerMobile && customerMobile.length >= 10) {
                    whatsappUrl = `https://wa.me/${customerMobile}?text=${encodedMessage}`;
                } else {
                    // If no customer mobile, open WhatsApp with message (user will need to select contact)
                    whatsappUrl = `https://wa.me/?text=${encodedMessage}`;
                }

                // Open WhatsApp in new tab
                window.open(whatsappUrl, '_blank');
            }

            // Show/hide outstanding banner using the toggle checkbox
            document.addEventListener("DOMContentLoaded", function () {
                const toggleOutstanding = document.getElementById("toggleOutstanding");
                const outstandingBlock = document.getElementById("customer-outstanding");

                function syncOutstandingVisibility() {
                    if (!outstandingBlock || !toggleOutstanding) return;
                    if (toggleOutstanding.checked) {
                        outstandingBlock.style.display = "";
                    } else {
                        outstandingBlock.style.display = "none";
                    }
                }

                if (toggleOutstanding) {
                    toggleOutstanding.addEventListener("change", syncOutstandingVisibility);
                    syncOutstandingVisibility();
                }
            });

            // Trigger print on Enter
            document.addEventListener("keydown", function (e) {
                if (e.key === "Enter") {
                    window.print();
                }
            });
        </script>
        <script src="assets/js/bootstrap.bundle.min.js"></script>
</body>

</html>