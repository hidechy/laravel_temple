<?php
$ex_phpself = explode("/", $_SERVER['PHP_SELF']);
array_pop($ex_phpself);
$public_path = implode("/", $ex_phpself);
?>

        <!doctype html>
<html lang="ja">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css"
          integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">

    <title>TempleList</title>

    <script src="{{ $public_path }}/js/jquery-3.3.1.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
</head>
<body>

<div class="jumbotron">
    <h1 class="display-3">TempleList</h1>
    <p class="lead">今までに参拝した神社のリストです</p>
    <hr class="my-2">
    <!-- <p class="lead">
      <a class="btn btn-primary btn-lg" href="#!" role="button">Some action</a>
    </p> -->
</div>

<div class="container">

    <div class="row my-3">
        <div class="col-12">
            <a href="{{ url('/templecreate') }}" target="_blank">templecreate</a>
        </div>
    </div>

    <div class="row my-3">
        <div class="col-12">
            <a href="{{ url('/templeaddress') }}" target="_blank">templeaddress</a>
        </div>
    </div>

    <?php
    foreach ($photolist as $year => $v) {

        echo "<div class='row'>";
        echo "<div class='col-12 py-2 mb-0 mt-5 alert alert-success'>" . $year . "</div>";
        echo "</div>";

        foreach ($v as $date => $v2) {
            echo "<div class='row'>";

            echo "<div class='col-3 py-1'>";
            echo "<div class='badge badge-success mr-3 px-2' onclick='javascript:openPhotoDiv(\"" . $date . "\")'>photo</div>";
            echo "<div class='badge badge-info mr-3 px-2' onclick='javascript:openMapWindow(\"" . $date . "\")'>map</div>";
            echo $date;
            echo "</div>";

            if (isset($explanation[$date]['temple'])) {
                if (trim($explanation[$date]['temple']) != "") {
                    echo "<div class='col-9 py-1'>";
                    echo $explanation[$date]['temple'];

                    if (isset($explanation[$date]['memo'])) {
                        if (trim($explanation[$date]['memo']) != "") {
                            echo " ＆ " . $explanation[$date]['memo'];
                        }
                    }

                    if (isset($explanation[$date]['gohonzon'])) {
                        if (trim($explanation[$date]['gohonzon']) != "") {
                            echo " // " . $explanation[$date]['gohonzon'];
                        }
                    }

                    echo "</div>";
                }
            }

            echo "</div>";

            echo "<input type='hidden' id='openPhotoStatus_" . $date . "' value='close'>";
            echo "<div class='row' id='photoDiv_" . $date . "'></div>";
        }
    }
    ?>
    <br><br><br>

</div>

<div class="modal js-modal text-center">
    <div class="modal__bg js-modal-close"></div>
    <div class="modal__content">
        <p id="modal_photo"></p>
        <button class="js-modal-close btn btn-warning">閉じる</button>
    </div><!--modal__inner-->
</div><!--modal-->

<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.4.1.min.js"
        integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n"
        crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"
        integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo"
        crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"
        integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6"
        crossorigin="anonymous"></script>

<style type="text/css">
    .badge-success {
        cursor: pointer;
    }

    .jumbotron {
        background: url({{ $public_path }}/img/temple.jpg);
        background-position: center;
        color: #ffffff;
    }

    .modal {
        display: none;
        height: 100vh;
        position: fixed;
        top: 0;
        width: 100%;
    }

    .modal__bg {
        background: rgba(0, 0, 0, 0.8);
        height: 100vh;
        position: absolute;
        width: 100%;
    }

    .modal__content {
        background: #fff;
        left: 50%;
        padding: 40px;
        position: absolute;
        top: 50%;
        transform: translate(-50%, -50%);
        width: 60%;
    }

</style>

<script>


    function openMapWindow(date) {
        window.open('{{ $appUrl }}/' + date + '/templemap');
    }


    function openPhotoDiv(date) {
        if ($("#openPhotoStatus_" + date).val() == "close") {

            //写真呼び出し（写真が呼び出されていない場合）
            if ($("#photoDiv_" + date).html() == "") {
                var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                $.ajax({
                    url: '{{ $appUrl }}/callphoto',
                    type: 'POST',
                    data: {
                        _token: CSRF_TOKEN,
                        'date': date
                    },
                    success: function (data) {
                        if (data != "") {
                            $("#photoDiv_" + date).html(data);
                        }
                    }
                });
            }

            $("#openPhotoStatus_" + date).val("open");
            $("#photoDiv_" + date).slideDown();
        } else {
            $("#openPhotoStatus_" + date).val("close");
            $("#photoDiv_" + date).slideUp();
        }
    }

    function modalOpen(imgsrc) {
        $('.js-modal').fadeIn();
        $("#modal_photo").html("<img src='" + imgsrc + "' style='width:400px;'>");
        return false;
    }

    $('.js-modal-close').on('click', function () {
        $('.js-modal').fadeOut();
        return false;
    });

</script>

</body>
</html>
