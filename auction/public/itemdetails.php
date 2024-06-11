<?php

use App\Exceptions\ClassException;
use App\Lib\Logger;
use App\Models\Bid;
use App\Models\Item;
use App\Models\User;

require_once (__DIR__ . '/../app/bootstrap.php');
$validid = pf_validate_number($_GET['id'],"redirect",
    CONFIG_URL);
//fetch from the database and create an item object which
//matches the given id
try{
    $item = Item::findFirst(["id" => "$validid"]);

}
catch(ClassException $e) {
    Logger::getLogger()->critical ("Invalid Item: ",
        ['exception'=> $e]);
    echo "Invalid Item";
    die();
}
//load Bid $Image objects into item
//ADD CODE TO LAZY LOAD BOTH IMAGE AND BID OBJECTS
$item->getBids();
$item->getImages();

if(isset($_POST['submit'])) {
    if (is_numeric($_POST['bid']) == false) {
        header("Location: itemdetails.php?id=" . $validid . "&error=letter");

        die();
    }
    $validbid = false;
    //if the number of bids on the item are 0
    if (count($item->get('bidObjs')) == 0) {
        //if the user price is greater than the starting price it"s valid
        $price = intval($item->get('price'));
        $postedBid = intval($_POST['bid']);

        if ($postedBid >= $price) {
            $validbid = true;
        }
    } else {
        //if the user price is greater than the highest bid it is valid
        $bid = $item->get('bidObjs');
        $highestBid = array_shift($bids);
        $highestBid = intval($highestBid->get('amount'));
        $postedBid = intval($_POST['bid']);
        if ($postedBid > $highestBid) {
            $validbid = true;
        }
    }
    if($validbid == false) {
        header("Location: itemdetails.php?id=" . $validid . "&error=lowprice#bidbox");
        die();
    } else{
        //create a new bid object with the given information and create a new record in the database
        $newBid = new Bid($item->get('id'), $_POST['bid'], $session->getUser()->get('id'));
        $newBid->create();
        header("Location: itemdetails.php?id=" . $validid);
        die();
    }
}
//include the code to require the header.php
require(__DIR__ . "/../app/Layouts/header.php");

$nowepoch = time();
$itemepoch = strtotime($item->get('date'));

$validAuction = false;
if($itemepoch > $nowepoch) {
    $validAuction = true;
}

//insert code to output the item name in H1 tags
echo "<h1>{$item->get('name')}</h1>";
echo "<p>";

//code to check if there are bid objects
if (count($item->get('bidObjs')) == 0){
    echo "<strong>This item has had no bids</strong> -
        <strong>Starting Price</strong>: ". CONFIG_CURRENCY .  sprintf('%.2f', $item->get('price'));
} else{
    $bids = $item->get('bidObjs');
    $highestBid = array_shift($bids);
    echo "<strong>Number of Bids</strong> " .
        count($item->get('bidObjs')).
        " - <strong>Current Price</strong>: " .
        CONFIG_CURRENCY . sprintf('%.2f',$highestBid->get('amount'));
}
echo "- <strong>Auction ends</strong>: ".
    date("D jS F Y g.iA", $itemepoch);
echo "</p>";

//retrieve the first image object from the array of images
$imgs = $item->get('imageObjs');
$img = array_shift($imgs);

//code to check if there are ant images
if ($img) {
    echo "<img src='imgs/{$img->get('name')}' width = '200'>";
}else {
    echo "No images";
}
echo "<p>". nl2br($item->get('description')). "</p>";

echo "<a name='bidbox'></a>";
echo "<h2>Bid for this item</h2>";


//if the current user is logged in
//code to check if the user is logged in

if(!$session->isLoggedIn()){
    echo "To bid, you need to log in. Login
        <a href='login.php?id=" .
        $validid . "&ref=addbid'>here</a>.";
} else{
    if($validAuction == true){
        echo "Enter the bid amount into the box below.";
        echo "<p>";

        if(isset($_GET['error'])) {
            try {
                $errorMsg = Item::displayError($_GET['error']);
            } catch (ClassException $e) {
                Logger::getLogger()->error("Invalid error code: ",
                    ['exception' => $e]);
                die();
            }
            echo $errorMsg;
        }
        ?>

        <form action="<?php echo $_SERVER['REQUEST_URI']; ?>"
              method="post">
            <table>
                <tr>
                    <td><input type="number" name="bid"></td>
                    <td><input type="submit" name="submit" id="submit"
                               value="Bid!"></td>
                </tr>
            </table>
        </form>

        <?php
    }else {
        echo "This auction has new ended.";
    }
    //if there are more than one bid on the item
    if(count($item->get('bidObjs')) > 0) {
        echo "<h2>Bid History</h2>";
        echo "<ul>";

        //retrieve the array of bids from the item and display
        $bids= $item->get('bidObjs');

        //code to iterate over all bids objects
        foreach ($bids as $bid) {
            $id = $bid->get('user_id');
            try{
                $user = User::findFirst(["id" => "$id"]);
            } catch (ClassException $e){
                Logger::getLogger()->critical("Invalid User: ",
                    ['exception' => $e]);
                echo "Invalid User";
                die();
            }
            echo "<li>{$user->get('username')} - ".
                CONFIG_CURRENCY .sprintf('%.2f', $bid->get('amount')) .
                "</li>";
        }
        echo "</ul>";
    }
}
require_once(__DIR__.'/../app/Layouts/footer.php');
?>
