<link href='<?php echo htmlspecialchars($_ENV["BASE_URL"], ENT_QUOTES, 'UTF-8'); ?>css/sweetalert2.min.css' rel="stylesheet">
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
          <div class="card-body"><?php echo $render; ?></div>
        </div>

    </div>
  </div>
</div>

<div id="artify-ajax-loader">
    <img width="300" src='<?php echo htmlspecialchars($_ENV["BASE_URL"], ENT_QUOTES, 'UTF-8'); ?>app/libs/artify/images/ajax-loader.gif' class="artify-img-ajax-loader"/>
</div>