<div id="auth">
	<div id="loginBox">
		<div class="loginBoxHeader">
			<h1>Log In</h1>
		</div>
		<form id="loginForm" class="mb-4">
			<div class="mb-2">
				<label class="form-label">Email</label>
				<input type="email" id="inputEmail" class="form-control" placeholder="Enter your email" required />
			</div>
			<div class="mb-4">
				<label class="form-label">Password</label>
				<input type="password" id="inputPassword" class="form-control" placeholder="Enter your password" required />
			</div>
			<div class="d-grid">
				<button type="submit" class="btn btn-primary">Log In</button>
			</div>
		</form>
	</div>
</div>

<script>
	$(document).ready(function() 
	{
		$('#loginForm').on('submit', function(e) 
		{
			e.preventDefault();

			login();
		});
	})

	function login()
	{
		var data = {
			email: $('#inputEmail').val(),
			password: $('#inputPassword').val()
		};

		$.ajax({
			type		: 'POST',
			data		: data,
			dataType	: 'json',
			url			: '<?= Yii::$app->getUrlManager()->createUrl('login/login') ?>',
			beforeSend	: function() 
			{

			},
			success		: function(result)
			{
				if (result.errNum > 0) {
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
						text : `Welcome back ${result.result['NAME']}`,
						icon : 'success',
						showConfirmButton: false,
						timer: 1500
					}).then(() => {
						window.location = '<?= Yii::$app->getUrlManager()->createUrl('user') ?>';
					})
				}
			}
		})
	}
</script>