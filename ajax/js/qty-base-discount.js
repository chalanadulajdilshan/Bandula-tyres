jQuery(document).ready(function () {

    // Auto-calculate Discount function
    function calculateDiscount() {
        var qtyMinVal = $('#qty').val();
        var qtyMaxVal = $('#qty_max').val();
        var monthCountVal = $('#period_month').val();

        if (qtyMinVal === '' && qtyMaxVal === '') {
            return;
        }

        var qtyMin = parseFloat(qtyMinVal) || 0;
        var qtyMax = parseFloat(qtyMaxVal) || 0;
        var monthCount = parseInt(monthCountVal) || 1;

        var discount = 0.0;

        // If two values are entered (range)
        if (qtyMinVal !== '' && qtyMaxVal !== '' && qtyMax > 0) {
            // Use Second Table
            if (monthCount === 3) {
                // 3-Month Target Column
                if (qtyMin >= 600 || qtyMax >= 600) {
                    discount = 6.0;
                } else if (qtyMin >= 480 && qtyMax <= 599) {
                    discount = 5.5;
                } else if (qtyMin >= 405 && qtyMax <= 479) {
                    discount = 5.0;
                } else if (qtyMin >= 330 && qtyMax <= 404) {
                    discount = 4.5;
                } else if (qtyMin >= 240 && qtyMax <= 329) {
                    discount = 4.0;
                } else if (qtyMin >= 165 && qtyMax <= 239) {
                    discount = 3.5;
                } else if (qtyMin >= 120 && qtyMax <= 164) {
                    discount = 3.0;
                } else if (qtyMin >= 75 && qtyMax <= 119) {
                    discount = 2.5;
                } else {
                    // Fallback to qtyMin
                    if (qtyMin >= 600) discount = 6.0;
                    else if (qtyMin >= 480) discount = 5.5;
                    else if (qtyMin >= 405) discount = 5.0;
                    else if (qtyMin >= 330) discount = 4.5;
                    else if (qtyMin >= 240) discount = 4.0;
                    else if (qtyMin >= 165) discount = 3.5;
                    else if (qtyMin >= 120) discount = 3.0;
                    else if (qtyMin >= 75) discount = 2.5;
                    else discount = 0.0;
                }
            } else {
                // Default/Monthly column of second table
                if (qtyMin >= 200 || qtyMax >= 200) {
                    discount = 6.0;
                } else if (qtyMin >= 160 && qtyMax <= 199) {
                    discount = 5.5;
                } else if (qtyMin >= 135 && qtyMax <= 159) {
                    discount = 5.0;
                } else if (qtyMin >= 110 && qtyMax <= 134) {
                    discount = 4.5;
                } else if (qtyMin >= 80 && qtyMax <= 109) {
                    discount = 4.0;
                } else if (qtyMin >= 55 && qtyMax <= 79) {
                    discount = 3.5;
                } else if (qtyMin >= 40 && qtyMax <= 54) {
                    discount = 3.0;
                } else if (qtyMin >= 25 && qtyMax <= 39) {
                    discount = 2.5;
                } else {
                    // Fallback to qtyMin
                    if (qtyMin >= 200) discount = 6.0;
                    else if (qtyMin >= 160) discount = 5.5;
                    else if (qtyMin >= 135) discount = 5.0;
                    else if (qtyMin >= 110) discount = 4.5;
                    else if (qtyMin >= 80) discount = 4.0;
                    else if (qtyMin >= 55) discount = 3.5;
                    else if (qtyMin >= 40) discount = 3.0;
                    else if (qtyMin >= 25) discount = 2.5;
                    else discount = 0.0;
                }
            }
        } else {
            // Use First Table
            var checkQty = qtyMin > 0 ? qtyMin : qtyMax;
            if (checkQty >= 100) {
                discount = 14.0;
            } else if (checkQty >= 51) {
                discount = 11.0;
            } else if (checkQty >= 31) {
                discount = 9.0;
            } else if (checkQty >= 10) {
                discount = 7.0;
            } else {
                discount = 0.0;
            }
        }

        $('#net_discount').val(discount.toFixed(2));
    }

    // Attach change triggers
    $(document).on('input change', '#qty, #qty_max, #period_month', function () {
        calculateDiscount();
    });

    // Create Qty Base Discount
    $("#create").click(function (event) {
        event.preventDefault();

        // Validation
        if (!$('#brand_id').val() || $('#brand_id').val().length === 0) {
            swal({
                title: "Error!",
                text: "Please select a brand",
                type: 'error',
                timer: 2000,
                showConfirmButton: false
            });
        } else if (!$('#period_year').val() || $('#period_year').val().length === 0) {
            swal({
                title: "Error!",
                text: "Please select date",
                type: 'error',
                timer: 2000,
                showConfirmButton: false
            });
        } else if (!$('#period_month').val() || $('#period_month').val().length === 0) {
            swal({
                title: "Error!",
                text: "Please enter month count",
                type: 'error',
                timer: 2000,
                showConfirmButton: false
            });
        } else if (!$('#qty').val() || $('#qty').val().length === 0) {
            swal({
                title: "Error!",
                text: "Please enter quantity",
                type: 'error',
                timer: 2000,
                showConfirmButton: false
            });
        } else if (!$('#net_discount').val() || $('#net_discount').val().length === 0) {
            swal({
                title: "Error!",
                text: "Please enter net discount",
                type: 'error',
                timer: 2000,
                showConfirmButton: false
            });
        } else {

            $('.someBlock').preloader();

            var formData = new FormData($("#form-data")[0]);
            formData.append('create', true);

            $.ajax({
                url: "ajax/php/qty-base-discount.php",
                type: 'POST',
                data: formData,
                async: false,
                cache: false,
                contentType: false,
                processData: false,
                dataType: "JSON",
                success: function (result) {
                    $('.someBlock').preloader('remove');

                    if (result.status === 'success') {
                        swal({
                            title: "Success!",
                            text: "Discount added successfully!",
                            type: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        });

                        setTimeout(function () {
                            window.location.reload();
                        }, 2000);
                    } else {
                        swal({
                            title: "Error!",
                            text: "Something went wrong.",
                            type: 'error',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }
                }
            });
        }
        return false;
    });

    // Update Qty Base Discount
    $("#update").click(function (event) {
        event.preventDefault();

        if (!$('#brand_id').val() || $('#brand_id').val().length === 0) {
            swal({
                title: "Error!",
                text: "Please select a brand",
                type: 'error',
                timer: 2000,
                showConfirmButton: false
            });
        } else if (!$('#period_year').val() || $('#period_year').val().length === 0) {
            swal({
                title: "Error!",
                text: "Please select date",
                type: 'error',
                timer: 2000,
                showConfirmButton: false
            });
        } else if (!$('#period_month').val() || $('#period_month').val().length === 0) {
            swal({
                title: "Error!",
                text: "Please enter month count",
                type: 'error',
                timer: 2000,
                showConfirmButton: false
            });
        } else if (!$('#qty').val() || $('#qty').val().length === 0) {
            swal({
                title: "Error!",
                text: "Please enter quantity",
                type: 'error',
                timer: 2000,
                showConfirmButton: false
            });
        } else if (!$('#net_discount').val() || $('#net_discount').val().length === 0) {
            swal({
                title: "Error!",
                text: "Please enter net discount",
                type: 'error',
                timer: 2000,
                showConfirmButton: false
            });
        } else {

            $('.someBlock').preloader();

            var formData = new FormData($("#form-data")[0]);
            formData.append('update', true);

            $.ajax({
                url: "ajax/php/qty-base-discount.php",
                type: 'POST',
                data: formData,
                async: false,
                cache: false,
                contentType: false,
                processData: false,
                dataType: "JSON",
                success: function (result) {
                    $('.someBlock').preloader('remove');

                    if (result.status == 'success') {
                        swal({
                            title: "Success!",
                            text: "Discount updated successfully!",
                            type: 'success',
                            timer: 2500,
                            showConfirmButton: false
                        });

                        setTimeout(function () {
                            window.location.reload();
                        }, 2000);
                    } else {
                        swal({
                            title: "Error!",
                            text: "Something went wrong.",
                            type: 'error',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }
                }
            });
        }
        return false;
    });

    // Reset input fields
    $("#new").click(function (e) {
        e.preventDefault();
        $('#form-data')[0].reset();
        $('#brand_id').prop('selectedIndex', 0);
        $('#period_year').val('');
        $('#period_month').val('');
        $('#qty').val('');
        $('#qty_max').val('');
        $('#net_discount').val('');
        $('#id').val('');
        $("#create").show();
        $("#update").hide();
        $(".delete-discount").hide();
    });

    // Populate form from table click
    $(document).on('click', '.select-dis', function () {
        var $this = $(this);
        $('#id').val($this.data('id'));
        
        // Update brand select
        var brandId = $this.data('brand');
        $('#brand_id').val(brandId).trigger('change');
        
        // Update year & month
        $('#period_year').val($this.data('year'));
        $('#period_month').val($this.data('month'));
        
        // Update qty & net discount
        $('#qty').val($this.data('qty'));
        $('#qty_max').val($this.data('qty_max') > 0 ? $this.data('qty_max') : '');
        $('#net_discount').val($this.data('net_discount'));

        // Show update button and hide create button
        $("#create").hide();
        $("#update").show();
        $(".delete-discount").show();
    });

    // Delete Discount
    $(document).on('click', '.delete-discount', function (e) {
        e.preventDefault();

        var disId = $('#id').val();

        if (!disId || disId === "") {
            swal({
                title: "Error!",
                text: "Please select a discount first.",
                type: "error",
                timer: 2000,
                showConfirmButton: false
            });
            return;
        }

        swal({
            title: "Are you sure?",
            text: "Do you want to delete this discount?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#6c757d",
            confirmButtonText: "Yes, delete it!",
            cancelButtonText: "Cancel",
            closeOnConfirm: false
        }, function (isConfirm) {
            if (isConfirm) {
                $('.someBlock').preloader();

                $.ajax({
                    url: 'ajax/php/qty-base-discount.php',
                    type: 'POST',
                    data: {
                        id: disId,
                        delete: true
                    },
                    dataType: 'JSON',
                    success: function (response) {
                        $('.someBlock').preloader('remove');

                        if (response.status === 'success') {
                            swal({
                                title: "Deleted!",
                                text: "Discount has been deleted.",
                                type: "success",
                                timer: 2000,
                                showConfirmButton: false
                            });

                            setTimeout(() => {
                                window.location.reload();
                            }, 2000);

                        } else {
                            swal({
                                title: "Error!",
                                text: "Something went wrong.",
                                type: "error",
                                timer: 2000,
                                showConfirmButton: false
                            });
                        }
                    }
                });
            }
        });
    });

});
