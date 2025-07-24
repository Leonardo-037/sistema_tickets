<?php include 'C:\xampp\htdocs\ArtifyFramework\app\core/cache/9a6ead897497ff6b1e249ce124bf34c6.php'; ?>
<?php include 'C:\xampp\htdocs\ArtifyFramework\app\core/cache/f4626fe1ca5989aa2b534500fc842128.php'; ?>
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
<?php include 'C:\xampp\htdocs\ArtifyFramework\app\core/cache/387186dc417e1ddf0aaa57120f53153a.php'; ?>
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