<!doctype html>
<?php
include 'class/include.php';

if (!isset($_SESSION)) {
    session_start();
}

$id = $_GET['id'];
$US = new User($_SESSION['id']);
$COMPANY_PROFILE = new CompanyProfile($US->company_id);

$QUOTATION = new Quotation($id);
$CUSTOMER_MASTER = new CustomerMaster($QUOTATION->customer_id);

$quotation_date = !empty($QUOTATION->date) ? date('d/m/Y', strtotime($QUOTATION->date)) : '';
?>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Quotation Details | <?php echo $COMPANY_PROFILE->name ?> </title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            color: #000;
            margin: 0;
            padding: 0;
            background: #f5f5f5;
        }

        .sheet {
            background: #fff;
            width: 210mm;
            min-height: 297mm;
            margin: 20px auto;
            padding: 18mm 16mm;
            box-shadow: 0 0 6px rgba(0, 0, 0, 0.15);
            position: relative;
        }

        .toolbar {
            max-width: 210mm;
            margin: 16px auto 0;
            display: flex;
            justify-content: flex-end;
            gap: 8px;
            padding: 0 16mm;
        }

        .toolbar select,
        .toolbar button {
            padding: 6px 14px;
            font-size: 14px;
            border: 1px solid #888;
            background: #fff;
            cursor: pointer;
            border-radius: 3px;
        }

        .toolbar button.print {
            background: #28a745;
            color: #fff;
            border-color: #28a745;
        }

        .header {
            text-align: center;
            position: relative;
        }

        .header h1 {
            font-size: 30px;
            font-weight: 900;
            letter-spacing: 1px;
            margin: 0 0 6px 0;
            text-transform: uppercase;
        }

        .header .tagline {
            font-style: italic;
            font-weight: bold;
            font-size: 16px;
            margin: 0 0 4px 0;
        }

        .header .addr {
            font-size: 13px;
            margin: 2px 0;
        }

        .header .logo-img {
            position: absolute;
            right: 0;
            top: 28px;
            max-height: 70px;
            max-width: 130px;
        }

        hr.divider {
            border: none;
            border-top: 1px solid #000;
            margin: 14px 0 18px 0;
        }

        .meta-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 6px;
            font-size: 13px;
        }

        .dotline {
            border-bottom: 1px dotted #000;
            display: inline-block;
            min-height: 16px;
        }

        .customer-block {
            width: 60%;
        }

        .customer-block .dotline {
            width: 100%;
            margin-bottom: 4px;
        }

        .date-block {
            width: 35%;
            text-align: right;
            white-space: nowrap;
        }

        .salutation {
            margin: 14px 0 8px 0;
            font-size: 14px;
        }

        table.items {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }

        table.items th {
            text-align: left;
            font-size: 14px;
            padding: 6px 4px;
            border-bottom: none;
        }

        table.items td {
            font-size: 13px;
            padding: 4px;
            border-bottom: 1px dotted #000;
            height: 22px;
        }

        table.items th.num,
        table.items td.num {
            text-align: right;
        }

        .thanks {
            margin-top: 24px;
            font-size: 14px;
            line-height: 1.4;
        }

        .signature {
            margin-top: 50px;
            font-size: 14px;
        }

        .signature .sigline {
            border-top: 1px dotted #000;
            width: 220px;
            padding-top: 4px;
        }

        .terms {
            margin-top: 28px;
            font-size: 13px;
        }

        .terms h4 {
            font-weight: bold;
            margin: 0 0 6px 0;
            font-size: 14px;
        }

        .terms ul {
            list-style: none;
            padding-left: 0;
            margin: 0;
        }

        .terms ul li {
            padding-left: 20px;
            position: relative;
            margin-bottom: 4px;
            line-height: 1.4;
        }

        .terms ul li::before {
            content: "\2756";
            position: absolute;
            left: 0;
            top: 0;
        }

        @media print {
            body {
                background: #fff;
            }

            .no-print {
                display: none !important;
            }

            .sheet {
                margin: 0;
                box-shadow: none;
                width: auto;
                min-height: auto;
                padding: 12mm;
            }

            @page {
                size: A4;
                margin: 10mm;
            }

            body.print-a3 .sheet { width: 297mm; }
            body.print-a5 .sheet { width: 148mm; }
            body.print-letter .sheet { width: 8.5in; }
            body.print-legal .sheet { width: 8.5in; }
            body.print-tabloid .sheet { width: 11in; }
            body.print-dotmatrix .sheet { width: 9.5in; }
        }
    </style>
</head>

<body class="print-a4">

    <div class="toolbar no-print">
        <select id="printFormat" onchange="setPrintFormat(this.value)">
            <option value="a4" selected>A4</option>
            <option value="a3">A3</option>
            <option value="a5">A5</option>
            <option value="letter">Letter</option>
            <option value="legal">Legal</option>
            <option value="tabloid">Tabloid</option>
            <option value="dotmatrix">Dot Matrix</option>
        </select>
        <button class="print" onclick="window.print()">Print</button>
    </div>

    <div class="sheet">

        <div class="header">
            <?php if (!empty($COMPANY_PROFILE->image_name)) { ?>
                <img class="logo-img" src="./uploads/company-logos/<?php echo $COMPANY_PROFILE->image_name ?>" alt="logo">
            <?php } ?>
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

        <hr class="divider">

        <div class="meta-row">
            <div class="customer-block">
                <span class="dotline"><?php echo htmlspecialchars($CUSTOMER_MASTER->name) ?></span>
                <span class="dotline"><?php echo htmlspecialchars($CUSTOMER_MASTER->address) ?></span>
                <span class="dotline"><?php echo htmlspecialchars($CUSTOMER_MASTER->mobile_number) ?></span>
            </div>
            <div class="date-block">
                <span class="dotline" style="min-width:140px; text-align:center;">
                    <?php echo $quotation_date ?>
                </span>
            </div>
        </div>

        <p class="salutation">Dear Sir / Madam,</p>

        <?php
        $QUOTATION_ITEM = new QuotationItem(null);
        $temp_items_list = $QUOTATION_ITEM->getByQuotationId($id);
        $row_count = count($temp_items_list);
        $min_rows = 8;
        ?>

        <table class="items">
            <thead>
                <tr>
                    <th style="width:5%;">No.</th>
                    <th style="width:12%;">Code</th>
                    <th style="width:34%;">Description</th>
                    <th class="num" style="width:7%;">Volt</th>
                    <th class="num" style="width:7%;">Ah</th>
                    <th class="num" style="width:8%;">Qty</th>
                    <th class="num" style="width:13%;">Unit Price</th>
                    <th class="num" style="width:14%;">Price</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $subtotal = 0;
                $total_discount = 0;
                foreach ($temp_items_list as $key => $temp_items) {
                    $key++;
                    $price = (float) $temp_items['price'];
                    $quantity = (int) $temp_items['qty'];
                    $discount_amount = isset($temp_items['discount']) ? (float) $temp_items['discount'] : 0;
                    $selling_price = $price - $discount_amount;
                    $line_total = $selling_price * $quantity;
                    $subtotal += $price * $quantity;
                    $total_discount += $discount_amount * $quantity;

                    $qi_item = new ItemMaster($temp_items['item_code']);
                    $qi_code = $qi_item->code ?: $temp_items['item_code'];
                    $qi_volt = $qi_item->voltage ?? '';
                    $qi_amp  = $qi_item->ampere ?? '';
                ?>
                    <tr>
                        <td><?php echo str_pad($key, 2, '0', STR_PAD_LEFT); ?></td>
                        <td><?php echo htmlspecialchars($qi_code); ?></td>
                        <td><?php echo htmlspecialchars($temp_items['item_name']); ?></td>
                        <td class="num"><?php echo htmlspecialchars($qi_volt); ?></td>
                        <td class="num"><?php echo htmlspecialchars($qi_amp); ?></td>
                        <td class="num"><?php echo $quantity; ?></td>
                        <td class="num"><?php echo number_format($selling_price, 2); ?></td>
                        <td class="num"><?php echo number_format($line_total, 2); ?></td>
                    </tr>
                <?php }
                for ($i = $row_count; $i < $min_rows; $i++) { ?>
                    <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                <?php } ?>

                <tr>
                    <td colspan="7" class="num" style="border-bottom:none; padding-top:8px;">Gross Amount :</td>
                    <td class="num" style="border-bottom:none; padding-top:8px;"><?php echo number_format($subtotal, 2); ?></td>
                </tr>
                <?php if ($total_discount > 0) { ?>
                    <tr>
                        <td colspan="7" class="num" style="border-bottom:none;">Discount :</td>
                        <td class="num" style="border-bottom:none;"><?php echo number_format($total_discount, 2); ?></td>
                    </tr>
                <?php } ?>
                <?php if (!empty($QUOTATION->is_vat_invoice) && (float) $QUOTATION->vat_percentage > 0) { ?>
                    <tr>
                        <td colspan="7" class="num" style="border-bottom:none;">VAT (<?php echo number_format($QUOTATION->vat_percentage, 2); ?>%) :</td>
                        <td class="num" style="border-bottom:none;"><?php echo number_format($QUOTATION->vat_total, 2); ?></td>
                    </tr>
                <?php } ?>
                <tr>
                    <td colspan="7" class="num" style="border-bottom:none;"><strong>Net Amount :</strong></td>
                    <td class="num" style="border-bottom:none;"><strong><?php echo number_format($QUOTATION->grand_total, 2); ?></strong></td>
                </tr>
            </tbody>
        </table>

        <div class="thanks">
            Thank You,<br>
            <?php echo htmlspecialchars($COMPANY_PROFILE->name) ?>
        </div>

        <div class="signature">
            <div class="sigline"></div>
            <strong>Manager</strong>
        </div>

        <div class="terms">
            <h4>Terms &amp; Exclusions:</h4>
            <ul>
                <li>Prices are valid for <?php echo (int) ($QUOTATION->validity ?: 30); ?> days from the date of this quote.</li>
                <li>Quoted prices are subject to change if the manufacturer's list price increases by more than 2% before order placement.</li>
                <li>This quote excludes shipping and any items not explicitly listed.</li>
                <li>All orders are subject to final confirmation and availability at the time of purchase.</li>
            </ul>
        </div>

    </div>

    <script>
        window.onload = function () {
            setPrintFormat('a4');
        };

        function setPrintFormat(format) {
            const formats = ['a4', 'a3', 'a5', 'letter', 'legal', 'tabloid', 'dotmatrix'];
            document.body.className = document.body.className
                .split(' ')
                .filter(c => !formats.map(f => 'print-' + f).includes(c))
                .join(' ')
                .trim();
            document.body.classList.add('print-' + format);
        }

        document.addEventListener("keydown", function (e) {
            if (e.key === "Enter") {
                window.print();
            }
        });
    </script>
</body>

</html>
