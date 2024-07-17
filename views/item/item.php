<div>
	<div class="pageHeader">
		<h1>Item</h1>
		<button class="btn btn-dark" onclick="openModal()"><span class="me-1"><i class="fa fa-plus"></i></span> Item</button>
	</div>
	<table class="table table-bordered">
		<thead>
			<tr>
				<th>Category</th>
				<th>Sub Category</th>
				<th>Name</th>
				<th>Description</th>
				<th>Price</th>
				<th>Valid Until</th>
				<th>Action</th>
			</tr>
		</thead>
		<tbody>
			<?php if (COUNT($items['data']) > 0): ?>
				<?php foreach ($items['data'] as $item): ?>
					<tr>
						<td><?= $item['CATEGORY'] ?></td>
						<td><?= $item['SUB_CATEGORY'] ?></td>
                        <td><?= $item['NAME'] ?></td>
                        <td><?= $item['DESCRIPTION'] ?></td>
						<td>Rp <?= number_format($item['PRICE'], 0, ',', '.') ?></td>
						<td><?= date('d/m/Y', strtotime($item['VALID_UNTIL'])) ?></td>
						<td>
							<div class="tableActionWrap">
								<button class="tableActionBtn btn text-info" onclick="openModal('<?= $item['ID'] ?>')">
									<span>
										<i class="fa fa-edit"></i>
									</span>
								</button>
								<button class="tableActionBtn btn text-danger" onclick="deleteData('<?= $item['ID'] ?>', '<?= $item['NAME'] ?>')">
									<span>
										<i class="fa fa-trash"></i>
									</span>
								</button>
							</div>
						</td>
					</tr>
				<?php endforeach; ?>
			<?php else: ?>
				<tr>
					<td colspan="3" class="text-center">No data found</td>
				</tr>
			<?php endif; ?>
		</tbody>
	</table>
</div>

<div id="modalForm" class="modal fade" tabindex="-1">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Item Form</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<form id="form">
			<div class="modal-body">
					<div class="mb-2">
						<label class="mb-1">Name</label>
						<input type="text" id="inputName" class="form-control" placeholder="Enter name" required>
					</div>
					<div class="mb-2">
						<label class="mb-1">Sub Category</label>
                        <select id="inputSubCategoryId" class="form-select">
                            <option value="">Select Sub Category</option>
							<?php if (COUNT($subCategories['data']) > 1): ?>
								<?php foreach ($subCategories['data'] as $subCategory): ?>
									<option value="<?= $subCategory['ID'] ?>"><?= $subCategory['NAME'] ?></option>
								<?php endforeach; ?>
							<?php endif; ?>
                        </select>
					</div>
					<div class="mb-2">
						<label class="mb-1">Description</label>
						<textarea type="text" id="inputDesc" rows="3" class="form-control" placeholder="Enter description" required></textarea>
					</div>
					<div class="mb-2">
						<label class="mb-1">Price</label>
						<input type="number" id="inputPrice" class="form-control" placeholder="Enter price" required>
					</div>
					<div class="mb-2">
						<label class="mb-1">Valid Until</label>
						<input type="date" id="inputValid" class="form-control" required>
					</div>
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-dark">Save</button>
				</div>
			</form>
		</div>
	</div>
</div>

<script>
	let editedId = null;

	function openModal(id)
	{
		if (id) 
		{
			getEditedData(id);
		}

		$('#modalForm').modal('show');
	}

	$(document).ready(function()
	{
		$('#form').on('submit', function(e)
		{
			e.preventDefault();
			if (!editedId)
			{
				insert();
			}
			else
			{
				update();
			}
		});
		$('#modalForm').on('hidden.bs.modal', function (e) {
			$('#form')[0].reset();
            editedId = null;
		});
	})

    function insert()
	{
		var data = {
			id: editedId,
			name: $('#inputName').val(),
			description: $('#inputDesc').val(),
			price: $('#inputPrice').val(),
			valid_until: $('#inputValid').val(),
			subcategory_id: $('#inputSubCategoryId').val()
		};

		$.ajax({
			type		: 'POST',
			data		: data,
			dataType	: 'json',
			url			: '<?= Yii::$app->getUrlManager()->createUrl('item/insert') ?>',
			success		: function(result)
			{
				showAlert(result, "Item Created");
			}
		})
	}
	
    function update()
	{
		var data = {
            id: editedId,
			name: $('#inputName').val(),
			description: $('#inputDesc').val(),
			price: $('#inputPrice').val(),
			valid_until: $('#inputValid').val(),
			subcategory_id: $('#inputSubCategoryId').val()
		};

		$.ajax({
			type		: 'POST',
			data		: data,
			dataType	: 'json',
			url			: '<?= Yii::$app->getUrlManager()->createUrl('item/update') ?>',
			success		: function(result)
			{
				showAlert(result, "Item Updated");
			}
		})
	}

	function deleteData(id, name)
	{	
		Swal.fire({
			title: 'Confirmation',
			text : `Are you sure you want to remove item ${name}`,
			icon : 'warning',
			confirmButtonText: 'Yes',
			showCancelButton: true,
		}).then((result) => {
			if (result.isConfirmed)
			{
				destroy(id);	
			}
		})
	}

	function destroy(id)
	{
		$.ajax({
			type		: 'POST',
			data		: {id:id},
			dataType	: 'json',
			url			: '<?= Yii::$app->getUrlManager()->createUrl('item/delete') ?>',
			success		: function(result)
			{
				showAlert(result, "Item Deleted");
			}
		})
	}

	function getEditedData(id)
	{
		$.ajax({
			type		: 'POST',
			data		: {id: id},
			dataType	: 'json',
			url			: '<?= Yii::$app->getUrlManager()->createUrl('item/get-item-by-id') ?>',
			success		: function(result)
			{
				$('#inputName').val(result.data['NAME']);
				$('#inputDesc').val(result.data['DESCRIPTION']);
				$('#inputPrice').val(result.data['PRICE']);
				$('#inputValid').val(moment(result.data['VALID_UNTIL']).format('YYYY-MM-DD'));
				$('#inputSubCategoryId').val(result.data['SUBCATEGORY_ID']);
				editedId = id;
			}
		})
	}
</script>