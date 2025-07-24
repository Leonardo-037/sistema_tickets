@include('layouts/header')
@include('layouts/sidebar')
<div class="content-wrapper">
    <section class="content">
        <div class="card">
            <div class="card-body">

                <div class="row">
                    <div class="col-md-3">
                        <div class="card p-3 upload_avatar">
                            @php
                               $isset = isset($_SESSION["usuario"][0]["avatar"]);
                            @endphp

                            @if(!$isset)
                                <img class="w-100 avatar" src='{{ $_ENV["BASE_URL"] }}theme/img/avatar.jpg' class="card-img-top">
                            @else
                                <img class="w-100 avatar" src='{{ $_ENV["BASE_URL"] }}app/libs/artify/uploads/{{ $_SESSION["usuario"][0]["avatar"] }}' class="card-img-top">
                            @endif
                        </div>
                    </div>
                    <div class="col-md-9">
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
<script>
    $(document).on("artify_after_submission", function(event, obj, data) {
      $.ajax({
        type: "POST",
        url: '{{ $_ENV["BASE_URL"] }}generar_datos_usuario',
        dataType: "json",
        success: function(response) {
          console.log(response);
          $('.nombre_usuario').text(response['usuario'][0]["nombre"]);
          $(".avatar").attr('src', '{{ $_ENV["BASE_URL"] }}app/libs/artify/uploads/' + response['usuario'][0]['avatar']);
        }
      });
    });
</script>
@include('layouts/footer')