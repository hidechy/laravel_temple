<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bootstrap demo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
</head>
<body>

<div class="container">
    <div id="map" style="width:1000px; height:600px"></div>
</div>

<script type="text/javascript">
    function initMap() {

        var lll0 = "{{ $latLng[0] }}";
        var ll0 = lll0.split('|');

        var opts = {
            zoom: 15,
            center: new google.maps.LatLng(ll0[0], ll0[1])
        };

        var map = new google.maps.Map(document.getElementById("map"), opts);


        //====================// marker
        var latLngStr = "{{ $latLngStr }}";
        var exLLStr = latLngStr.split('/');

        for (var i = 0; i < exLLStr.length; i++) {
            var exVal = exLLStr[i].split('|');
            new google.maps.Marker({
                position: new google.maps.LatLng(exVal[0], exVal[1]),
                map: map
            });
        }
        //====================// marker


    }
</script>

<script async defer
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyD9PkTM1Pur3YzmO-v4VzS0r8ZZ0jRJTIU&callback=initMap">
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz"
        crossorigin="anonymous"></script>
</body>
</html>
