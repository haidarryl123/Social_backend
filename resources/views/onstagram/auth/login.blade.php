@extends("onstagram.auth.layout")
@push("css")

@endpush

@push("js")
    <script src="/onstagram/auth/login.js"></script>
@endpush

@section("content")
    <div class="limiter">
    <div class="container-login100" style="background-image: url('/login_asset/images/bg-01.jpg');">
        <div class="wrap-login100 p-l-55 p-r-55 p-t-65 p-b-54">
            <form class="login100-form validate-form" onsubmit="return false" autocomplete="off">
					<span class="login100-form-title p-b-49">
						{{env("APP_NAME")}}
					</span>

                <div class="wrap-input100 validate-input m-b-23" data-validate = "Email is required">
                    <span class="label-input100">Email</span>
                    <input class="input100" type="text" name="username" id="email" placeholder="Email" value="admin@gmail.com">
                    <span class="focus-input100" data-symbol="&#xf206;"></span>
                </div>

                <div class="wrap-input100 validate-input" data-validate="Password is required">
                    <span class="label-input100">Password</span>
                    <input class="input100" type="password" name="pass" id="password" placeholder="Password">
                    <span class="focus-input100" data-symbol="&#xf190;"></span>
                </div>

                <div class="text-left text-danger p-t-8 p-b-31">
                    <span id="login_error"></span>
                </div>

                <div class="container-login100-form-btn">
                    <div class="wrap-login100-form-btn">
                        <div class="login100-form-bgbtn"></div>
                        <button class="login100-form-btn" id="btnLogin">
                            <i id="btnLoading" class="fa fa-circle-o-notch fa-spin mr-2" style="display: none"></i>Login
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
