<div class="form-popup animate" id="myForm">
    <span onclick="$('#myForm').hide();
            $('#lien_login').show()" class="close_form" >&times;</span>
    <form  class="form-container" id="form-login" onsubmit="return login()">
        <img src="new_img/login-2.png" class="avatar">
        <h1 style="text-align: center;">Espace MyColis</h1>
        <input type="email" placeholder="Enter Email"  required id="email_login">
        <input type="password" placeholder="Enter Password"  required id="password_login">
        <button type="submit" class="btn">Login</button>
    </form>
    <form action="/action_page.php" class="form-container" id="form-passe" style="margin-top: 40px">
        <img src="new_img/login-2.png" class="avatar">
        <h1 style="text-align: center;">Mot de passe</h1>
        <input type="text" placeholder="Enter Email" name="email" required>
        <button type="submit" class="btn">Envoyer</button>
        <br>
        <a  style="float: right;cursor: pointer;"  
            onclick="$('#form-login').show();
                    $('#form-passe').hide()"
            >Se Connecter</a>
    </form>
</div>

<script>
    function login() {
        var email_login = $('#email_login').val();
        var password_login = $('#password_login').val();
        $.ajax({
            type: 'POST',
            url: "login_curl/login_curl.php",
            data: ({email: email_login, password: password_login})
        }).done(function (data) {
            var obj = jQuery.parseJSON(data);
            if (obj.success === false) {
                alert(obj.message);
            } else {
               // window.location.replace(obj.redirect_url);
            }
        });

        return false;
    }
</script>