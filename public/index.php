<!DOCTYPE html>
<?php
include "../php/autoloader.php";
include_once "../php/twitch_api.php"
?>
<head>
<title>Varcyon Sariou Games </title>
<meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<link href= "https://fonts.googleapis.com/css?family=Coda" rel="stylesheet">
<link href="../css/global.css" rel="stylesheet" type="text/css">
<link href="../css/home.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="../js/jQuery.js"></script>
<script type="text/javascript" src="../js/loader.js"></script>
<script>
    $(function(){
        loader.initialize()
        $ ('#load_test').on('click',function(){
        loader.showLoader();
        setInterval(() => {
            loader.hideLoader();
        }, 3000);
    });
});
</script>
<?php
if(isset($_GET['code']) && isset($_GET['state']) && $_GET['state']== $_SESSION['twitch_state']){
    $vsgTwitchApi = new vsgTwitchAPI(TWITCH_CLIENT_ID,TWITCH_CLIENT_SECRET);
    $twitchLogin = $vsgTwitchApi->tryAndLoginWithTwitch($_GET['code'],TWITCH_REDIRECT_URI);
}

$vsgTwitchApi = new vsgTwitchAPI(TWITCH_CLIENT_ID,TWITCH_CLIENT_SECRET);
$TwitchLoginUrl = $vsgTwitchApi->getLoginUrl(TWITCH_REDIRECT_URI);

?>
</head>
<body>


<div class="content">
    <div class="content-inner">
        <div class="content-inner-padding">
            <div> Future home of the VarcyonSariouGames Bot!! </div>
                <a href="<?php print $TwitchLoginUrl; ?>" class="a-twitch">
                    <div class="twitch-button-container">
                        Twitch Login
                    </div> 
                </a>
            </div>
        </div>
    </div>  
</div>
<div class="footer-container">
<div span id="load_test"> Loading overlay test ( lasts 3 secs)</span></div>
</body>
