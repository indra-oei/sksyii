
<div>
	<div class="pageHeader">
		<h1>Subcategory</h1>
		<button class="btn btn-dark" onclick="openModal()"><span class="me-1"><i class="fa fa-plus"></i></span> Subcategory</button>
	</div>
	<table class="table table-bordered">
		<thead>
			<tr>
				<th>Name</th>
				<th>Category</th>
				<th>Action</th>
			</tr>
		</thead>
		<tbody>
			<?php if (COUNT($subCategories['data']) > 0): ?>
				<?php foreach ($subCategories['data'] as $subcategory): ?>
					<tr>
                        <td><?= $subcategory['NAME'] ?></td>
						<td><?= $subcategory['CATEGORY'] ?></td>
						<td>
							<div class="tableActionWrap">
								<button class="tableActionBtn btn text-info" onclick="openModal('<?= $subcategory['ID'] ?>')">
									<span>
										<i class="fa fa-edit"></i>
									</span>
								</button>
								<button class="tableActionBtn btn text-danger" onclick="deleteData('<?= $subcategory['ID'] ?>', '<?= $subcategory['NAME'] ?>')">
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
				<h5 class="modal-title">Sub Category Form</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<form id="form">
			<div class="modal-body">
					<div class="mb-2">
						<label class="mb-1">Name</label>
						<input type="text" id="inputName" class="form-control" placeholder="Enter name" required>
					</div>
					<div class="mb-2">
						<label class="mb-1">Category</label>
                        <select id="inputCategoryId" class="form-select">
                            <option value="">Select Category</option>
							<?php if (COUNT($categories['data']) > 1): ?>
								<?php foreach ($categories['data'] as $category): ?>
									<option value="<?= $category['ID'] ?>"><?= $category['NAME'] ?></option>
								<?php endforeach; ?>
							<?php endif; ?>
                        </select>
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
			name: $('#inputName').val(),
			category_id: $('#inputCategoryId').val(),
		};

		$.ajax({
			type		: 'POST',
			data		: data,
			dataType	: 'json',
			url			: '<?= Yii::$app->getUrlManager()->createUrl('sub-category/insert') ?>',
			success		: function(result)
			{
				showAlert(result, "Subcategory Created");
			}
		})
	}
	
    function update()
	{
		var data = {
            id: editedId,
			name: $('#inputName').val(),
			category_id: $('#inputCategoryId').val()
		};

		$.ajax({
			type		: 'POST',
			data		: data,
			dataType	: 'json',
			url			: '<?= Yii::$app->getUrlManager()->createUrl('sub-category/update') ?>',
			success		: function(result)
			{
				showAlert(result, "Subcategory Updated");
			}
		})
	}

	function deleteData(id, name)
	{	
		Swal.fire({
			title: 'Confirmation',
			text : `Are you sure you want to remove sub category ${name}`,
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
			url			: '<?= Yii::$app->getUrlManager()->createUrl('sub-category/delete') ?>',
			success		: function(result)
			{
				showAlert(result, "Subcategory Deleted");
			}
		})
	}

	function getEditedData(id)
	{
		$.ajax({
			type		: 'POST',
			data		: {id: id},
			dataType	: 'json',
			url			: '<?= Yii::$app->getUrlManager()->createUrl('sub-category/get-category-by-id') ?>',
			success		: function(result)
			{
				$('#inputName').val(result.data['NAME']);
				$('#inputCategoryId').val(result.data['CATEGORY_ID']);
				editedId = id;
			}
		})
	}
</script>