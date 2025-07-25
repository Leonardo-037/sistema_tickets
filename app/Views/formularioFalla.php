<div class="card">
  <div class="card-header bg-dark text-white text-center">Genera tu Ticket</div>
  <div class="card-body bg-light">{!!$render!!}</div>
</div>

<div id="artify-ajax-loader">
    <img width="300" src='{{ $_ENV["BASE_URL"] }}app/libs/artify/images/ajax-loader.gif' class="artify-img-ajax-loader"/>
</div>
<script>
  $(document).ready(function(){
    $(".fallas").empty();
    $(".fallas").html("<option>Seleccionar</option>");
  });
</script>
