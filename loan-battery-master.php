<!doctype html>
<?php
include 'class/include.php';
include 'auth.php';
?>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Loan Battery Master | <?php echo $COMPANY_PROFILE_DETAILS->name ?> </title>
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

                            <?php if ($PERMISSIONS['add_page']): ?>
                                <a href="#" class="btn btn-primary" id="create">
                                    <i class="uil uil-save me-1"></i> Save
                                </a>
                            <?php endif; ?>

                            <?php if ($PERMISSIONS['edit_page']): ?>
                                <a href="#" class="btn btn-warning" id="update" style="display: none;">
                                    <i class="uil uil-edit me-1"></i> Update
                                </a>
                            <?php endif; ?>

                            <?php if ($PERMISSIONS['delete_page']): ?>
                                <a href="#" class="btn btn-danger delete-loan-battery">
                                    <i class="uil uil-trash-alt me-1"></i> Delete
                                </a>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-4 text-md-end text-start mt-3 mt-md-0">
                            <ol class="breadcrumb m-0 justify-content-md-end">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                                <li class="breadcrumb-item active">Loan Battery Master</li>
                            </ol>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-12">
                            <div id="addproduct-accordion" class="custom-accordion">
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
                                                <h5 class="font-size-16 mb-1">Loan Battery Master</h5>
                                                <p class="text-muted text-truncate mb-0">Fill all information below to manage loan batteries</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="p-4">
                                        <form id="form-data" method="post" autocomplete="off">
                                            <div class="row g-3">

                                                <div class="col-md-3">
                                                    <label class="form-label" for="name">Battery Name <span class="text-danger">*</span></label>
                                                    <div class="input-group">
                                                        <input id="name" name="name" type="text" class="form-control" placeholder="Enter Battery Name">
                                                        <button class="btn btn-info" type="button" data-bs-toggle="modal" data-bs-target="#loan_battery_master" title="Search">
                                                            <i class="uil uil-search"></i>
                                                        </button>
                                                    </div>
                                                </div>

                                                <div class="col-md-3">
                                                    <label class="form-label" for="make">Make</label>
                                                    <input id="make" name="make" type="text" class="form-control" placeholder="Enter Make">
                                                </div>

                                                <div class="col-md-2">
                                                    <label class="form-label" for="voltage">Voltage</label>
                                                    <input id="voltage" name="voltage" type="text" class="form-control" placeholder="Enter Voltage">
                                                </div>

                                                <div class="col-md-3">
                                                    <label class="form-label" for="battery_no">Battery No</label>
                                                    <input id="battery_no" name="battery_no" type="text" class="form-control" placeholder="Enter Battery No">
                                                </div>

                                                <div class="col-md-1 d-flex justify-content-center align-items-end">
                                                    <div class="form-check mb-2">
                                                        <input class="form-check-input" type="checkbox" id="activeStatus" name="activeStatus" value="1" checked>
                                                        <label class="form-check-label" for="activeStatus">Active</label>
                                                    </div>
                                                </div>

                                                <input type="hidden" id="loan_battery_id" name="loan_battery_id">
                                            </div>
                                        </form>
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

    <!-- Listing modal -->
    <div class="modal fade" id="loan_battery_master" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Loan Batteries</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <table class="datatable table table-bordered dt-responsive nowrap" style="width:100%;">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Battery Name</th>
                                <th>Make</th>
                                <th>Voltage</th>
                                <th>Battery No</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $LB = new LoanBattery(NULL);
                            foreach ($LB->all() as $key => $battery) {
                                $key++;
                                ?>
                                <tr class="select-loan-battery" style="cursor:pointer;"
                                    data-id="<?php echo $battery['id']; ?>"
                                    data-name="<?php echo htmlspecialchars($battery['name']); ?>"
                                    data-make="<?php echo htmlspecialchars($battery['make']); ?>"
                                    data-voltage="<?php echo htmlspecialchars($battery['voltage']); ?>"
                                    data-battery_no="<?php echo htmlspecialchars($battery['battery_no']); ?>"
                                    data-active="<?php echo $battery['is_active']; ?>">
                                    <td><?php echo $key; ?></td>
                                    <td><?php echo htmlspecialchars($battery['name']); ?></td>
                                    <td><?php echo htmlspecialchars($battery['make']); ?></td>
                                    <td><?php echo htmlspecialchars($battery['voltage']); ?></td>
                                    <td><?php echo htmlspecialchars($battery['battery_no']); ?></td>
                                    <td>
                                        <?php if ($battery['is_active'] == 1): ?>
                                            <span class="badge bg-soft-success font-size-12">Active</span>
                                        <?php else: ?>
                                            <span class="badge bg-soft-danger font-size-12">Inactive</span>
                                        <?php endif; ?>
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
    <script src="ajax/js/loan-battery.js"></script>

    <?php include 'main-js.php' ?>

</body>

</html>
