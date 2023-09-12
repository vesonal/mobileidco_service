
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="DJ-Authmobileid" />
        <meta name="author" content="DJ-Authmobileid" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
        <meta http-equiv="Pragma" content="no-cache" />
        <meta http-equiv="Expires" content="0" />
        <title>mobileid</title>
        <!-- Favicon-->
        <link rel="shortcut icon" href="https://dj.authmobileid.com/public/favicon.ico">
        <script src="https://use.fontawesome.com/releases/v5.15.3/js/all.js" crossorigin="anonymous"></script>
        <!-- Google fonts-->
        <link href="https://fonts.googleapis.com/css?family=Catamaran:100,200,300,400,500,600,700,800,900" rel="stylesheet" />
        <link href="https://fonts.googleapis.com/css?family=Lato:100,100i,300,300i,400,400i,700,700i,900,900i" rel="stylesheet" />
        <!-- Core theme CSS (includes Bootstrap)-->
        <link href="{{asset('assets/css/style.css')}}" rel="stylesheet" />
    </head>
    <body id="page-top">
        <section class="page-header page-header-text-light bg-dark-3 py-5">
    <div class="container">
        <div class="row text-center">
            <div class="col-12">
                <h1>{{$privacy->title}}</h1>
            </div>
        </div>
    </div>
</section>
<section id="scroll" style="height: 58vh;">
    <div class="container">
        <div id="content">
            <section class="section bg-white">
                <div class="container">
                    <div class="row no-gutters">
                        <div class="my-auto px-0 px-lg-5">
                           {!! $privacy->description !!}
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</section>

        
    </body>
</html>
