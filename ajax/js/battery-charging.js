jQuery(document).ready(function () {

    function calcTotal() {
        var acid     = parseFloat($("#acid").val()) || 0;
        var repairs  = parseFloat($("#repairs").val()) || 0;
        var charging = parseFloat($("#charging").val()) || 0;
        var total    = acid + repairs + charging;
        $("#total").val(total.toFixed(2));
    }

    $("#acid, #repairs, #charging").on("input change", calcTotal);

    // Create
    $("#create").click(function (e) {
        e.preventDefault();

        if (!$("#customer_name").val()) {
            swal({ title: "Error!", text: "Please enter Customer Name", type: "error", timer: 2000, showConfirmButton: false });
            return;
        }

        calcTotal();
        $(".someBlock").preloader();

        var formData = new FormData($("#form-data")[0]);
        formData.append("create", true);

        $.ajax({
            url: "ajax/php/battery-charging.php",
            type: "POST",
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            dataType: "json",
            success: function (result) {
                $(".someBlock").preloader("remove");
                if (result.status === "success") {
                    swal({ title: "Success!", text: "Battery Charging Bill saved! (" + result.invoice_no + ")", type: "success", timer: 2000, showConfirmButton: false });
                    setTimeout(function () {
                        window.open("battery-charging-print.php?id=" + result.id, "_blank");
                        window.location.reload();
                    }, 1500);
                } else {
                    swal({ title: "Error!", text: "Something went wrong.", type: "error", timer: 2000, showConfirmButton: false });
                }
            }
        });
    });

    // Update
    $("#update").click(function (e) {
        e.preventDefault();

        if (!$("#id").val() || $("#id").val() === "0") {
            swal({ title: "Error!", text: "Please select a record first", type: "error", timer: 2000, showConfirmButton: false });
            return;
        }

        calcTotal();
        $(".someBlock").preloader();

        var formData = new FormData($("#form-data")[0]);
        formData.append("update", true);

        $.ajax({
            url: "ajax/php/battery-charging.php",
            type: "POST",
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            dataType: "json",
            success: function (result) {
                $(".someBlock").preloader("remove");
                if (result.status === "success") {
                    swal({ title: "Success!", text: "Updated!", type: "success", timer: 1500, showConfirmButton: false });
                    setTimeout(function () { window.location.reload(); }, 1500);
                } else {
                    swal({ title: "Error!", text: "Something went wrong.", type: "error", timer: 2000, showConfirmButton: false });
                }
            }
        });
    });

    // Delete
    $(document).on("click", ".delete-battery-charging", function (e) {
        e.preventDefault();
        var id = $("#id").val();
        if (!id || id === "0") {
            swal({ title: "Error!", text: "Please select a record first", type: "error", timer: 2000, showConfirmButton: false });
            return;
        }
        swal({
            title: "Are you sure?",
            text: "Do you want to delete this record?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#6c757d",
            confirmButtonText: "Yes, delete it!",
            cancelButtonText: "Cancel",
            closeOnConfirm: false
        }, function (isConfirm) {
            if (isConfirm) {
                $(".someBlock").preloader();
                $.ajax({
                    url: "ajax/php/battery-charging.php",
                    type: "POST",
                    data: { id: id, delete: true },
                    dataType: "json",
                    success: function (response) {
                        $(".someBlock").preloader("remove");
                        if (response.status === "success") {
                            swal({ title: "Deleted!", text: "Record deleted.", type: "success", timer: 1500, showConfirmButton: false });
                            setTimeout(function () { window.location.reload(); }, 1500);
                        } else {
                            swal({ title: "Error!", text: "Something went wrong.", type: "error", timer: 2000, showConfirmButton: false });
                        }
                    }
                });
            }
        });
    });

    // Print button
    $("#print_bill").click(function (e) {
        e.preventDefault();
        var id = $("#id").val();
        if (!id || id === "0") {
            swal({ title: "Error!", text: "Please select / save a record first", type: "error", timer: 2000, showConfirmButton: false });
            return;
        }
        window.open("battery-charging-print.php?id=" + id, "_blank");
    });

    // New / reset
    $("#new").click(function (e) {
        e.preventDefault();
        window.location.reload();
    });

    // Select row from modal
    $(document).on("click", ".select-battery", function () {
        var d = $(this).data();
        $("#id").val(d.id);
        $("#invoice_no").val(d.invoice_no);
        $("#bill_date").val(d.bill_date);
        $("#customer_name").val(d.customer_name);
        $("#customer_address").val(d.address);
        $("#customer_code").val("");
        $("#customer_mobile").val("");
        $("#customer_id").val("");
        $("#deposit_amount").val(d.deposit_amount);
        $("#loan_hire_per_day").val(d.loan_hire_per_day);
        $("#ready_date").val(d.ready_date);
        $("#make").val(d.make);
        $("#voltage").val(d.voltage);
        $("#battery_no").val(d.battery_no);
        $("#loan_battery").val(d.loan_battery);
        $("#acid").val(d.acid);
        $("#repairs").val(d.repairs);
        $("#charging").val(d.charging);
        $("#total").val(d.total);

        $("#create").hide();
        $("#update").show();
        $("#print_bill").show();
        $(".bs-example-modal-xl").modal("hide");
    });

    // Select a loan battery from the master and auto-fill battery details
    $(document).on("click", ".select-loan-battery-row", function () {
        var d = $(this).data();
        $("#make").val(d.make);
        $("#voltage").val(d.voltage);
        $("#battery_no").val(d.battery_no);
        $("#loan_battery").val(d.name);
        $("#loanBatteryModal").modal("hide");
    });
});
