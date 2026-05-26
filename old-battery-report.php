<!doctype html>
<?php
include 'class/include.php';
include './auth.php';
?>

<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Old Battery Report | <?php echo $COMPANY_PROFILE_DETAILS->name ?></title>
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
                    <div class="row mb-4">
                        <div class="col-md-8 d-flex align-items-center flex-wrap gap-2">
                            <a href="#" class="btn btn-primary" id="view_old_battery_report">
                                <i class="uil uil-eye me-1"></i> View Report
                            </a>
                            <a href="#" class="btn btn-success" id="print_old_battery_report">
                                <i class="uil uil-print me-1"></i> Print
                            </a>
                        </div>

                        <div class="col-md-4 text-md-end text-start mt-3 mt-md-0">
                            <ol class="breadcrumb m-0 justify-content-md-end">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                                <li class="breadcrumb-item active">Old Battery Report</li>
                            </ol>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="p-4">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0 me-3">
                                            <div class="avatar-xs">
                                                <div class="avatar-title rounded-circle bg-soft-primary text-primary">
                                                    01
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 overflow-hidden">
                                            <h5 class="font-size-16 mb-1">Old Battery Report</h5>
                                            <p class="text-muted text-truncate mb-0">Select date range to view old batteries sold</p>
                                        </div>
                                    </div>

                                    <div class="p-4">
                                        <form id="form-data" autocomplete="off">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <label for="from_date" class="form-label">From Date</label>
                                                    <div class="input-group">
                                                        <input type="text" class="form-control date-picker"
                                                            id="from_date" name="from_date">
                                                        <span class="input-group-text"><i class="mdi mdi-calendar"></i></span>
                                                    </div>
                                                </div>

                                                <div class="col-md-3">
                                                    <label for="to_date" class="form-label">To Date</label>
                                                    <div class="input-group">
                                                        <input type="text" class="form-control date-picker"
                                                            id="to_date" name="to_date">
                                                        <span class="input-group-text"><i class="mdi mdi-calendar"></i></span>
                                                    </div>
                                                </div>

                                                <div class="col-md-3">
                                                    <label for="department_id" class="form-label">Department</label>
                                                    <select id="department_id" name="department_id" class="form-select">
                                                        <option value="0">-- All Departments --</option>
                                                        <?php
                                                        $DEPARTMENT_MASTER = new DepartmentMaster(NULL);
                                                        foreach ($DEPARTMENT_MASTER->getActiveDepartment() as $departments) {
                                                            if ($US->type != 1) {
                                                                if ($departments['id'] == $US->department_id) {
                                                        ?>
                                                                    <option value="<?php echo $departments['id'] ?>">
                                                                        <?php echo $departments['name'] ?>
                                                                    </option>
                                                        <?php
                                                                }
                                                            } else {
                                                        ?>
                                                                <option value="<?php echo $departments['id'] ?>">
                                                                    <?php echo $departments['name'] ?>
                                                                </option>
                                                        <?php
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                </div>

                                                <div class="col-md-3 d-flex align-items-end">
                                                    <div class="form-check form-switch mt-2">
                                                        <input class="form-check-input" type="checkbox" id="detailed_view" name="detailed_view" value="1">
                                                        <label class="form-check-label fw-bold" for="detailed_view">
                                                            Full Detailed Report
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>

                                        <hr class="my-4">

                                        <div id="oldBatteryReportDateRange" class="mb-3"></div>

                                        <div class="table-responsive">
                                            <table class="table table-bordered" id="oldBatteryReport">
                                                <thead class="table-light" id="oldBatteryReportHead">
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Invoice No</th>
                                                        <th>Date</th>
                                                        <th>Customer</th>
                                                        <th>Department</th>
                                                        <th class="text-end">Old Battery Qty</th>
                                                        <th class="text-end">Total Amount</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td colspan="7" class="text-center text-muted">Select a date range and click View Report</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="rightbar-overlay"></div>

    <script src="assets/libs/jquery/jquery.min.js"></script>
    <script src="ajax/js/old-battery-report.js?v=<?php echo @filemtime(__DIR__ . '/ajax/js/old-battery-report.js'); ?>"></script>
    <?php include 'main-js.php' ?>
    <script src="assets/js/app.js"></script>
    <script src="https://code.jquery.com/ui/1.14.1/jquery-ui.js"></script>
    <script>
        $(function () {
            $(".date-picker").datepicker({
                dateFormat: 'yy-mm-dd'
            });
            var today = $.datepicker.formatDate('yy-mm-dd', new Date());
            $(".date-picker").val(today);
        });
    </script>

</body>

</html>
