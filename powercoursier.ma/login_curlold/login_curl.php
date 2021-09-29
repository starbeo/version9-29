<?php
if(isset($_POST['email']) && !empty($_POST['email']) && isset($_POST['password']) && !empty($_POST['password'])){
    $post = [
        'token' => '2aWf13rVh3fN5UAr2Yr',
        'email' => $_POST['email'],
        'password' => $_POST['password']
    ];

    $ch = curl_init('https://powercoursier.ma/Recette/authentication/api');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    $response = curl_exec($ch);
    curl_close($ch);
    $result = json_decode($response);
    if($result->success == true) {
        $_SESSION['__ci_last_regenerate'] = $result->session_generate;
        $_SESSION['expediteur_user_id'] = $result->user_id;
        $_SESSION['staff_user_id_entreprise'] = $result->entreprise_id;
        $_SESSION['expediteur_logged_in'] = $result->logged_in;    
    } else {
        $_SESSION['__ci_last_regenerate'] = '';
        $_SESSION['expediteur_user_id'] = '';
        $_SESSION['staff_user_id_entreprise'] = '';
        $_SESSION['expediteur_logged_in'] = ''; 
    }
    
    echo($response);
}
?>