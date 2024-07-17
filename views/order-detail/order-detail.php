<div>
    <div class="pageHeader">
        <div class="pageHeaderTitle">
            <a href="<?= Yii::$app->getUrlManager()->createUrl('order') ?>">
                <button class="btn"><span><i class="fa fa-arrow-left"></i></span></button>
            </a>
            <h1>Order Detail</h1>
        </div>
    </div>
    <div class="row mb-5">
        <div class="col-2">
            <strong>Order No.</strong>
            <p class="m-0"><?= $order['ORDER_NO'] ?></p>
        </div>
        <div class="col-2">
            <strong>Customer Name</strong>
            <p class="m-0"><?= $order['CUSTOMER_NAME'] ?></p>
        </div>
        <div class="col-2">
            <strong>Table No.</strong>
            <p class="m-0"><?= $order['TABLE_NO'] ?></p>
        </div>
        <div class="col-2">
            <strong>Number of Customer</strong>
            <p class="m-0"><?= $order['TOTAL_CUSTOMER'] ?></p>
        </div>
        <div class="col-2">
            <strong>Status</strong>
            <p class="m-0">
                <?php if ($order['ORDER_STATUS'] === "New"): ?>
                    <span class="badge text-bg-primary"><?= $order['ORDER_STATUS'] ?></span>
                <?php elseif ($order['ORDER_STATUS'] === "Cancel"): ?>
                    <span class="badge text-bg-danger"><?= $order['ORDER_STATUS'] ?></span>
                <?php else: ?>
                    <span class="badge text-bg-success"><?= $order['ORDER_STATUS'] ?></span>
                <?php endif; ?>
            </p>
        </div>
    </div>
    <table class="table table-bordered">
		<thead>
			<tr>
				<th>Item</th>
				<th>Quantity</th>
				<th>Price</th>
				<th>Notes</th>
				<th>Subtotal</th>
				<?php if ($order['ORDER_STATUS'] === "New"): ?>
                    <div class="text-end">
                        <th></th>
                    </div>
                <?php endif; ?>
			</tr>
		</thead>
		<tbody id="orderDetailList">
            
		</tbody>
        <tfoot>
            <tr>
                <td colspan="4" class="text-end">Service charge</td>
                <td id="serviceCharge" colspan="2">Rp 0</td>
            </tr>
            <tr>
                <td colspan="4" class="text-end">Tax</td>
                <td id="tax" colspan="2">Rp 0</td>
            </tr>
            <tr>
                <td colspan="4" class="text-end">Grand Total</td>
                <td id="grandTotal" colspan="2">Rp 0</td>
            </tr>
        </tfoot>
	</table>
    <?php if ($order['ORDER_STATUS'] === "New"): ?>
        <div class="text-end">
            <button type="button" class="btn btn-dark" onclick="submitForm()">Checkout</button>
        </div>
    <?php endif; ?>
</div>

<script>
self._order_no = '<?= $order['ORDER_NO'] ?>';
self._order_id = '<?= $order['ID'] ?>';
self._order_status = '<?= $order['ORDER_STATUS'] ?>';
let orderDetails = [
    {item_name: '', item_quantity: 1, item_price: 0, notes: '', subtotal: 0}
];
let total;
let serviceCharge;
let tax;
let grandTotal;
let items = "";

function addOrderDetail()
{
    orderDetails = [...orderDetails, {
        item_name: '', 
        item_quantity: 1, 
        item_price: 0, 
        notes: '', 
        subtotal: 0
    }];
    renderOrderDetail();
}

function removeOrderDetail(index)
{
    let oldData = [...orderDetails];
    oldData.splice(index, 1);
    orderDetails = oldData;
    renderOrderDetail();
    calculateOrderDetail();
}

function renderOrderDetail()
{
    let view = '';
    let button = '';
    let itemPicker = '';
    let qtyPicker = '';
    let notesInput = '';
    for (let i = 0; i < orderDetails.length; i++)
    {
        if (_order_status === "New")
        {
            if (i > 0)
            {
                button = `
                    <td>
                        <button class="btn btn-danger btn-sm" onclick="removeOrderDetail()"><span><i class="fa fa-minus"></i></span></button>
                    </td>
                `;
            }
            else
            {
                button = `
                    <td>
                        <button class="btn btn-success btn-sm" onclick="addOrderDetail()"><span><i class="fa fa-plus"></i></span></button>
                    </td>
                `;
            }
        }

        if (_order_status === "New")
        {
            itemPicker = `
                <select id="inputItem_${i}" class="form-select" onchange="itemNameChangeHandler(event, ${i})">
                    <option value="">Select item</option>
                    ${items}
                </select>
            `;
        }
        else 
        {
            itemPicker = `
                ${orderDetails[i]['item_name']}
            `;
        }

        if (_order_status === "New")
        {
            qtyPicker = `
                <input type="number" id="inputQuantity_${i}" class="form-control" placeholder="Enter item quantity" onchange="itemQtyChangeHandler(event, ${i})" value="${orderDetails[i].item_quantity}">
            `;
        }
        else
        {
            qtyPicker = `
                ${orderDetails[i]['item_quantity']}
            `;
        }

        if (_order_status === "New")
        {
            notesInput = `
                <input type="number" id="inputQuantity_${i}" class="form-control" placeholder="Enter item quantity" onchange="itemQtyChangeHandler(event, ${i})" value="${orderDetails[i].item_quantity}">
            `;
        }
        else
        {
            notesInput = `
                ${orderDetails[i]['notes'] || '-'}
            `;
        }


        
        view += `
            <tr>
                <td>
                    ${itemPicker}
                </td>
                <td>
                    ${qtyPicker}
                </td>
                <td id="itemPrice_${i}">Rp ${formatCurrency(orderDetails[i].item_price)}</td>
                <td>
                    ${notesInput}
                </td>
                <td>Rp ${formatCurrency(orderDetails[i].subtotal)}</td>
                ${button}
            </tr>
        `;
    }
    $('#orderDetailList').html(view);
    renderSelectedItem();
}

function getOrderDetail()
{
    $.ajax({
        type		: 'POST',
        data        : {order_no: _order_no},
        dataType	: 'json',
        url			: '<?= Yii::$app->getUrlManager()->createUrl('order-detail/get-all') ?>',
        success		: function(result)
        {
            if (result.errNum === 0) {
                orderDetails = [];
                for (let i = 0; i < result.data.length; i++)
                {
                    const orderDetail = result.data[i];
                    orderDetails = [...orderDetails, 
                    {
                        item_name: orderDetail['ITEM_NAME'], 
                        item_quantity: orderDetail['ITEM_QUANTITY'], 
                        item_price: orderDetail['ITEM_PRICE'], 
                        notes: orderDetail['NOTES'] || '', 
                        subtotal: orderDetail['SUBTOTAL']
                    }];
                }
                calculateOrderDetail();
            }
        }
    });
}

function renderSelectedItem()
{
    for (let i = 0; i < orderDetails.length; i++)
    {
        $(`#inputItem_${i}`).children(`option[value="${orderDetails[i].item_name}"]`).attr('selected', true)
    }
}

function getItem()
{
    $.ajax({
        type		: 'POST',
        dataType	: 'json',
        url			: '<?= Yii::$app->getUrlManager()->createUrl('item/get-all') ?>',
        success		: function(result)
        {
            if (result.errNum === 0)
            {
                const itemList = result.data;

                for (let i = 0; i < itemList.length; i++)
                {
                    items += `
                        <option value="${itemList[i]['NAME']}" >${itemList[i]['NAME']}</option>
                    `
                }
            }
            renderOrderDetail();
        }
    });
}

function itemNameChangeHandler(e, index)
{
    $.ajax({
        type		: 'POST',
        data		: {name: e.target.value},
        dataType	: 'json',
        url			: '<?= Yii::$app->getUrlManager()->createUrl('item/get-item-by-name') ?>',
        success		: function(result)
        {
            if (result.errNum == 0)
            {
                $(`#itemPrice_${index}`).html(`Rp ${result.data['PRICE']}`);
                $(`#itemName_${index}`).val(result.data['ITEM_NAME']);
                orderDetails[index]['item_name'] = e.target.value;
                orderDetails[index]['item_price'] = result.data['PRICE'];
                calculateOrderDetail();
            }
        }
    });
}

function itemQtyChangeHandler(e, index)
{
    orderDetails[index]['item_quantity'] = e.target.value;
    calculateOrderDetail();
}
function itemNotesChangeHandler(e, index)
{
    orderDetails[index]['notes'] = e.target.value;
}

function itemNoteChangeHandler(e, index)
{
    orderDetails[index]['notes'] = e.target.value;
}

function calculateOrderDetail()
{
    for (let i = 0; i < orderDetails.length; i++)
    {
        orderDetails[i]['subtotal'] = orderDetails[i]['item_price'] * orderDetails[i]['item_quantity'];
    }
    renderOrderDetail();
    const sumOfSubtotal = orderDetails.reduce((acc, val) => {
        return acc + val.subtotal;
    }, 0);
    total = sumOfSubtotal;
    serviceCharge = sumOfSubtotal * 0.1;
    tax = (sumOfSubtotal + serviceCharge) * 0.11;
    grandTotal = sumOfSubtotal + serviceCharge + tax;

    $('#serviceCharge').html(`Rp ${formatCurrency(serviceCharge)}`);
    $('#tax').html(`Rp ${formatCurrency(tax)}`);
    $('#grandTotal').html(`Rp ${formatCurrency(grandTotal)}`);
}

function submitForm()
{
    $.ajax({
        type		: 'POST',
        data		: {order_no: _order_no},
        dataType	: 'json',
        url			: '<?= Yii::$app->getUrlManager()->createUrl('order-detail/destroy') ?>',
        success		: function(result)
        {
            for (let i = 0; i < orderDetails.length; i++)
            {
                orderDetails[i]['order_no'] = _order_no;
                insert(orderDetails[i]);
            }
            insertTransaction();
            Swal.fire({
                title: 'Success',
                text : 'Order detail created',
                icon : 'success',
                showConfirmButton: false,
                timer: 1500
            });
        }
    })
}

function insert(data)
{
    $.ajax({
        type		: 'POST',
        data		: data,
        dataType	: 'json',
        url			: '<?= Yii::$app->getUrlManager()->createUrl('order-detail/insert') ?>',
        success		: function(result)
        {
            if (result.errNum === 0) 
            {
                
            } else {
                console.log(result.errStr);
            }
        }
    });
}

function insertTransaction()
{
    var data = {
        order_id: _order_id,
        total: total,
        service_amount: serviceCharge,
        tax_amount: tax,
        grand_total: grandTotal
    }
    $.ajax({
        type		: 'POST',
        data		: {order_id: _order_id},
        dataType	: 'json',
        url			: '<?= Yii::$app->getUrlManager()->createUrl('transaction/destroy') ?>',
        success		: function(result)
        {
            $.ajax({
                type		: 'POST',
                data		: data,
                dataType	: 'json',
                url			: '<?= Yii::$app->getUrlManager()->createUrl('transaction/insert') ?>',
                success		: function(result)
                {
                    console.log(result);
                }
            })
            
        }
    })
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
        if (result.errNum === 1) {
            Swal.fire({
                title: 'Oops!',
                text : '' + result.errStr,
                icon : 'error',
                showConfirmButton: false,
                timer: 1500
            });
        } else {
            Swal.fire({
                title: 'Success',
                text : 'Order updated',
                icon : 'success',
                showConfirmButton: false,
                timer: 1500
            }).then(() => {
                window.location.reload();
            })
        }
    }
    });
}

$(document).ready(function()
{
    getItem();

    getOrderDetail();

})
</script>