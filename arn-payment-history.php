<!doctype html>
<?php
include 'class/include.php';
include './auth.php';

$db = Database::getInstance();

// Filters
$brand_id   = isset($_GET['brand_id']) ? (int)$_GET['brand_id'] : 0;
$date_from  = isset($_GET['date_from']) ? trim($_GET['date_from']) : '';
$date_to    = isset($_GET['date_to']) ? trim($_GET['date_to']) : '';
$arn_no     = isset($_GET['arn_no']) ? trim($_GET['arn_no']) : '';

$where = ["am.is_cancelled = 0"];
if ($brand_id > 0)         { $where[] = "am.brand = '" . (int)$brand_id . "'"; }
if ($date_from !== '')     { $where[] = "am.invoice_date >= '" . $db->escapeString($date_from) . "'"; }
if ($date_to !== '')       { $where[] = "am.invoice_date <= '" . $db->escapeString($date_to) . "'"; }
if ($arn_no !== '')        { $where[] = "am.arn_no LIKE '%" . $db->escapeString($arn_no) . "%'"; }

$whereSql = implode(' AND ', $where);

$query = "SELECT am.id, am.arn_no, am.invoice_date, am.brand, am.bill_file,
                 am.total_received_qty, am.total_arn_value, am.paid_amount,
                 am.supplier_id
          FROM arn_master am
          WHERE {$whereSql}
          ORDER BY am.invoice_date DESC, am.id DESC";
$arnsResult = $db->readQuery($query);
$arns = [];
while ($row = mysqli_fetch_assoc($arnsResult)) { $arns[] = $row; }

$BRAND      = new Brand();
$brandsList = $BRAND->all();
?>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>ARN Payment History | <?php echo $COMPANY_PROFILE_DETAILS->name ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="<?php echo $COMPANY_PROFILE_DETAILS->name ?>" name="author" />
    <?php include 'main-css.php' ?>
    <style>
        .attach-yes { color:#28a745; font-size:18px; }
        .attach-no  { color:#dc3545; font-size:18px; }
        .pay-slip-chip {
            display:inline-flex; align-items:center; gap:6px;
            background:#eef5ff; border:1px solid #cfe2ff; color:#0d6efd;
            padding:4px 10px; border-radius:14px; font-size:12px;
            margin:2px 4px 2px 0; text-decoration:none;
        }
        .pay-slip-chip:hover { background:#cfe2ff; color:#0a58ca; }
        .pay-slip-chip i { font-size:12px; }
        .pay-slip-missing {
            display:inline-flex; align-items:center; gap:6px;
            background:#fdecec; border:1px solid #f5c2c7; color:#842029;
            padding:4px 10px; border-radius:14px; font-size:12px;
            margin:2px 4px 2px 0;
        }
        .slip-cell {
            min-width: 260px;
            max-width: 360px;
            white-space: normal !important;
            word-break: break-word;
        }
        .slip-cell .pay-slip-chip,
        .slip-cell .pay-slip-missing { max-width: 100%; }
        #arnPayHistoryTable td.slip-cell { white-space: normal !important; }
    </style>
</head>

<body data-layout="horizontal" data-topbar="colored" class="someBlock">
    <div id="layout-wrapper">

        <?php include 'navigation.php' ?>

        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">
                    <div class="row mb-4">
                        <div class="col-md-8 d-flex align-items-center flex-wrap gap-2">
                            ARN Payment History
                        </div>
                        <div class="col-md-4 text-md-end text-start mt-3 mt-md-0">
                            <ol class="breadcrumb m-0 justify-content-md-end">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                                <li class="breadcrumb-item active">ARN Payment History</li>
                            </ol>
                        </div>
                    </div>

                    <!-- Filters -->
                    <form method="get" action="arn-payment-history.php">
                        <div class="card mb-3">
                            <div class="card-body row g-3">
                                <div class="col-md-3">
                                    <label for="brand_id" class="form-label">Brand</label>
                                    <select id="brand_id" name="brand_id" class="form-control">
                                        <option value="">All Brands</option>
                                        <?php foreach ($brandsList as $b): ?>
                                            <option value="<?= (int)$b['id'] ?>" <?= ($brand_id == $b['id']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($b['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label for="date_from" class="form-label">From Date</label>
                                    <input type="date" id="date_from" name="date_from"
                                           class="form-control" value="<?= htmlspecialchars($date_from) ?>">
                                </div>
                                <div class="col-md-2">
                                    <label for="date_to" class="form-label">To Date</label>
                                    <input type="date" id="date_to" name="date_to"
                                           class="form-control" value="<?= htmlspecialchars($date_to) ?>">
                                </div>
                                <div class="col-md-3">
                                    <label for="arn_no" class="form-label">ARN No</label>
                                    <input type="text" id="arn_no" name="arn_no"
                                           class="form-control" placeholder="Search ARN No"
                                           value="<?= htmlspecialchars($arn_no) ?>">
                                </div>
                                <div class="col-md-2 d-flex align-items-end gap-2">
                                    <button type="submit" class="btn btn-success flex-fill">Filter</button>
                                    <a href="arn-payment-history.php" class="btn btn-secondary flex-fill">Reset</a>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Table -->
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-body">
                                    <table id="arnPayHistoryTable" class="table table-bordered dt-responsive"
                                           style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>ARN No</th>
                                                <th>Date</th>
                                                <th>Brand</th>
                                                <th>Supplier</th>
                                                <th class="text-end">Qty</th>
                                                <th class="text-end">ARN Value</th>
                                                <th class="text-end">Paid</th>
                                                <th class="text-end">Outstanding</th>
                                                <th class="text-center">Invoice Attached</th>
                                                <th class="slip-cell">Payment Slips</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php foreach ($arns as $idx => $arn):
                                            $key = $idx + 1;
                                            $BRAND_OBJ    = new Brand($arn['brand']);
                                            $CUSTOMER     = new CustomerMaster($arn['supplier_id']);
                                            $hasInvoice   = !empty($arn['bill_file']);
                                            $outstanding  = (float)$arn['total_arn_value'] - (float)$arn['paid_amount'];

                                            // Gather payment slips (cash receipt slip + per-cheque method slip)
                                            $slipsQuery = "
                                                SELECT prm.id AS method_id, prm.payment_type_id, prm.amount,
                                                       prm.cheq_no, prm.cheq_date, prm.bill_file AS method_bill,
                                                       pr.id AS receipt_id, pr.receipt_no, pr.entry_date,
                                                       pr.cash_bill_file
                                                FROM payment_receipt_method_supplier prm
                                                INNER JOIN payment_receipt_supplier pr
                                                        ON pr.id = prm.receipt_id
                                                WHERE prm.invoice_id = '" . (int)$arn['id'] . "'
                                                ORDER BY pr.entry_date ASC, prm.id ASC";
                                            $slipsRes = $db->readQuery($slipsQuery);
                                            $slips = [];
                                            while ($s = mysqli_fetch_assoc($slipsRes)) { $slips[] = $s; }
                                        ?>
                                            <tr>
                                                <td><?= $key ?></td>
                                                <td><?= htmlspecialchars($arn['arn_no']) ?></td>
                                                <td><?= htmlspecialchars($arn['invoice_date']) ?></td>
                                                <td><?= htmlspecialchars($BRAND_OBJ->name ?? '') ?></td>
                                                <td><?= htmlspecialchars(($CUSTOMER->code ?? '') . ' - ' . ($CUSTOMER->name ?? '')) ?></td>
                                                <td class="text-end"><?= number_format((float)$arn['total_received_qty'], 2) ?></td>
                                                <td class="text-end"><?= number_format((float)$arn['total_arn_value'], 2) ?></td>
                                                <td class="text-end"><?= number_format((float)$arn['paid_amount'], 2) ?></td>
                                                <td class="text-end"><?= number_format($outstanding, 2) ?></td>
                                                <td class="text-center">
                                                    <?php if ($hasInvoice): ?>
                                                        <a href="uploads/arn-bills/<?= htmlspecialchars($arn['bill_file']) ?>"
                                                           target="_blank" title="View Invoice">
                                                            <i class="fas fa-check-circle attach-yes"></i>
                                                        </a>
                                                    <?php else: ?>
                                                        <i class="fas fa-times-circle attach-no" title="No invoice attached"></i>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="slip-cell">
                                                    <?php
                                                    if (empty($slips)) {
                                                        echo '<span class="text-muted">No payments</span>';
                                                    } else {
                                                        // Show cash receipt slip once per receipt (when payment type = 1 cash)
                                                        $shownReceiptCash = [];
                                                        foreach ($slips as $s) {
                                                            $ptype = (int)$s['payment_type_id'];
                                                            if ($ptype == 1) {
                                                                // Cash → slip stored on receipt level
                                                                $rid = (int)$s['receipt_id'];
                                                                if (isset($shownReceiptCash[$rid])) { continue; }
                                                                $shownReceiptCash[$rid] = true;
                                                                $label = 'Cash #' . htmlspecialchars($s['receipt_no']);
                                                                if (!empty($s['cash_bill_file'])) {
                                                                    echo '<a class="pay-slip-chip" target="_blank" href="uploads/payment-receipt-supplier-bills/'
                                                                        . htmlspecialchars($s['cash_bill_file']) . '" '
                                                                        . 'title="' . htmlspecialchars($s['entry_date']) . ' • '
                                                                        . number_format((float)$s['amount'], 2) . '">'
                                                                        . '<i class="fas fa-money-bill-wave"></i> ' . $label
                                                                        . ' <i class="fas fa-check-circle attach-yes" style="font-size:12px;"></i></a>';
                                                                } else {
                                                                    echo '<span class="pay-slip-missing">'
                                                                        . '<i class="fas fa-money-bill-wave"></i> ' . $label
                                                                        . ' <i class="fas fa-times-circle attach-no" style="font-size:12px;"></i></span>';
                                                                }
                                                            } else {
                                                                // Cheque / other → method-level bill
                                                                $label = 'Chq ' . htmlspecialchars($s['cheq_no'] ?: '-');
                                                                if (!empty($s['method_bill'])) {
                                                                    echo '<a class="pay-slip-chip" target="_blank" href="uploads/payment-receipt-supplier-bills/'
                                                                        . htmlspecialchars($s['method_bill']) . '" '
                                                                        . 'title="' . htmlspecialchars($s['cheq_date']) . ' • '
                                                                        . number_format((float)$s['amount'], 2) . '">'
                                                                        . '<i class="fas fa-money-check"></i> ' . $label
                                                                        . ' <i class="fas fa-check-circle attach-yes" style="font-size:12px;"></i></a>';
                                                                } else {
                                                                    echo '<span class="pay-slip-missing">'
                                                                        . '<i class="fas fa-money-check"></i> ' . $label
                                                                        . ' <i class="fas fa-times-circle attach-no" style="font-size:12px;"></i></span>';
                                                                }
                                                            }
                                                        }
                                                    }
                                                    ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                        <?php if (empty($arns)): ?>
                                            <tr><td colspan="11" class="text-center text-muted">No ARN records found.</td></tr>
                                        <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <?php include 'footer.php' ?>
    </div>

    <script src="assets/libs/jquery/jquery.min.js"></script>
    <?php include 'main-js.php' ?>
    <script src="assets/libs/datatables.net-buttons/js/buttons.html5.min.js"></script>
    <script src="assets/libs/datatables.net-buttons/js/buttons.print.min.js"></script>
    <script src="assets/libs/pdfmake/build/pdfmake.min.js"></script>
    <script src="assets/libs/pdfmake/build/vfs_fonts.js"></script>

    <script>
        $(function () {
            if ($.fn.DataTable) {
                $('#arnPayHistoryTable').DataTable({
                    pageLength: 25,
                    order: [[2, 'desc']],
                    dom: 'Bfrtip',
                    buttons: ['copy', 'excel', 'pdf', 'print']
                });
            }
        });
    </script>
</body>
</html>
