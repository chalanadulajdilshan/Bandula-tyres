jQuery(document).ready(function () {

    // Create Loan Battery
    $("#create").click(function (event) {
        event.preventDefault();

        if (!$('#name').val() || $('#name').val().length === 0) {
            swal({ title: "Error!", text: "Please enter battery name", type: 'error', timer: 2000, showConfirmButton: false });
            return false;
        }

        $('.someBlock').preloader();

        var formData = new FormData($("#form-data")[0]);
        formData.append('create', true);

        $.ajax({
            url: "ajax/php/loan-battery.php",
            type: 'POST',
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            dataType: "JSON",
            success: function (result) {
                $('.someBlock').preloader('remove');
                if (result.status === 'success') {
                    swal({ title: "Success!", text: "Loan battery added successfully!", type: 'success', timer: 2000, showConfirmButton: false });
                    window.setTimeout(function () { window.location.reload(); }, 2000);
                } else {
                    swal({ title: "Error!", text: "Something went wrong.", type: 'error', timer: 2000, showConfirmButton: false });
                }
            }
        });
        return false;
    });

    // Update Loan Battery
    $("#update").click(function (event) {
        event.preventDefault();

        if (!$('#name').val() || $('#name').val().length === 0) {
            swal({ title: "Error!", text: "Please enter battery name", type: 'error', timer: 2000, showConfirmButton: false });
            return false;
        }

        $('.someBlock').preloader();

        var formData = new FormData($("#form-data")[0]);
        formData.append('update', true);

        $.ajax({
            url: "ajax/php/loan-battery.php",
            type: 'POST',
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            dataType: "JSON",
            success: function (result) {
                $('.someBlock').preloader('remove');
                if (result.status === 'success') {
                    swal({ title: "Success!", text: "Loan battery updated successfully!", type: 'success', timer: 2000, showConfirmButton: false });
                    window.setTimeout(function () { window.location.reload(); }, 2000);
                } else {
                    swal({ title: "Error!", text: "Something went wrong.", type: 'error', timer: 2000, showConfirmButton: false });
                }
            }
        });
        return false;
    });

    // Reset input fields
    $("#new").click(function (e) {
        e.preventDefault();
        $('#form-data')[0].reset();
        $('#loan_battery_id').val('');
        $("#create").show();
        $("#update").hide();
    });

    // Populate form from modal click
    $(document).on('click', '.select-loan-battery', function () {
        $('#loan_battery_id').val($(this).data('id'));
        $('#name').val($(this).data('name'));
        $('#make').val($(this).data('make'));
        $('#voltage').val($(this).data('voltage'));
        $('#battery_no').val($(this).data('battery_no'));

        $('#activeStatus').prop('checked', $(this).data('active') == 1);

        $("#create").hide();
        $("#update").show();
        $('#loan_battery_master').modal('hide');
    });

    // Delete Loan Battery
    $(document).on('click', '.delete-loan-battery', function (e) {
        e.preventDefault();

        var id = $('#loan_battery_id').val();
        var name = $('#name').val();

        if (!id || id === "") {
            swal({ title: "Error!", text: "Please select a battery first.", type: "error", timer: 2000, showConfirmButton: false });
            return;
        }

        swal({
            title: "Are you sure?",
            text: "Do you want to delete '" + name + "'?",
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
                    url: 'ajax/php/loan-battery.php',
                    type: 'POST',
                    data: { id: id, delete: true },
                    dataType: 'JSON',
                    success: function (response) {
                        $('.someBlock').preloader('remove');
                        if (response.status === 'success') {
                            swal({ title: "Deleted!", text: "Battery has been deleted.", type: "success", timer: 2000, showConfirmButton: false });
                            setTimeout(function () { window.location.reload(); }, 2000);
                        } else {
                            swal({ title: "Error!", text: "Something went wrong.", type: "error", timer: 2000, showConfirmButton: false });
                        }
                    }
                });
            }
        });
    });

});
