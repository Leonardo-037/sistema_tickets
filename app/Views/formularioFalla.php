<link href='{{ $_ENV["BASE_URL"] }}css/sweetalert2.min.css' rel="stylesheet">
<style>
  body {
    background: #f3f3f3!important;
  }
</style>
<div class="container">
  <div class="row justify-content-center">
    <div class="col-md-6">

        <div class="card mt-5">
          <div class="card-header bg-info text-white text-center">Genera tu Ticket</div>
          <div class="card-body">{!!$render!!} {!! $chosen !!}</div>
        </div>

    </div>
  </div>
</div>

<div id="artify-ajax-loader">
    <img width="300" src='{{ $_ENV["BASE_URL"] }}app/libs/artify/images/ajax-loader.gif' class="artify-img-ajax-loader"/>
</div>
<script src='{{ $_ENV["BASE_URL"] }}js/sweetalert2.all.min.js'></script>
<script>
  $(document).ready(function(){
    $(".fallas").empty();
    $(".fallas").html("<option>Seleccionar</option>");
  });
</script>
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