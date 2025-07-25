@include('layouts/header')
@include('layouts/sidebar')
<link href='{{ $_ENV["BASE_URL"] }}css/sweetalert2.min.css' rel="stylesheet">
<div class="content-wrapper">
    <section class="content">
        <div class="card mt-4">
            <div class="card-body">

                <div class="row">
                    <div class="col-md-12">
                        {!! $render !!}
                    </div>
                </div>

            </div>
        </div>
    </section>
</div>
<div id="artify-ajax-loader">
    <img width="300" src='{{ $_ENV["BASE_URL"] }}app/libs/artify/images/ajax-loader.gif' class="artify-img-ajax-loader"/>
</div>
@include('layouts/footer')
<script src='{{ $_ENV["BASE_URL"] }}js/sweetalert2.all.min.js'></script>
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
                url: '{{ $_ENV["BASE_URL"] }}cargar_imagenes_configuracion',
                dataType: "json",
                success: function(data){
                    console.log(data);
                    $(".logo_login").attr("src", '{{ $_ENV["URL_ArtifyCrud"] }}' + 'artify/uploads/' + data[0].logo_login);
                    $(".logo_panel").attr("src", '{{ $_ENV["URL_ArtifyCrud"] }}' + 'artify/uploads/' + data[0].logo_panel);
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