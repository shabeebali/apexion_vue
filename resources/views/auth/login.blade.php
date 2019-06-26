<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" style="height: 100%;">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet" type="text/css">
    <link href='https://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Material+Icons' rel="stylesheet">
    <!-- Styles -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">

    <!-- Compiled and minified JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
</head>
<body class="teal" style="height: 100%;">
    <div class="container-fluid" style="height: 100%; background-image: url('svg/apexion_logo.svg');background-size: cover; background-position-y: center;">
        <div class="row" style="padding-top:20px;">
            <div class="col m4 s12"></div>
            <div class="col m4 s12">
                <div class="card">
                    <div class="card-content">
                        <div class="card-title">{{ __('Login') }}</div>
                        <div class="row">
                            <form method="POST" class="col s12" action="{{ route('login') }}">
                                @csrf
                                <div class="row">
                                    <div class="input-field col s12">
                                        <input value="{{ old('email') }}" id="email" name="email" type="email" class="validate {{ $errors->has('email') ? 'invalid' : '' }}" required>
                                        <label for="email">{{ __('E-Mail Address') }}</label>
                                        <span class="helper-text" data-error="{{ $errors->first('email') }}"></span>
                                    </div>
                                    <div class="input-field col s12">
                                        <input  id="password" name="password" type="password" class="validate {{ $errors->has('password') ? 'invalid' : '' }}" required>
                                        <label for="password">{{ __('Password') }}</label>
                                        <span class="helper-text" data-error="{{ $errors->first('password') }}"></span>
                                    </div>
                                    <p class="col s12">
                                        <label for="remember">
                                            <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                            <span> Remember Me </span>
                                        </label>
                                    </p>
                                </div>

                                <div class="card-action">
                                    <div class="row">
                                        <div class="col s12">
                                            <button type="submit" class="btn btn-primary teal btn-flat white-text" style="width:100%">
                                                {{ __('Login') }}
                                            </button>
                                            <div class="col s12 center-align" style="margin-top:10px;">
                                            @if (Route::has('password.request'))
                                                <a class="teal-text" href="{{ route('password.request') }}" style="margin:0">
                                                    {{ __('Forgot Your Password?') }}
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col m4 s12"></div>
        </div>
    </div>
</body>
</html>