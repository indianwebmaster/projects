<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

    <title>YaMaVi League</title>
</head>
<?php
    $http_mode = 'http';
    if (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on')) $http_mode = 'https';

    $this_url = $http_mode . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
?>
<body>
<div class="container-fluid">
    <a href="<?= $this_url ?>"><img src="img/YaMaVi_Logo_13.png" height='15%' width='15%'></a>
    <div class='row'>
        <div class='col col-md-auto'>
            <table class='table table-sm table-responsive table-striped table-bordered'>
                <tr class="table-primary"><th colspan=3>Leagues over the years</th></tr>
                <tr class="table-info"><th>Year</th><th>Tournament</th><th>Winner</th></tr>
                <tr><td>2020</td><td><a href="web/index_ipl2020.php">IPL 2020</a></td><td></td></tr>
                <tr><td>2019</td><td><a href="web/index_odi_worldcup2019.php">ICC ODI World Cup 2019 in England</a></td><td></td></tr>
                <tr><td>2019</td><td><a href="web/index_ipl2019.php">IPL 2019</a></td><td>Yash</td></tr>
                <tr><td>2018</td><td><a href="ipl2018/ipl2018.pdf" target="_blank">IPL 2018</a></td><td>Vikas</td></tr>
                <tr><td>2017</td><td><a href="ipl2017/ipl2017.pdf" target="_blank">IPL 2017</a></td><td>Manoj</td></tr>
            </table>
        </div>
    </div>
</div>
</body>
<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</html>
