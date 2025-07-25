<?php include 'C:\xampp7.4\htdocs\sistema_tickets\app\core/cache/9d19023728c0e1b6dd73224481099346.php'; ?>
<?php include 'C:\xampp7.4\htdocs\sistema_tickets\app\core/cache/9c3bf8063899bd9aac9f8b4d0f44e6d8.php'; ?>
<link href='<?php echo htmlspecialchars($_ENV["BASE_URL"], ENT_QUOTES, 'UTF-8'); ?>css/sweetalert2.min.css' rel="stylesheet">
<div class="content-wrapper">
    <section class="content">
        <div class="card mt-4">
            <div class="card-body">

                <div class="row">
                    <div class="col-md-12">
                        <?php echo $render; ?>
                    </div>
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

    $(document).ready(function () {
        // Detectar cambio en el select de nombreTecnico
        $('.input-bulk-crud-update[name="nombreTecnico[]"]').on('change', function () {
            var $row = $(this).closest('tr'); // buscar la fila actual
            var selectedValue = $(this).val();

            if (selectedValue !== "") {
                // buscar el select de estado dentro de la misma fila
                var $estadoSelect = $row.find('.input-bulk-crud-update[name="estado[]"]');

                // cambiar su valor a "Asignado"
                $estadoSelect.val('Asignado');
            }
        });
    });
</script>