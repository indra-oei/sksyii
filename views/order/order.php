<div>
    <div class="pageHeader">
        <h1>Order</h1>
        <button class="btn btn-dark" onclick="openModal()"><span class="me-1"><i class="fa fa-plus"></i></span> Order</button>
    </div>
    <table class="table table-bordered">
		<thead>
			<tr>
				<th>Order No</th>
				<th>Customer Name</th>
				<th>Customer Sex</th>
				<th>Table No</th>
				<th>Total Customer</th>
				<th>Transaction Date</th>
				<th>Order Status</th>
				<th>Action</th>
			</tr>
		</thead>
		<tbody>
            <?php if (COUNT($orders['data']) > 0): ?>
                <?php foreach ($orders['data'] as $order): ?>
                    <tr>
                        <td><?= $order['ORDER_NO'] ?></td>
                        <td><?= $order['CUSTOMER_NAME'] ?></td>
                        <td><?= $order['CUSTOMER_SEX'] ?></td>
                        <td><?= $order['TABLE_NO'] ?></td>
                        <td><?= $order['TOTAL_CUSTOMER'] ?></td>
                        <td><?= date('d M Y', strtotime($order['TRANSACTION_DATE'])) ?></td>
                        <td>
                            <?php if ($order['ORDER_STATUS'] === "New"): ?>
                                <span class="badge text-bg-primary"><?= $order['ORDER_STATUS'] ?></span>
                            <?php elseif ($order['ORDER_STATUS'] === "Cancel"): ?>
                                <span class="badge text-bg-danger"><?= $order['ORDER_STATUS'] ?></span>
                            <?php else: ?>
                                <span class="badge text-bg-success"><?= $order['ORDER_STATUS'] ?></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="<?= Yii::$app->getUrlManager()->createUrl('order-detail?ORDER_NUMBER='. $order['ORDER_NO'] .'') ?>">
                                    <button class="btn btn-info btn-sm text-light">
                                        See Detail
                                    </button>
                                </a>
                                <?php if ($order['ORDER_STATUS'] === "New"): ?>
                                    <button class="btn btn-danger btn-sm" onclick="confirmOrder('<?= $order['ID'] ?>', 'Cancel', '<?= $order['ORDER_NO'] ?>')">
                                        Cancel
                                    </button>
                                    <button class="btn btn-success btn-sm" onclick="confirmOrder('<?= $order['ID'] ?>', 'Pay', '<?= $order['ORDER_NO'] ?>')">
                                        Pay
                                    </button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8" class="text-center">No data found</td>
                </tr>
            <?php endif; ?>
		</tbody>
	</table>
</div>

<div id="modalForm" class="modal fade" tabindex="-1">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Add New Order</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<form id="orderForm">
                <div class="modal-body">
                    <div class="mb-2">
                        <label class="mb-1">Customer Name</label>
                        <input type="text" id="inputCustomerName" class="form-control" placeholder="Enter customer's name" required>
                    </div>
                    <div class="mb-2">
                        <div>
                            <label class="mb-1">Customer Sex</label>
                        </div>
                        <div class="d-flex flex-row align-items-center gap-1">
                            <label for="maleSex">Male</label>
                            <input id="maleSex" type="radio" name="sex" class="me-1" value="M">
                            <label for="femaleSex">Female</label>
                            <input id="femaleSex" type="radio" name="sex" value="F">
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="mb-1">Table No</label>
                        <div class="input-group">
                            <input type="number" id="inputTableNo" class="form-control" placeholder="Generate table number" required disabled>
                            <button type="button" class="btn btn-dark" onclick="getTableNum()">
                                <span><i class="fa-solid fa-arrows-rotate"></i></span>
                            </button>
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="mb-1">Total Customer</label>
                        <input type="number" id="inputTotalCustomer" class="form-control" placeholder="Enter total of customer" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <!-- <button type="button" class="btn btn-outline-danger">Reset</button> -->
                    <button type="submit" class="btn btn-dark">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openModal()
{
    $('#modalForm').modal('show');
    getTableNum()
}

function getTableNum()
{
    $.ajax({
        type		: 'POST',
        dataType	: 'text',
        url			: '<?= Yii::$app->getUrlManager()->createUrl('order/get-table-num') ?>',
        success		: function(result)
        {
            if (result)
            {
                $('#inputTableNo').val(result);
            }
        }
    })
}

$(document).ready(function()
{
    $('#orderForm').on('submit', function(e)
        {
            e.preventDefault();
            insert();
        });
        $('#modalForm').on('hidden.bs.modal', function (e) {
            $('#userForm')[0].reset();
        });
    });

    function insert()
    {
        var data = {
            customer_name: $('#inputCustomerName').val(),
            customer_sex: $('input[type="radio"][name="sex"]:checked').val(),
            table_no: $('#inputTableNo').val(),
            total_customer: $('#inputTotalCustomer').val()
        };

        $.ajax({
            type		: 'POST',
            data		: data,
            dataType	: 'json',
            url			: '<?= Yii::$app->getUrlManager()->createUrl('order/insert') ?>',
            success		: function(result)
            {
                showAlert(result, 'Order Created', '<?= Yii::$app->getUrlManager()->createUrl('order-detail') ?>?ORDER_NUMBER=' + result.orderNo + '');
            }
        });
	}

    function confirmOrder(id, order_status, order_no)
    {
        Swal.fire({
			title: 'Confirmation',
			text : `Are you sure you want to ${order_status === 'Cancel' ? 'cancel' : 'pay'} order ${order_no}`,
			icon : 'warning',
			confirmButtonText: 'Yes',
			showCancelButton: true,
		}).then((result) => {
			if (result.isConfirmed)
			{
				update(id, order_status);	
			}
		})
    }

    function update(id, order_status)
    {
        $.ajax({
            type		: 'POST',
            data		: {id:id, order_status:order_status},
            dataType	: 'json',
            url			: '<?= Yii::$app->getUrlManager()->createUrl('order/update') ?>',
            success		: function(result)
            {
                showAlert(result, 'Order Updated');
                updateOrderDetail(id);
                updateTransaction(id);
            }
		});
    }

    function updateOrderDetail(order_id)
    {
        $.ajax({
            type		: 'POST',
            data		: {order_id:order_id},
            dataType	: 'json',
            url			: '<?= Yii::$app->getUrlManager()->createUrl('order-detail/update') ?>',
            success		: function(result)
            {
                console.log(result);
            }
		});
    }

    function updateTransaction(order_id)
    {
        $.ajax({
            type		: 'POST',
            data		: {order_id:order_id},
            dataType	: 'json',
            url			: '<?= Yii::$app->getUrlManager()->createUrl('transaction/update') ?>',
            success		: function(result)
            {
                console.log(result);
            }
		});
    }
</script>