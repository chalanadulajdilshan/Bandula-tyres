jQuery(document).ready(function () {

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
                text: "Please select a year",
                type: 'error',
                timer: 2000,
                showConfirmButton: false
            });
        } else if (!$('#period_month').val() || $('#period_month').val().length === 0) {
            swal({
                title: "Error!",
                text: "Please select a month",
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
                text: "Please select a year",
                type: 'error',
                timer: 2000,
                showConfirmButton: false
            });
        } else if (!$('#period_month').val() || $('#period_month').val().length === 0) {
            swal({
                title: "Error!",
                text: "Please select a month",
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
        $('#period_year').prop('selectedIndex', 0);
        $('#period_month').prop('selectedIndex', 0);
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
        $('#period_year').val($this.data('year')).trigger('change');
        $('#period_month').val($this.data('month')).trigger('change');
        
        // Update qty & net discount
        $('#qty').val($this.data('qty'));
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
