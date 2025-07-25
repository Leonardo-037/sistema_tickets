<div class="card">
  <div class="card-header bg-dark text-white text-center">Genera tu Ticket</div>
  <div class="card-body bg-light"><?php echo $render; ?></div>
</div>

<div id="artify-ajax-loader">
    <img width="300" src='<?php echo htmlspecialchars($_ENV["BASE_URL"], ENT_QUOTES, 'UTF-8'); ?>app/libs/artify/images/ajax-loader.gif' class="artify-img-ajax-loader"/>
</div>
