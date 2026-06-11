<!doctype html>
<?php
include 'class/include.php';
include 'auth.php';
?>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Monthly Target Report | <?php echo $COMPANY_PROFILE_DETAILS->name ?> </title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="<?php echo $COMPANY_PROFILE_DETAILS->name ?>" name="author" />
    <?php include 'main-css.php' ?>
</head>

<body data-layout="horizontal" data-topbar="colored" class="someBlock">

    <div id="layout-wrapper">

        <?php include 'navigation.php' ?>

        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">

                    <!-- page header -->
                    <div class="row mb-4">
                        <div class="col-md-8 d-flex align-items-center flex-wrap gap-2">
                            <a href="#" class="btn btn-primary" id="btnViewReport">
                                <i class="uil uil-eye me-1"></i> View Report
                            </a>
                            <a href="#" class="btn btn-success" id="btnReset">
                                <i class="uil uil-redo me-1"></i> Reset
                            </a>
                        </div>

                        <div class="col-md-4 text-md-end text-start mt-3 mt-md-0">
                            <ol class="breadcrumb m-0 justify-content-md-end">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                                <li class="breadcrumb-item active">Monthly Target Report</li>
                            </ol>
                        </div>
                    </div>

                    <!-- Filter section -->
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title">Filter Options</h4>
                                    <p class="card-title-desc">Select brand and date range to view the monthly target chart</p>

                                    <div class="row">
                                        <!-- Brand -->
                                        <div class="col-md-4 mb-3">
                                            <label for="brand_id" class="form-label">Brand <span class="text-danger">*</span></label>
                                            <select id="brand_id" class="form-select" required>
                                                <option value="">-- Select Brand --</option>
                                                <?php
                                                $BRAND = new Brand(NULL);
                                                $brands_query = "SELECT * FROM `brands` WHERE `is_active` = 1 ORDER BY `name` ASC";
                                                $db_brand = Database::getInstance();
                                                $brand_res = $db_brand->readQuery($brands_query);
                                                while ($brand = mysqli_fetch_assoc($brand_res)) {
                                                    echo "<option value='{$brand['id']}'>" . htmlspecialchars($brand['name']) . "</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>

                                        <!-- Date From -->
                                        <div class="col-md-4 mb-3">
                                            <label for="from_date" class="form-label">Date From <span class="text-danger">*</span></label>
                                            <input id="from_date" type="text" class="form-control date-picker" placeholder="YYYY-MM-DD" required readonly style="background-color: #fff; cursor: pointer;">
                                        </div>

                                        <!-- Date To -->
                                        <div class="col-md-4 mb-3">
                                            <label for="to_date" class="form-label">Date To <span class="text-danger">*</span></label>
                                            <input id="to_date" type="text" class="form-control date-picker" placeholder="YYYY-MM-DD" required readonly style="background-color: #fff; cursor: pointer;">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Target Chart -->
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title">Target Chart</h4>
                                    <p class="card-title-desc">Select a target tier to see how much more needs to be sold to achieve it</p>

                                    <div class="table-responsive">
                                        <table class="table table-bordered text-center align-middle" id="targetTable">
                                            <thead class="table-light">
                                                <tr>
                                                    <th style="width: 40%;">Qty</th>
                                                    <th style="width: 30%;">Discount %</th>
                                                    <th style="width: 30%;">Select</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td colspan="3" class="text-muted">No data. Pick a brand and date range, then click View Report.</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Achievement Summary -->
                    <div class="row" id="achievementSection" style="display: none;">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title">Achievement Summary</h4>

                                    <div class="row mt-3">
                                        <div class="col-md-3 col-6">
                                            <div class="border rounded p-3 text-center">
                                                <p class="text-muted mb-1">Total Qty (Target)</p>
                                                <h4 class="mb-0" id="sumTotalQty">0</h4>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-6">
                                            <div class="border rounded p-3 text-center">
                                                <p class="text-muted mb-1">Monthly Qty</p>
                                                <h4 class="mb-0" id="sumMonthlyQty">0</h4>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-6">
                                            <div class="border rounded p-3 text-center">
                                                <p class="text-muted mb-1">Sales Qty</p>
                                                <h4 class="mb-0 text-success" id="sumSalesQty">0</h4>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-6">
                                            <div class="border rounded p-3 text-center">
                                                <p class="text-muted mb-1">Balance to Achieve</p>
                                                <h4 class="mb-0 text-danger" id="sumBalance">0</h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <?php include 'footer.php' ?>
        </div>
    </div>

    <div class="rightbar-overlay"></div>

    <script src="assets/libs/jquery/jquery.min.js"></script>
    <?php include 'main-js.php' ?>

    <script>
        $(document).ready(function () {

            // Init date pickers
            $('.date-picker').datepicker({
                dateFormat: 'yy-mm-dd',
                changeMonth: true,
                changeYear: true
            });

            var currentSalesQty = 0;

            function resetSummary() {
                $('#achievementSection').hide();
                $('#sumTotalQty').text('0');
                $('#sumMonthlyQty').text('0');
                $('#sumSalesQty').text('0');
                $('#sumBalance').text('0');
            }

            function resetTable() {
                $('#targetTable tbody').html('<tr><td colspan="3" class="text-muted">No data. Pick a brand and date range, then click View Report.</td></tr>');
            }

            $('#btnReset').on('click', function (e) {
                e.preventDefault();
                $('#brand_id').val('');
                $('#from_date').val('');
                $('#to_date').val('');
                resetTable();
                resetSummary();
            });

            $('#btnViewReport').on('click', function (e) {
                e.preventDefault();

                var brandId = $('#brand_id').val();
                var fromDate = $('#from_date').val();
                var toDate = $('#to_date').val();

                if (!brandId) {
                    alert('Please select a brand');
                    return;
                }
                if (!fromDate || !toDate) {
                    alert('Please select both From and To dates');
                    return;
                }
                if (fromDate > toDate) {
                    alert('From date cannot be after To date');
                    return;
                }

                resetSummary();
                $('#targetTable tbody').html('<tr><td colspan="3" class="text-muted">Loading...</td></tr>');

                $.ajax({
                    url: 'ajax/php/monthly-target-report.php',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        action: 'load_report',
                        brand_id: brandId,
                        from_date: fromDate,
                        to_date: toDate
                    },
                    success: function (resp) {
                        if (resp.status !== 'success') {
                            resetTable();
                            alert(resp.message || 'Failed to load report');
                            return;
                        }

                        currentSalesQty = parseFloat(resp.sales_qty) || 0;

                        var tiers = resp.tiers || [];
                        if (tiers.length === 0) {
                            $('#targetTable tbody').html('<tr><td colspan="3" class="text-muted">No targets configured for this brand within the selected period.</td></tr>');
                            return;
                        }

                        var html = '';
                        tiers.forEach(function (t) {
                            var qty = parseFloat(t.qty) || 0;
                            var months = parseInt(t.period_month) || 1;
                            html += '<tr>' +
                                '<td>' + qty + '</td>' +
                                '<td>' + parseFloat(t.net_discount).toFixed(2) + '%</td>' +
                                '<td>' +
                                '<input type="radio" name="target_tier" class="form-check-input target-radio" ' +
                                'data-qty="' + qty + '" data-months="' + months + '">' +
                                '</td>' +
                                '</tr>';
                        });
                        $('#targetTable tbody').html(html);
                    },
                    error: function () {
                        resetTable();
                        alert('Server error while loading report');
                    }
                });
            });

            // Tier selection -> compute achievement summary
            $(document).on('change', '.target-radio', function () {
                var totalQty = parseFloat($(this).data('qty')) || 0;
                var months = parseInt($(this).data('months')) || 1;
                if (months < 1) months = 1;

                var monthlyQty = totalQty / months;
                var balance = totalQty - currentSalesQty;
                if (balance < 0) balance = 0;

                $('#sumTotalQty').text(totalQty);
                $('#sumMonthlyQty').text(monthlyQty.toFixed(2));
                $('#sumSalesQty').text(currentSalesQty);
                $('#sumBalance').text(balance.toFixed(2));
                $('#achievementSection').show();
            });
        });
    </script>
</body>

</html>
