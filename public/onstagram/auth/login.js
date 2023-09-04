
let btnLogin = $("#btnLogin");
let btnLoading = $("#btnLoading");
let login_error = $("#login_error");

btnLogin.click(function (){
   let email = $("#email").val();
   let password = $("#password").val();
   if (email.length === 0 || password.length === 0){
       return;
   }
   login(email,password);
});

function login(email,password){
    const data = {};
    data.email = email;
    data.password = password;
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        url: '/auth/post-login',
        data: JSON.stringify(data),
        contentType: "application/json; charset=utf-8",
        traditional: true,
        processData: false,
        type: 'POST',
        beforeSend: function() {
            btnLogin.prop("disabled",true);
            btnLoading.show();
            login_error.text("");
        },
        success: function (response) {
            btnLogin.prop("disabled",false);
            btnLoading.hide();
            if (response.result === 'success'){
                notify.open({ type: 'success', message: response.message });
                setTimeout(function (){ window.location.href = "/admin/user"; },1000);
            } else {
                login_error.text(response.message);
            }
        },
        error: function (response) {
            btnLogin.prop("disabled",false);
            btnLoading.hide();
            notify.open({ type: 'error', message: 'Unexpected error.' });
        }
    });
}
