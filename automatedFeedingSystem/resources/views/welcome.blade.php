<!doctype html>
<html lang="en" data-bs-theme="auto">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Arthur Kawalewale, Gift Alufandika, Sydney Chabuka and Chimwemwe Thayo">
    <meta name="generator" content="Hugo 0.111.3">
    <title>Dashboard</title>

    <style>
        @media (min-width: 768px) {

        }

        #myBtn {
            display: none; /* Hidden by default */
            position: fixed; /* Fixed/sticky position */
            bottom: 20px; /* Place the button at the bottom of the page */
            right: 30px; /* Place the button 30px from the right */
            z-index: 99; /* Make sure it does not overlap */
            border: none; /* Remove borders */
            outline: none; /* Remove outline */
            color: white; /* Text color */
            cursor: pointer; /* Add a mouse pointer on hover */
            border-radius: 10px; /* Rounded corners */
            font-size: 15px; /* Increase font size */
        }

        #myBtn:hover {
            background-color: #2e2e5e; /* Add a dark-grey background on hover */
        }

        #Rec{
            display: inline;
            width: 8px;
            height: 12px;
            font-size: 0;
            background-color: red;
            border: 0;
            border-radius: 20px;
            outline: black;
            transform: translate(-20%, -40%);

            animation-name: pulse;
            animation-duration: 1.5s;
            animation-iteration-count: infinite;
            animation-timing-function: linear;
        }

        @keyframes pulse {
            0% {
                box-shadow: 0px 0px 1px 0px rgba(173, 0, 0, .3);
            }
            65% {
                box-shadow: 0px 0px 1px 4px rgba(173, 0, 0, .3);
            }
            90% {
                box-shadow: 0px 0px 1px 4px rgba(173, 0, 0, 0);
            }
        }
    </style>

    @livewireStyles

    @vite(['resources/sass/app.scss'])

</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark" aria-label="navbar">
        <div class="container-fluid">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarsExample11" aria-controls="navbarsExample11" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse d-lg-flex" id="navbarsExample11">
                <h1 class="h2 text-light col-lg-3 me-0">Dashboard</h1>
                <ul class="navbar-nav col-lg-6 justify-content-lg-center">
                    <<li class="nav-item">
                        <a class="nav-link" aria-current="page" href="#">
                            <x-feathericon-home class="align-text-bottom" style="height: 20px"/>
                            Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#reports">
                            <x-feathericon-bar-chart-2 class="align-text-bottom" style="height: 20px"/>
                            Reports
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#controls">
                            <x-feathericon-settings class="align-text-bottom" style="height: 20px"/>
                            Controls
                        </a>
                    </li>
                </ul>
                <div class="d-lg-flex col-lg-3 justify-content-lg-end">
                    <button class="btn btn-primary"><x-feathericon-log-out class="align-text-bottom" style="height: 20px"/> Log out</button>
                </div>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <main class="mx-md-4 mt-4">
            <div class="row">
                <header>
                    <h5>Monitoring <button id="Rec" class="mx-2">Recording</button></h5>
                </header>

                <div class="pb-2 col-md-6">
                    <livewire:dashboard.water-level-data-sets/>
                </div>

                <div class="pb-2 col-md-6">
                    <livewire:dashboard.feed-level-datasets/>
                </div>

            </div>

            <div id="reports" class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <div class="btn-toolbar mb-2 mb-md-0">
                    <div class="dropdown">
                    <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                        <x-feathericon-calendar class="align-text-bottom" style="height: 20px"/>
                        Weekly
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#">Monthly</a></li>
                        <li><a class="dropdown-item" href="#">Yearly</a></li>
                    </ul>
                </div>
                </div>
            </div>

            <div class="row">
                <!--<div class="col-md-6">
                    <canvas class="my-4 w-100" id="myChart" width="450" height="250"></canvas>
                </div>-->

                <div class="col-md-6">
                    {!! $weekly_chart->container() !!}
                </div>

                <div class="col-md-6">
                    <canvas class="my-4 w-100" id="myGraph" width=450" height="250"></canvas>
                </div>
            </div>

            <br>

            <div>
                <button type="button" class="btn btn-secondary mb-3" id="controls"> <x-feathericon-settings class="align-text-bottom" style="height: 20px"/> Controls</button>

                <div class="d-flex justify-content-center align-items-center" style="min-height: 50vh;">
                    <div class="col-lg-6">

                        <form class="border p-4 rounded-5">
                            <fieldset>
                                <legend>
                                    System control
                                    <div class="form-check form-switch d-inline-block align-text-bottom">
                                        <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckDefault"/>
                                    </div>
                                </legend>

                                <div class="row">
                                    <div class="col-md-6">
                                        <fieldset>
                                            <legend><h5>Servo Motor</h5></legend>
                                            <div class="form-check form-switch d-inline-block align-text-bottom">
                                                <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckDefault"/>
                                            </div>
                                        </fieldset>
                                    </div>

                                    <div class="col-md-6">
                                        <fieldset>
                                            <legend><h5>Water Trough Sensor</h5></legend>
                                            <div class="form-check form-switch d-inline-block align-text-bottom">
                                                <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckDefault"/>
                                            </div>
                                        </fieldset>
                                    </div>
                                </div>
                            </fieldset>
                        </form>
                    </div>
                </div>

            </div>
        </main>
    </div>

    <button onclick="topFunction()" id="myBtn" class="btn btn-sm btn-primary" title="Go to top">Top</button>

    <footer class="footer bg-dark container-fluid overflow-hidden">
        <hr>
        <p class="text-muted text-center">Copyright &copy; <?php echo date('Y');?> - All Rights Reserved</p>
    </footer>
</body>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.3.0/dist/chart.umd.min.js">

</script>
@livewireScripts
@stack('scripts')

@vite(['resources/js/app.js'])

<script>
    // Get the button:
    let mybutton = document.getElementById("myBtn");

    // When the user scrolls down 20px from the top of the document, show the button
    window.onscroll = function() {scrollFunction()};

    function scrollFunction() {
        if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
            mybutton.style.display = "block";
        } else {
            mybutton.style.display = "none";
        }
    }

    // When the user clicks on the button, scroll to the top of the document
    function topFunction() {
        document.body.scrollTop = 0; // For Safari
        document.documentElement.scrollTop = 0; // For Chrome, Firefox, IE and Opera
    }

</script>

</html>
