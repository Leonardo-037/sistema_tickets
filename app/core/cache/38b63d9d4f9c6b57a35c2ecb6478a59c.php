<?php include 'C:\xampp7429\htdocs\ArtifyFramework\app\core/cache/70bca4beba6f729d29cb3a4d682ffd9f.php'; ?>
<?php include 'C:\xampp7429\htdocs\ArtifyFramework\app\core/cache/0a7131fc6e4012a62e6a53bff84f6a69.php'; ?>
<link href='<?php echo htmlspecialchars($_ENV["BASE_URL"], ENT_QUOTES, 'UTF-8'); ?>css/sweetalert2.min.css' rel="stylesheet">
<div class="content-wrapper">
    <section class="content">
        <div class="card mt-4">
            <div class="card-body">

                <div class="row">
                    <div class="col-md-12">
                        <?php echo $render; ?>
                        <?php echo $color; ?>
                    </div>
                </div>

            </div>
        </div>
    </section>
</div>
<div id="artify-ajax-loader">
    <img width="300" src='<?php echo htmlspecialchars($_ENV["BASE_URL"], ENT_QUOTES, 'UTF-8'); ?>app/libs/artify/images/ajax-loader.gif' class="artify-img-ajax-loader"/>
</div>
<?php include 'C:\xampp7429\htdocs\ArtifyFramework\app\core/cache/1e00a00070ff40b84e33c5f8cc9cb5e5.php'; ?>
<script src='<?php echo htmlspecialchars($_ENV["BASE_URL"], ENT_QUOTES, 'UTF-8'); ?>js/sweetalert2.all.min.js'></script>
<script>
    $(document).on("artify_after_ajax_action", function(event, obj, data){
        var dataAction = obj.getAttribute('data-action');
        var dataId = obj.getAttribute('data-id');

        if(dataAction == "add"){
        
        }

        if(dataAction == "edit"){
        
        }
    });
    $(document).on("artify_after_submission", function(event, obj, data) {
        let json = JSON.parse(data);

        if (json.message) {
            $(".alert-success").hide();
            $(".alert-danger").hide();
            $.ajax({
                type: "POST",
                url: '<?php echo htmlspecialchars($_ENV["BASE_URL"], ENT_QUOTES, 'UTF-8'); ?>cargar_imagenes_configuracion',
                dataType: "json",
                success: function(data){
                    console.log(data);
                    $(".logo_login").attr("src", '<?php echo htmlspecialchars($_ENV["URL_ArtifyCrud"], ENT_QUOTES, 'UTF-8'); ?>' + 'artify/uploads/' + data[0].logo_login);
                    $(".logo_panel").attr("src", '<?php echo htmlspecialchars($_ENV["URL_ArtifyCrud"], ENT_QUOTES, 'UTF-8'); ?>' + 'artify/uploads/' + data[0].logo_panel);
                }
            });

            Swal.fire({
                icon: "success",
                text: json["message"],
                confirmButtonText: "Aceptar",
                allowOutsideClick: false
            });
        }
    });
</script>