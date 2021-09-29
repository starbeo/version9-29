$(document).ready(function () {
    var wall = getCookie('wallpaper');
    if (wall !== '') {
        showWallpaper(wall);
    }
});
// Show & Hide Password
function showHidePassword() {
    var element = document.getElementById("password");
    if (element.type === "password") {
        element.type = "text";
    } else {
        element.type = "password";
    }
}
// Update Wallpaper
function modifyWallpaper() {
    var elt = document.getElementById('configuration').className;
    if (elt === 'hide-configuration') {
        document.getElementById('configuration').className = 'login-configuration';
        document.getElementById('containerWallId').style.display = 'block';
        document.getElementById('textWallId').style.display = 'block';
    } else {
        document.getElementById('configuration').className = 'hide-configuration';
        document.getElementById('containerWallId').style.display = 'none';
        document.getElementById('textWallId').style.display = 'none';
    }
}
// Show Wallpaper
function showWallpaper(wall) {
    wall = wall.substr(0, wall.lastIndexOf('.'));
    document.getElementById('wallId').className = 'login_admin wall-' + wall;
}
// Change Wallpaper
function changeWallpaper(wall) {
    document.cookie = 'wallpaper=' + wall + ';expires=Thu, 01 Jan 2970 00:00:00 UTC';
    wall = wall.substr(0, wall.lastIndexOf('.'));
    document.getElementById('wallId').className = 'login_admin wall-' + wall;
    modifyWallpaper();
}
// Get Cookie
function getCookie(cname) {
    var name = cname + "=";
    var decodedCookie = decodeURIComponent(document.cookie);
    var ca = decodedCookie.split(';');
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) === ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) === 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}