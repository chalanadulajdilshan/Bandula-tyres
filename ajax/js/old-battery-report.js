$(function () {
    $('#view_old_battery_report').on('click', function (e) {
        e.preventDefault();
        loadOldBatteryReport();
    });

    $('#detailed_view').on('change', function () {
        loadOldBatteryReport();
    });

    $('#from_date, #to_date, #department_id').on('change', function () {
        loadOldBatteryReport();
    });

    $('#print_old_battery_report').on('click', function (e) {
        e.preventDefault();
        const printContents = $('#oldBatteryReportDateRange').prop('outerHTML') +
            $('#oldBatteryReport').prop('outerHTML');
        const w = window.open('', '', 'width=900,height=700');
        w.document.write(`
            <html><head><title>Old Battery Report</title>
            <style>
                body { font-family: Arial, sans-serif; padding: 20px; }
                table { width: 100%; border-collapse: collapse; }
                th, td { border: 1px solid #333; padding: 6px 8px; font-size: 12px; }
                thead { background: #eee; }
                .text-end { text-align: right; }
                tfoot td, .summary-row td { font-weight: bold; background: #f1f1f1; }
            </style>
            </head><body>${printContents}</body></html>`);
        w.document.close();
        w.focus();
        setTimeout(function () { w.print(); w.close(); }, 250);
    });

    function loadOldBatteryReport() {
        const from_date = $('#from_date').val();
        const to_date = $('#to_date').val();
        const department_id = $('#department_id').val();
        const detailed = $('#detailed_view').is(':checked') ? 1 : 0;

        // Swap table headers based on mode
        if (detailed) {
            $('#oldBatteryReportHead').html(`
                <tr>
                    <th rowspan="2">#</th>
                    <th colspan="3" class="text-center">Invoice</th>
                    <th colspan="3" class="text-center">Customer</th>
                    <th rowspan="2">Department</th>
                    <th rowspan="2">Vehicle No</th>
                    <th colspan="5" class="text-center">New Battery (Item Sold)</th>
                    <th colspan="3" class="text-center">Old Battery</th>
                </tr>
                <tr>
                    <th>Invoice No</th>
                    <th>Date</th>
                    <th>Payment</th>
                    <th>Name</th>
                    <th>Mobile</th>
                    <th>Address</th>
                    <th>Item</th>
                    <th class="text-end">Qty</th>
                    <th class="text-end">List Price</th>
                    <th class="text-end">Sell Price</th>
                    <th class="text-end">Total</th>
                    <th class="text-end">Qty</th>
                    <th class="text-end">Price</th>
                    <th class="text-end">Total</th>
                </tr>`);
        } else {
            $('#oldBatteryReportHead').html(`
                <tr>
                    <th>#</th>
                    <th>Invoice No</th>
                    <th>Date</th>
                    <th>Customer</th>
                    <th>Department</th>
                    <th class="text-end">Old Battery Qty</th>
                    <th class="text-end">Total Amount</th>
                </tr>`);
        }

        const colspan = detailed ? 16 : 7;

        $.ajax({
            url: 'ajax/php/old-battery-report.php',
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'load_old_battery_report',
                from_date,
                to_date,
                department_id,
                detailed
            },
            beforeSend: function () {
                $('#oldBatteryReport tbody').html(
                    `<tr><td colspan="${colspan}" class="text-center text-muted">Loading...</td></tr>`
                );
            },
            success: function (response) {
                if (response.status !== 'success') {
                    $('#oldBatteryReport tbody').html(
                        `<tr><td colspan="${colspan}" class="text-danger text-center">${response.message || 'Failed to load report'}</td></tr>`
                    );
                    return;
                }

                const data = response.data || [];
                const grandQty = parseFloat(response.grand_qty) || 0;
                const grandAmount = parseFloat(response.grand_amount) || 0;
                let tbody = '';

                if (data.length === 0) {
                    tbody = `<tr><td colspan="${colspan}" class="text-center text-muted">No old battery sales found for this range</td></tr>`;
                } else {
                    $.each(data, function (i, row) {
                        const idx = i + 1;
                        if (detailed) {
                            const oldQty = parseFloat(row.old_battery_qty) || 0;
                            const oldPrice = parseFloat(row.old_battery_price) || 0;
                            const oldLine = parseFloat(row.line_total) || 0;
                            const newQty = parseFloat(row.new_battery_qty) || 0;
                            const newListPrice = parseFloat(row.new_battery_list_price) || 0;
                            const newSellPrice = parseFloat(row.new_battery_selling_price) || 0;
                            const newTotal = parseFloat(row.new_battery_total) || 0;
                            const paymentLabel = (row.payment_type == 1) ? 'CASH' : (row.payment_type == 2 ? 'CREDIT' : '');
                            const fmt = (v) => v.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                            tbody += `<tr>
                                <td>${idx}</td>
                                <td>${row.invoice_no || ''}</td>
                                <td>${row.invoice_date || ''}</td>
                                <td><span style="color:${paymentLabel === 'CASH' ? 'green' : 'blue'};font-weight:bold;">${paymentLabel}</span></td>
                                <td>${row.customer_name || ''}</td>
                                <td>${row.customer_mobile || ''}</td>
                                <td>${row.customer_address || ''}</td>
                                <td>${row.department_name || ''}</td>
                                <td>${row.inv_vehicle_no || ''}</td>
                                <td>${row.item_name || ''}</td>
                                <td class="text-end">${newQty}</td>
                                <td class="text-end">${fmt(newListPrice)}</td>
                                <td class="text-end">${fmt(newSellPrice)}</td>
                                <td class="text-end">${fmt(newTotal)}</td>
                                <td class="text-end">${oldQty}</td>
                                <td class="text-end">${fmt(oldPrice)}</td>
                                <td class="text-end">${fmt(oldLine)}</td>
                            </tr>`;
                        } else {
                            const qty = parseFloat(row.total_qty) || 0;
                            const amount = parseFloat(row.total_amount) || 0;
                            tbody += `<tr>
                                <td>${idx}</td>
                                <td>${row.invoice_no || ''}</td>
                                <td>${row.invoice_date || ''}</td>
                                <td>${row.customer_name || ''}</td>
                                <td>${row.department_name || ''}</td>
                                <td class="text-end">${qty}</td>
                                <td class="text-end">${amount.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
                            </tr>`;
                        }
                    });

                    // Grand total row
                    if (detailed) {
                        tbody += `<tr class="summary-row" style="font-weight:bold; background-color:#f1f1f1;">
                            <td colspan="14" class="text-end">Grand Total (Old Batteries)</td>
                            <td class="text-end">${grandQty}</td>
                            <td></td>
                            <td class="text-end">${grandAmount.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
                        </tr>`;
                    } else {
                        tbody += `<tr class="summary-row" style="font-weight:bold; background-color:#f1f1f1;">
                            <td colspan="5" class="text-end">Grand Total</td>
                            <td class="text-end">${grandQty}</td>
                            <td class="text-end">${grandAmount.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
                        </tr>`;
                    }
                }

                $('#oldBatteryReport tbody').html(tbody);

                const fromDate = $('#from_date').val();
                const toDate = $('#to_date').val();
                const modeLabel = detailed ? 'Detailed' : 'Summary';
                if (fromDate && toDate) {
                    $('#oldBatteryReportDateRange').html(
                        `<h6>Old Battery ${modeLabel} Report from <strong>${fromDate}</strong> to <strong>${toDate}</strong></h6>`
                    );
                } else {
                    $('#oldBatteryReportDateRange').html(
                        `<h6>Old Battery ${modeLabel} Report</h6>`
                    );
                }
            },
            error: function (xhr, status, error) {
                console.error('Error loading old battery report:', error);
                $('#oldBatteryReport tbody').html(
                    `<tr><td colspan="${colspan}" class="text-danger text-center">Error loading report</td></tr>`
                );
            }
        });
    }
});
