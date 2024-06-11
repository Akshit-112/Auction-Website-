<?php



// define the database connection contants here!
define('DB_HOST', 'localhost');
define('DB_USER', 'amehta');
define('DB_PASSWORD', 'zc23lzc23l295hg295hg');
define('DB_NAME','amehtaauction');

define("CONFIG_ADMIN", "Akshit Mehta");
define("CONFIG_ADMINEMAIL", "akkimehta1102@gmail.com");
define("CONFIG_URL", "https://amehta.scweb.ca/Auction/");
define("CONFIG_AUCTIONNAME", "Web Guys Online Auction");
define("CONFIG_CURRENCY", "$");
date_default_timezone_set("America/Toronto");
define("LOG_LOCATION", __DIR__ . "/../../logs/app.log");
define("FILE_UPLOADLOC", "imgs/");

//paypal account
define("CLIENT_ID", "AWiuzYSWLwOyw_zGTMvacWuLgTR2IjIYAaTP1-PveQekBVeF6B3EIYN9B89FNOqQ08bZHTVGg2s4p9bw");
define("CLIENT_SECRET", "EG5ozZ9FZaCvWcByGEZ84Mg9BH1KFoJ7ilUgxaLl83HHVZvNouMpmR2xFEGBXiMBTD5E4whO-3L8MNQL");
define("WEBHOOK_ID", "5D9454898T583181W");
//Default currency dollars
define("PAYPAL_CURRENCY", "CAD");
define("PAYPAL_RETURNURL", "https://amehta.scweb.ca/Auction/payment-successful.php");
define("PAYPAL_CANCELURL", "https://amehta.scweb.ca/Auction/payment-cancelled.php")
?>