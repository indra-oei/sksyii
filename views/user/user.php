<div>
	<div class="pageHeader">
		<h1>User</h1>
		<button class="btn btn-dark" onclick="openModal()"><span class="me-1"><i class="fa fa-plus"></i></span> User</button>
	</div>
	<table class="table table-bordered">
		<thead>
			<tr>
				<th>Name</th>
				<th>Email</th>
				<th>Action</th>
			</tr>
		</thead>
		<tbody>
			<?php if (COUNT($users) > 0): ?>
				<?php foreach ($users['data'] as $user): ?>
					<tr>
						<td><?= $user['NAME'] ?></td>
						<td><?= $user['EMAIL'] ?></td>
						<td>
							<div class="tableActionWrap">
								<button class="tableActionBtn btn text-info" onclick="openModal('<?= $user['EMAIL'] ?>')">
									<span>
										<i class="fa fa-edit"></i>
									</span>
								</button>
								<button class="tableActionBtn btn text-danger" onclick="deleteData('<?= $user['EMAIL'] ?>')">
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
				<h5 class="modal-title">User Form</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<form id="userForm">
			<div class="modal-body">
				<div class="mb-2">
					<label class="mb-1">Name</label>
					<input type="text" id="inputName" class="form-control" placeholder="Enter user's name..." required>
				</div>
				<div class="mb-2">
					<label class="mb-1">Email</label>
					<input type="email" id="inputEmail" class="form-control" placeholder="Enter user's email..." required>
				</div>
				<div class="mb-2">
					<label class="mb-1">Password</label>
					<input type="password" id="inputPassword" class="form-control" placeholder="Enter user's password...">
				</div>
				<div class="mb-2">
					<label class="mb-1">Confirm Password</label>
					<input type="password" id="inputReconfirm" class="form-control" placeholder="Re enter user's password...">
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
	let editMode = false;

	function openModal(email)
	{
		if (email) 
		{
			getEditedUser(email);
		}

		$('#modalForm').modal('show');
	}

	$(document).ready(function()
	{
		$('#userForm').on('submit', function(e)
		{
			e.preventDefault();
			if (editMode === false)
			{
				insert();
			}
			else
			{
				update();
			}
		});
		$('#modalForm').on('hidden.bs.modal', function (e) {
			$('#userForm')[0].reset();
		});
	})

    function insert()
	{
		var data = {
			name: $('#inputName').val(),
			email: $('#inputEmail').val(),
			password: $('#inputPassword').val(),
			reconfirm: $('#inputReconfirm').val()
		};

		$.ajax({
			type		: 'POST',
			data		: data,
			dataType	: 'json',
			url			: '<?= Yii::$app->getUrlManager()->createUrl('user/insert') ?>',
			success		: function(result)
			{
				showAlert(result, 'User Created');
			}
		})
	}
	
    function update()
	{
		var data = {
			name: $('#inputName').val(),
			email: $('#inputEmail').val(),
			password: $('#inputPassword').val(),
			reconfirm: $('#inputReconfirm').val()
		};

		$.ajax({
			type		: 'POST',
			data		: data,
			dataType	: 'json',
			url			: '<?= Yii::$app->getUrlManager()->createUrl('user/update') ?>',
			success		: function(result)
			{
				showAlert(result, 'User Updated');
			}
		})
	}

	function deleteData(email)
	{	
		Swal.fire({
			title: 'Confirmation',
			text : `Are you sure you want to remove user ${email}`,
			icon : 'warning',
			confirmButtonText: 'Yes',
			showCancelButton: true,
		}).then((result) => {
			if (result.isConfirmed)
			{
				destroy(email);	
			}
		})
	}

	function destroy(email)
	{
		$.ajax({
			type		: 'POST',
			data		: {email:email},
			dataType	: 'json',
			url			: '<?= Yii::$app->getUrlManager()->createUrl('user/delete') ?>',
			success		: function(result)
			{
				alert(result, 'User Deleted');
			}
		})
	}

	function getEditedUser(email)
	{
		$.ajax({
			type		: 'POST',
			data		: {email: email},
			dataType	: 'json',
			url			: '<?= Yii::$app->getUrlManager()->createUrl('user/get-user-by-email') ?>',
			success		: function(result)
			{
				$('#inputName').val(result.data['NAME']);
				$('#inputEmail').val(result.data['EMAIL']);
				editMode = true;
			}
		})
	}
</script>