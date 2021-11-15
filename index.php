<!DOCTYPE html>
<html>
    <head>
<title>Varcyon Sariou Games </title>
<link href= "https://fonts.googleapis.com/css?family=Coda" rel="stylesheet">
<meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
<link href="css/global.css" rel="stylesheet" type="text/css">
<link href="css/home.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="js/jQuery.js"></script>
<script type="text/javascript" src="js/loader.js"></script>
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
</head>
<body>


<div class="content">
    <div class="content-inner">
        <div class="content-inner-padding">
    
         </div>
    </div>  
</div>
<div class="footer-container">
<div span id="load_test"> Loading overlay test ( lasts 3 secs)</span></div>
</body>
</html>