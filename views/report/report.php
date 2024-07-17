<form id="reportForm">
    <div class="row mb-3">
        <div class="col-12 mb-2">
            <h5>Transaction Date</h5>
        </div>
        <div class="col-2">
            <label class="form-label">Start Date</label>
            <input id="inputTransDateStart" type="date" class="form-control" onchange="recalibrateDate(event, 'inputTransDateEnd')">
        </div>
        <div class="col-2">
            <label class="form-label">End Date</label>
            <input id="inputTransDateEnd" type="date" class="form-control" onchange="recalibrateDate(event, 'inputTransDateStart')">
        </div>
    </div>
    <div class="row mb-4">
        <div class="col-12 mb-2">
            <h5>Payment Date</h5>
        </div>
        <div class="col-2">
            <label class="form-label">Start Date</label>
            <input id="inputPaymentDateStart" type="date" class="form-control" onchange="recalibrateDate(event, 'inputPaymentDateEnd')">
        </div>
        <div class="col-2">
            <label class="form-label">End Date</label>
            <input id="inputPaymentDateEnd" type="date" class="form-control" onchange="recalibrateDate(event, 'inputPaymentDateStart')">
        </div>
    </div>
    <div class="row">
        <div class="col-4">
            <button class="btn btn-dark">Generate Report</button>
        </div>
    </div>
</form>
<div>

</div>

<script>
    $('#reportForm').on('submit', function(e)
    {
        e.preventDefault();
        generateReport();
    });

    function generateReport()
    {
        var data = {
            transaction_date_start: $('#inputTransDateStart').val(),
            transaction_date_end: $('#inputTransDateEnd').val(),
            payment_date_start: $('#inputPaymentDateStart').val(),
            payment_date_end: $('#inputPaymentDateEnd').val()
        }
        if (validateForm())
        {
            generateExcelReport(data);
            // generatePdfReport(data);
        }
    }

    function generateExcelReport(data)
    {
        console.log(data);
        $.ajax({
            type		: 'POST',
            data		: data,
            dataType	: 'json',
            url			: '<?= Yii::$app->getUrlManager()->createUrl('report/generate-excel-report') ?>',
            success		: function(result)
            {
                
            }
        });
    }

    function generatePdfReport(data)
    {
        $.ajax({
            type		: 'POST',
            data		: data,
            dataType	: 'json',
            url			: '<?= Yii::$app->getUrlManager()->createUrl('report/generate-pdf-report') ?>',
            success		: function(result)
            {
                
            }
        });
    }

    function validateForm()
    {
        const TransDateStart = $('#inputTransDateStart').val();
        const TransDateEnd = $('#inputTransDateEnd').val();
        const PaymentDateStart = $('#inputPaymentDateStart').val();
        const PaymentDateEnd = $('#inputPaymentDateEnd').val();

        if (TransDateStart || PaymentDateStart)
        {
            if (TransDateStart && !TransDateEnd) {
                Swal.fire({
                    title: 'Wait',
                    text : 'Transaction date end must be filled',
                    icon : 'error',
                    timer: 2500
                });
                
                return false;
            }

            if (PaymentDateStart && !PaymentDateEnd) {
                Swal.fire({
                    title: 'Wait',
                    text : 'Payment date end must be filled',
                    icon : 'error',
                    timer: 2500
                });
                
                return false;
            }
        }
        else
        {
            Swal.fire({
                title: 'Wait',
                text : 'Either transaction or payment date must be filled',
                icon : 'error',
                timer: 2500
            });

            return false;
        }

        return true;
    }

    function recalibrateDate(e, target)
    {
        const valueEl = $(e.target);
        const targetEl = $(`#${target}`);
        const value = valueEl.val();
        const targetValue = targetEl.val();
        if (target === "inputTransDateStart" || target === "inputPaymentDateStart")
        {
            // END DATE LESS THAN START
            if (!targetValue || moment(value).isBefore(moment(targetValue)))
            {
                targetEl.val(value);
            }   
        }

        if (target === "inputTransDateEnd" || target === "inputPaymentDateEnd")
        {
            if (!targetValue || moment(value).isAfter(moment(targetValue)))
            {
                targetEl.val(value);
            }
        }
    }
</script>