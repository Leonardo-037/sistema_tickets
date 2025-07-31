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
          <div class="card-header bg-info text-white text-center">Registrar Funcionarios</div>
          <div class="card-body">{!!$render!!}</div>
        </div>

    </div>
  </div>
</div>

<div id="artify-ajax-loader">
    <img width="300" src='{{ $_ENV["BASE_URL"] }}app/libs/artify/images/ajax-loader.gif' class="artify-img-ajax-loader"/>
</div>