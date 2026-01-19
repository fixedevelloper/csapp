<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{env('APP_NAME')}} | Login</title>
    <link rel="stylesheet" href="{{asset('css/bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{asset('css/app.min.css')}}">
    <link rel="stylesheet" href="{{asset('css/icons.min.css')}}">
</head>
<body class="loading authentication-bg authentication-bg-pattern">
<div class="account-pages mt-5 mb-5">
    <div class="container">

        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6 col-xl-4">
                <div class="text-center">
                    <a href="{{route('dashboard')}}">
                        <img src="#" alt="{{env('APP_NAME')}}" height="22" class="mx-auto">
                    </a>
                    <p class="text-muted mt-2 mb-4">{{env('APP_NAME')}}</p>
                </div>
                @include("back._partials.errors-and-messages")
                <div class="card">

                    <div class="card-body p-4">

                        <div class="text-center mb-4">
                            <h4 class="text-uppercase mt-0 mb-3">Reset Password</h4>
                            <p class="text-muted mb-0 font-13">Enter your email address and we'll send you an email with instructions to reset your password.  </p>
                        </div>

                        <form action="{{route('reset_password')}}" method="POST">
                            {{csrf_field()}}
                            <div class="mb-3">
                                <label for="emailaddress" class="form-label">Email address</label>
                                <input class="form-control" name="email" type="email" id="emailaddress" required="" placeholder="Enter your email">
                            </div>

                            <div class="mb-3 text-center d-grid">
                                <button class="btn btn-primary" type="submit"> Reset Password </button>
                            </div>

                        </form>

                    </div> <!-- end card-body -->
                </div>
                <!-- end card -->

                <div class="row mt-3">
                    <div class="col-12 text-center">
                        <p class="text-muted">Back to <a href="{{route('login')}}" class="text-dark ml-1"><b>Log in</b></a></p>
                    </div> <!-- end col -->
                </div>
                <!-- end row -->

            </div> <!-- end col -->
        </div>
        <!-- end row -->
    </div>
    <!-- end container -->
</div>
<!-- end page -->
<script src="{{asset('js/vendor.min.js') }}"></script>
<script src="{{asset('js/app.min.js') }}"></script>

</body>

</html>
