<!doctype html>
<?php
include 'class/include.php';
include 'auth.php';
?>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Qty Base Discount | <?php echo $COMPANY_PROFILE_DETAILS->name ?> </title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="<?php echo $COMPANY_PROFILE_DETAILS->name ?>" name="author" />
    <!-- include main CSS -->
    <?php include 'main-css.php' ?>
</head>

<body data-layout="horizontal" data-topbar="colored" class="someBlock">

    <!-- Begin page -->
    <div id="layout-wrapper">

        <?php include 'navigation.php' ?>

        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">

                    <!-- page header -->
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
                                <a href="#" class="btn btn-danger delete-discount" style="display: none;">
                                    <i class="uil uil-trash-alt me-1"></i> Delete
                                </a>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-4 text-md-end text-start mt-3 mt-md-0">
                            <ol class="breadcrumb m-0 justify-content-md-end">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                                <li class="breadcrumb-item active">Qty Base Discount</li>
                            </ol>
                        </div>
                    </div>

                    <!-- main form -->
                    <div class="row">
                        <div class="col-lg-12">
                            <div id="discount-accordion" class="custom-accordion">
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
                                                <h5 class="font-size-16 mb-1">Qty Base Discount</h5>
                                                <p class="text-muted text-truncate mb-0">Fill details for quantity-based discounts</p>
                                            </div>
                                            
                                        </div>
                                    </div>

                                    <div class="p-4">
                                        <form id="form-data" method="post" enctype="multipart/form-data" autocomplete="off">
                                            <div class="row">

                                                <!-- Brand -->
                                                <div class="col-md-2 mb-3">
                                                    <label for="brand_id" class="form-label">Brand <span class="text-danger">*</span></label>
                                                    <select id="brand_id" name="brand_id" class="form-select" required>
                                                        <option value="">-- Select Brand --</option>
                                                        <?php
                                                        $BRAND = new Brand(NULL);
                                                        foreach ($BRAND->all() as $brand) {
                                                            echo "<option value='{$brand['id']}'>{$brand['name']}</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                </div>

                                                <!-- Date -->
                                                <div class="col-md-2 col-sm-6 mb-3">
                                                    <label for="period_year" class="form-label">Date <span class="text-danger">*</span></label>
                                                    <input id="period_year" name="period_year" type="text" class="form-control date-picker-date" placeholder="Select Date" required readonly style="background-color: #fff; cursor: pointer;">
                                                </div>

                                                <!-- Month Count -->
                                                <div class="col-md-2 col-sm-6 mb-3">
                                                    <label for="period_month" class="form-label">Month Count <span class="text-danger">*</span></label>
                                                    <input id="period_month" name="period_month" type="number" min="1" step="1" class="form-control" placeholder="Month Count" required>
                                                </div>

                                                <!-- Qty Min -->
                                                <div class="col-md-2 col-sm-6 mb-3">
                                                    <label for="qty" class="form-label">Qty Min <span class="text-danger">*</span></label>
                                                    <input id="qty" name="qty" type="number" step="1" min="0" class="form-control" placeholder="Qty Min" required>
                                                </div>

                                                <!-- Qty Max -->
                                                <div class="col-md-2 col-sm-6 mb-3">
                                                    <label for="qty_max" class="form-label">Qty Max</label>
                                                    <input id="qty_max" name="qty_max" type="number" step="1" min="0" class="form-control" placeholder="Qty Max (Optional)">
                                                </div>

                                                <!-- Net Discount -->
                                                <div class="col-md-2 col-sm-6 mb-3">
                                                    <label for="net_discount" class="form-label">Net Discount (%) <span class="text-danger">*</span></label>
                                                    <input id="net_discount" name="net_discount" type="number" step="0.01" min="0" class="form-control" placeholder="Net Discount" required>
                                                </div>

                                                <input type="hidden" id="id" name="id">
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- discount list -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <table class="datatable table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                        <thead>
                                            <tr>
                                                <th>#ID</th>
                                                <th>Brand</th>
                                                <th>Date</th>
                                                <th>Month Count</th>
                                                <th>Qty Min</th>
                                                <th>Qty Max</th>
                                                <th>Net Discount</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $QTY_DIS = new QtyBaseDiscount(NULL);
                                            foreach ($QTY_DIS->all() as $key => $dis) {
                                                $key++;
                                                $BRAND = new Brand($dis['brand_id']);
                                            ?>
                                                <tr class="select-dis" 
                                                    data-id="<?php echo $dis['id']; ?>"
                                                    data-brand="<?php echo $dis['brand_id']; ?>"
                                                    data-year="<?php echo htmlspecialchars($dis['period_year']); ?>"
                                                    data-month="<?php echo htmlspecialchars($dis['period_month']); ?>"
                                                    data-qty="<?php echo htmlspecialchars($dis['qty']); ?>"
                                                    data-qty_max="<?php echo htmlspecialchars($dis['qty_max']); ?>"
                                                    data-net_discount="<?php echo htmlspecialchars($dis['net_discount']); ?>">

                                                    <td><?php echo $key; ?></td>
                                                    <td><?php echo htmlspecialchars($BRAND->name); ?></td>
                                                    <td><?php echo htmlspecialchars($dis['period_year']); ?></td>
                                                    <td><?php echo htmlspecialchars($dis['period_month']); ?></td>
                                                    <td><?php echo number_format($dis['qty'], 2); ?></td>
                                                    <td><?php echo $dis['qty_max'] > 0 ? number_format($dis['qty_max'], 2) : '-'; ?></td>
                                                    <td><?php echo number_format($dis['net_discount'], 2); ?>%</td>
                                                    <td>
                                                        <button type="button" class="btn btn-primary btn-sm edit-dis" data-id="<?php echo $dis['id']; ?>">
                                                            <i class="mdi mdi-pencil"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                </div> <!-- container-fluid -->
            </div>

            <?php include 'footer.php' ?>
        </div>
    </div>

    <!-- Right bar overlay-->
    <div class="rightbar-overlay"></div>

    <!-- JAVASCRIPT -->
    <script src="assets/libs/jquery/jquery.min.js"></script>
    <script src="ajax/js/qty-base-discount.js"></script>
    
    <?php include 'main-js.php' ?>

</body>
</html>
