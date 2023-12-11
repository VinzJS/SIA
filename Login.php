<?php require_once('Auth.php') ?>
<?php require_once('GoogleAPI/vendor/autoload.php') ?>

<?php

    $clientID = "325050421194-jcrlqda3fbne26abr8f5jjtfg6hpekl8.apps.googleusercontent.com";
    $secret = "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";

    // Google API Client
    $gclient = new Google_Client();

    $gclient->setClientId($clientID);
    $gclient->setClientSecret($secret);
    $gclient->setRedirectUri('http://localhost/SIA/Login.php');

    $gclient->addScope('email');
    $gclient->addScope('profile');

    if(isset($_GET['code']))
    {
        $token = $gclient->fetchAccessTokenWithAuthCode($_GET['code']);
        
        if(!isset($token['error']))
        {
            $gclient->setAccessToken($token['access_token']);

            $_SESSION['access_token'] = $token['access_token'];

            $gservice = new Google_Service_Oauth2($gclient);

            $udata = $gservice->userinfo->get();
            foreach($udata as $k => $v)
            {
                $_SESSION['login_'.$k] = $v;
            }
            $_SESSION['ucode'] = $_GET['code'];

            header('location: ./');
            exit;
        }
    }

?>

<!DOCTYPE html>
<html lang="en">

    <head>

        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title> Vinzledo Shop </title>
        
        <link rel="stylesheet" href="CSS/Style_Login.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
        <script src="https://code.jquery.com/jquery-3.6.1.min.js" integrity="sha256-o88AwQnZB+VDvE9tvIXrMQaPlFFSUTR+nldQm1LuPXQ=" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-OERcA2EqjJCMA+/3y+gxIOqMEjwtxJY7qPCqsdltbNJuaOe923+mo//f6V8Qbsw3" crossorigin="anonymous"></script>

    </head>

    <body>

        <img src = "IMG/Logo2.png" width = "100px" class = "logo">

        <div class = "container2">

            <h1> Welcome to Vinzledo Shop </h1>

        </div>

        <div class = "google-btn">

            <a href="<?= $gclient->createAuthUrl() ?>"> <img src = "IMG/Google.png" width = 25px> &nbsp; Login with Google </a>
            
        </div>

    </body>

</html>