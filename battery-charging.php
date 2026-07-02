<!doctype html>
<?php
include 'class/include.php';
include 'auth.php';

$BC_TMP = new BatteryCharging(null);
$nextInvoice = $BC_TMP->nextInvoiceNo();
?>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Battery Charging | <?php echo $COMPANY_PROFILE_DETAILS->name ?></title>
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
                            <a href="#" class="btn btn-success" id="new">
                                <i class="uil uil-plus me-1"></i> New
                            </a>
                            <a href="#" class="btn btn-primary" id="create">
                                <i class="uil uil-save me-1"></i> Save
                            </a>
                            <a href="#" class="btn btn-warning" id="update" style="display:none;">
                                <i class="uil uil-edit me-1"></i> Update
                            </a>
                            <a href="#" class="btn btn-dark" id="print_bill" style="display:none;">
                                <i class="uil uil-print me-1"></i> Print
                            </a>
                            <a href="#" class="btn btn-danger delete-battery-charging">
                                <i class="uil uil-trash-alt me-1"></i> Delete
                            </a>
                        </div>

                        <div class="col-md-4 text-md-end text-start mt-3 mt-md-0">
                            <ol class="breadcrumb m-0 justify-content-md-end">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                                <li class="breadcrumb-item active">Battery Charging</li>
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
                                                    <i class="uil uil-battery-bolt"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 overflow-hidden">
                                            <h5 class="font-size-16 mb-1">Battery Charging Bill</h5>
                                            <p class="text-muted text-truncate mb-0">Fill all information below</p>
                                        </div>
                                        <div class="flex-shrink-0">
                                            <button type="button" class="btn btn-info"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#bcModel">
                                                <i class="uil uil-list-ul me-1"></i> View All
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="p-4 pt-0">
                                    <form id="form-data" autocomplete="off">
                                        <input type="hidden" id="id" name="id" value="0">
                                        <input type="hidden" id="customer_id">

                                        <!-- Section: Invoice details -->
                                        <div class="border rounded p-3 mb-3 bg-light">
                                            <h6 class="text-uppercase text-muted fw-bold mb-3">Invoice Details</h6>
                                            <div class="row g-3">
                                                <div class="col-md-4">
                                                    <label class="form-label">Invoice No</label>
                                                    <input id="invoice_no" name="invoice_no" type="text"
                                                           value="<?php echo $nextInvoice; ?>"
                                                           class="form-control fw-bold" readonly>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label">Date</label>
                                                    <input id="bill_date" name="bill_date" type="date"
                                                           value="<?php echo date('Y-m-d'); ?>" class="form-control">
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label">Battery Ready On</label>
                                                    <input id="ready_date" name="ready_date" type="date" class="form-control">
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label">Deposit Amount</label>
                                                    <input id="deposit_amount" name="deposit_amount" type="number" step="0.01"
                                                           value="0" class="form-control text-end">
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label">Loan Hire / Day</label>
                                                    <input id="loan_hire_per_day" name="loan_hire_per_day" type="number" step="0.01"
                                                           value="0" class="form-control text-end">
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Section: Customer -->
                                        <div class="border rounded p-3 mb-3">
                                            <h6 class="text-uppercase text-muted fw-bold mb-3">Customer</h6>
                                            <div class="row g-3">
                                                <div class="col-md-3">
                                                    <label class="form-label">Customer Code</label>
                                                    <div class="input-group">
                                                        <input id="customer_code" type="text" class="form-control" placeholder="Code" readonly>
                                                        <button class="btn btn-info" type="button" data-bs-toggle="modal" data-bs-target="#customerModal" title="Search Customer">
                                                            <i class="uil uil-search"></i>
                                                        </button>
                                                        <?php
                                                        $hasAddCustomerPermission = false;
                                                        if (isset($_SESSION['id'])) {
                                                            $specialPermission = new SpecialUserPermission();
                                                            $hasAddCustomerPermission = $specialPermission->hasAccess($_SESSION['id'], 'add_customer');
                                                        }
                                                        ?>
                                                        <button class="btn btn-danger" type="button" title="Add New Customer"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#customerAddModal"
                                                                style="display: <?php echo $hasAddCustomerPermission ? 'inline-block' : 'none'; ?>">
                                                            <i class="uil uil-plus"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="col-md-5">
                                                    <label class="form-label">Customer Name</label>
                                                    <input id="customer_name" name="customer_name" type="text" class="form-control" placeholder="Customer name" readonly>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label">Mobile</label>
                                                    <input id="customer_mobile" type="text" class="form-control" readonly>
                                                </div>
                                                <div class="col-md-12">
                                                    <label class="form-label">Address</label>
                                                    <input id="customer_address" name="address" type="text" class="form-control" placeholder="Address" readonly>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Section: Battery details -->
                                        <div class="border rounded p-3 mb-3">
                                            <h6 class="text-uppercase text-muted fw-bold mb-3">Battery Details</h6>
                                            <div class="row g-3">
                                                <div class="col-md-3">
                                                    <label class="form-label">Make</label>
                                                    <input id="make" name="make" type="text" class="form-control">
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label">Voltage</label>
                                                    <input id="voltage" name="voltage" type="text" class="form-control">
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label">Battery No</label>
                                                    <input id="battery_no" name="battery_no" type="text" class="form-control">
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label">Loan Battery</label>
                                                    <input id="loan_battery" name="loan_battery" type="text" class="form-control">
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Section: Charges -->
                                        <div class="border rounded p-3 mb-2 bg-light">
                                            <h6 class="text-uppercase text-muted fw-bold mb-3">Charges</h6>
                                            <div class="row g-3 align-items-end">
                                                <div class="col-md-3">
                                                    <label class="form-label">Acid</label>
                                                    <input id="acid" name="acid" type="number" step="0.01"
                                                           value="0" class="form-control text-end">
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label">Repairs</label>
                                                    <input id="repairs" name="repairs" type="number" step="0.01"
                                                           value="0" class="form-control text-end">
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label">Charging</label>
                                                    <input id="charging" name="charging" type="number" step="0.01"
                                                           value="0" class="form-control text-end">
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label fw-bold text-primary">TOTAL</label>
                                                    <input id="total" name="total" type="number" step="0.01"
                                                           value="0"
                                                           class="form-control form-control-lg text-end fw-bold text-primary border-primary"
                                                           readonly>
                                                </div>
                                            </div>
                                        </div>

                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php include 'footer.php' ?>
        </div>
    </div>

    <!-- Listing modal -->
    <div class="modal fade bs-example-modal-xl" id="bcModel" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Battery Charging Bills</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <table class="datatable table table-bordered dt-responsive nowrap" style="width:100%;">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Invoice</th>
                                <th>Date</th>
                                <th>Customer</th>
                                <th>Make</th>
                                <th>Battery No</th>
                                <th>Total</th>
                                <th>Print</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $BC = new BatteryCharging(null);
                            foreach ($BC->all() as $key => $row) {
                                $key++;
                                ?>
                                <tr class="select-battery" style="cursor:pointer;"
                                    data-id="<?php echo $row['id']; ?>"
                                    data-invoice_no="<?php echo htmlspecialchars($row['invoice_no']); ?>"
                                    data-bill_date="<?php echo htmlspecialchars($row['bill_date']); ?>"
                                    data-customer_name="<?php echo htmlspecialchars($row['customer_name']); ?>"
                                    data-address="<?php echo htmlspecialchars($row['address']); ?>"
                                    data-deposit_amount="<?php echo $row['deposit_amount']; ?>"
                                    data-loan_hire_per_day="<?php echo $row['loan_hire_per_day']; ?>"
                                    data-ready_date="<?php echo htmlspecialchars($row['ready_date']); ?>"
                                    data-make="<?php echo htmlspecialchars($row['make']); ?>"
                                    data-voltage="<?php echo htmlspecialchars($row['voltage']); ?>"
                                    data-battery_no="<?php echo htmlspecialchars($row['battery_no']); ?>"
                                    data-loan_battery="<?php echo htmlspecialchars($row['loan_battery']); ?>"
                                    data-acid="<?php echo $row['acid']; ?>"
                                    data-repairs="<?php echo $row['repairs']; ?>"
                                    data-charging="<?php echo $row['charging']; ?>"
                                    data-total="<?php echo $row['total']; ?>">
                                    <td><?php echo $key; ?></td>
                                    <td><?php echo htmlspecialchars($row['invoice_no']); ?></td>
                                    <td><?php echo htmlspecialchars($row['bill_date']); ?></td>
                                    <td><?php echo htmlspecialchars($row['customer_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['make']); ?></td>
                                    <td><?php echo htmlspecialchars($row['battery_no']); ?></td>
                                    <td class="text-end"><?php echo number_format((float)$row['total'], 2); ?></td>
                                    <td>
                                        <a href="battery-charging-print.php?id=<?php echo $row['id']; ?>"
                                           target="_blank" class="btn btn-sm btn-dark"
                                           onclick="event.stopPropagation();">
                                            <i class="uil uil-print"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="rightbar-overlay"></div>

    <script src="assets/libs/jquery/jquery.min.js"></script>

    <?php include 'main-js.php' ?>

    <script src="ajax/js/common.js"></script>
    <script src="ajax/js/customer-master.js"></script>
    <script src="ajax/js/battery-charging.js"></script>
</body>
</html>
