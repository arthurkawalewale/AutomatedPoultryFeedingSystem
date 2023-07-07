<!doctype html>
<html lang="en" data-bs-theme="auto">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Arthur Kawalewale, Gift Alufandika, Sydney Chabuka and Chimwemwe Thayo">
    <meta name="generator" content="Hugo 0.111.3">
    <meta name="csrf-token" content="{{ csrf_token() }}">

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

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark" aria-label="navbar">
            <div class="container-fluid">
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarsExample11" aria-controls="navbarsExample11" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse d-lg-flex" id="navbarsExample11">
                    <h1 class="h3 col-lg-3 me-0">
                        <a href="/" class="text-light" style="text-decoration:none">Dashboard</a>
                    </h1>
                    @guest

                    @else
                        <ul class="navbar-nav col-lg-6 justify-content-lg-center">
                            <li class="nav-item">
                                <a class="nav-link" href="/#reports">
                                    <x-feathericon-bar-chart-2 class="align-text-bottom" style="height: 20px"/>
                                    Statistics
                                </a>
                            </li>
                            <li class="nav-item">
                                <!--<a class="nav-link" href="/#controls">
                                    <x-feathericon-settings class="align-text-bottom" style="height: 20px"/>
                                    Controls
                                </a>-->

                                <a class="nav-link" href="/profile">
                                     <x-feathericon-user class="align-text-bottom" style="height: 20px"/>
                                     Profile
                                </a>
                            </li>
                        </ul>
                    @endguest

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        <!-- Authentication Links -->
                        @guest
                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">

                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        <x-feathericon-log-out class="align-text-bottom" style="height: 20px"/>
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>

                        @endguest
                    </ul>
                </div>
            </div>
        </nav>


        <main class="py-4">
            @yield('content')
        </main>
    </div>
    @guest
    @else
        <footer class="footer bg-dark container-fluid overflow-hidden">
            <hr>
            <p class="text-muted text-center">Copyright &copy; <?php echo date('Y');?> - All Rights Reserved</p>
        </footer>
    @endguest
</body>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.3.0/dist/chart.umd.min.js">

</script>
@livewireScripts
@stack('scripts')
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
