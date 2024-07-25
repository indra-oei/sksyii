<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction Report</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
        }

        header {
            margin-bottom: 3rem;
        }

        header>div {
            margin-bottom: 0.5rem;
        }

        header>div:last-child {
            margin-bottom: 0px;
        }

        .reportHeaderTitle {
            font-size: 30px;
        }

        .printInfo {
            display: -webkit-box;
            display: flex;
            align-items: center;
            -webkit-box-pack: center;
            gap: 2.5rem;
        }
    </style>
</head>

<body style="font-family: Arial, Helvetica, sans-serif;">
    <header class="text-center" style="margin-bottom: 3rem;">
        <h1 style="font-size: 30px;">Transaction Report</h1>
        <div style="margin-bottom: 0.5rem;">
            <p class="m-0"><strong>Transaction Date</strong></p>
            <p class="m-0"><?= $data['transaction_date'] ?></p>
        </div>
        <div style="margin-bottom: 0.5rem;">
            <p class="m-0"><strong>Payment Date</strong></p>
            <p class="m-0"><?= $data['payment_date'] ?></p>
        </div>
        <div class="margin-bottom: 0.5rem">
            <p class="m-0"><strong>Printed By</strong></p>
            <p class="m-0"><?= $data['print_by'] ?></p>
        </div>
        <div>
            <p class="m-0"><strong>Print Date</strong></p>
            <p class="m-0"><?= $data['print_date'] ?></p>
        </div>
    </header>
    <table class="table table-bordered">
        <tr>
            <th>Order No</th>
            <th>Transaction Date</th>
            <th>Payment Date</th>
            <th>Total Amount</th>
            <th>Service Charge</th>
            <th>Tax Amount</th>
            <th>Grand Total</th>
        </tr>
        <?php foreach ($data['transaction_report'] as $key => $report) : ?>
            <tr>
                <td><?= $report['ORDER_NO'] ?></td>
                <td><?= $report['TRANSACTION_DATE'] ?></td>
                <td><?= $report['PAYMENT_DATE'] ?></td>
                <td><?= $report['TOTAL'] ?></td>
                <td><?= $report['SERVICE_AMOUNT'] ?></td>
                <td><?= $report['TAX_AMOUNT'] ?></td>
                <td><?= $report['GRAND_TOTAL'] ?></td>
            </tr>
        <?php endforeach; ?>

        <tr>
            <th colspan="3">Total</th>
            <td><?= $data['data_total']['BASE_AMOUNT_TOTAL'] ?></td>
            <td><?= $data['data_total']['SERVICE_AMOUNT_TOTAL'] ?></td>
            <td><?= $data['data_total']['TAX_AMOUNT_TOTAL'] ?></td>
            <td><?= $data['data_total']['GRAND_AMOUNT_TOTAL'] ?></td>
        </tr>
    </table>
</body>

</html>