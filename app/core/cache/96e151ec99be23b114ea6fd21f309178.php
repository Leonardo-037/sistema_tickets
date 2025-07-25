<?php include 'C:\xampp7.4\htdocs\sistema_tickets\app\core/cache/9d19023728c0e1b6dd73224481099346.php'; ?>
<?php include 'C:\xampp7.4\htdocs\sistema_tickets\app\core/cache/9c3bf8063899bd9aac9f8b4d0f44e6d8.php'; ?>
<link href='<?php echo htmlspecialchars($_ENV["BASE_URL"], ENT_QUOTES, 'UTF-8'); ?>css/sweetalert2.min.css' rel="stylesheet">
<div class="content-wrapper">
	<section class="content">
		<div class="card mt-4">
			<div class="card-body">
				
        <div class="card">
          <div class="card-header bg-dark text-white text-center">Genera tu Ticket</div>
          <div class="card-body bg-light"><?php echo $render; ?></div>
        </div>

			</div>
		</div>
	</section>
</div>
<div id="artify-ajax-loader">
    <img width="300" src='<?php echo htmlspecialchars($_ENV["BASE_URL"], ENT_QUOTES, 'UTF-8'); ?>app/libs/artify/images/ajax-loader.gif' class="artify-img-ajax-loader"/>
</div>
<?php include 'C:\xampp7.4\htdocs\sistema_tickets\app\core/cache/8b931ff7b991445f0fe37e6211d9e549.php'; ?>
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