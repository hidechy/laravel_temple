<!doctype html>
<html lang="ja">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css"
          integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">

    <title>temple input</title>
</head>
<body>

<div class="container">

    <h1>temple input</h1>
    
    <div class="row-12">

        <form method="POST" action="{{ url('/templestore') }}">
            {{ csrf_field() }}

            <table class="table table-bordered">

                <tr>
                    <td>Date</td>
                    <td>
                        <select name="date">
                            <option></option>
                            @foreach ($diff_date as $date)
                                <option value="<?=$date?>"><?=$date?></option>
                            @endforeach
                        </select>
                    </td>
                </tr>

                <tr>
                    <td>Temple</td>
                    <td>
                        <input type="text" name="temple" class="form-control">
                    </td>
                </tr>

                <tr>
                    <td>Memo</td>
                    <td>
                        <input type="text" name="memo" class="form-control">
                    </td>
                </tr>

                <tr>
                    <td>Address</td>
                    <td>
                        <input type="text" name="address" class="form-control">
                    </td>
                </tr>

                <tr>
                    <td>Station</td>
                    <td>
                        <input type="text" name="station" class="form-control">
                    </td>
                </tr>

                <tr>
                    <td>Gohonzon</td>
                    <td>
                        <input type="text" name="gohonzon" class="form-control">
                    </td>
                </tr>

            </table>

            <div class="text-center p-3">
                <button type="submit" class="btn btn-primary">input</button>
            </div>

        </form>
    </div>
</div>

<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"
        integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj"
        crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"
        integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo"
        crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"
        integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI"
        crossorigin="anonymous"></script>
</body>
</html>
