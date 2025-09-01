<?php include 'C:\xampp\htdocs\sistema_tickets\app\core/cache/bc43069e62f27db794193766586b71fb.php'; ?>
<?php include 'C:\xampp\htdocs\sistema_tickets\app\core/cache/f9f744bb1c61fdeaa90f01b98fdffdbc.php'; ?>
<link href='<?php echo htmlspecialchars($_ENV["BASE_URL"], ENT_QUOTES, 'UTF-8'); ?>css/sweetalert2.min.css' rel="stylesheet">
<div class="content-wrapper">
	<section class="content">
		<div class="card mt-4">
			<div class="card-body">
				<div class="row mb-3">
				</div>
				<?php echo $render; ?>
			</div>
		</div>
	</section>
</div>
<div id="artify-ajax-loader">
    <img width="300" src='<?php echo htmlspecialchars($_ENV["BASE_URL"], ENT_QUOTES, 'UTF-8'); ?>app/libs/artify/images/ajax-loader.gif' class="artify-img-ajax-loader"/>
</div>
<?php include 'C:\xampp\htdocs\sistema_tickets\app\core/cache/0b27e2778a539e2c7b14342c3b0c5b16.php'; ?>
<script src='<?php echo htmlspecialchars($_ENV["BASE_URL"], ENT_QUOTES, 'UTF-8'); ?>js/sweetalert2.all.min.js'></script>
<script>
$(document).on("artify_after_submission", function(event, obj, data) {
	let json = JSON.parse(data);

	if (json.message) {
		Swal.fire({
			icon: "success",
			text: json["message"],
			confirmButtonText: "Aceptar",
			allowOutsideClick: false
		}).then((result) => {
			if (result.isConfirmed) {
				$(".artify-back").click();
			}
		});
	}
});
</script>