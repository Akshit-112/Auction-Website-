<?php
use App\Models\Item;

require_once(__DIR__ . '/../app/bootstrap.php');
require_once (__DIR__ .'/../app/Layouts/header.php');

//App\Lib\Database::getConnection();


//INCLUDE THE CODE TO REQUIRE THE BOOTSTRAP.PHP HERE
if (isset($_GET['id'])) {
    $validid = pf_validate_number($_GET['id'], "value",
        CONFIG_URL);
} else {
    $validid = 0;
}
//INCLUDE THE CODE TO REQUIRE THE HEADER.PHP HERE

if ($validid == 0) {
    //if a category id has not been specified, retrieve all items
    //as an array of objs that are not expired
    $items = Item::find("date > NOW()");
} else {
    //if a category id had been specifies,retrieve all items as an array of objs
    //that are not expired and are a member of the category
    $items = Item::find("date >NOW() AND cat_id = $validid");

}
?>

    <h1 > Items available </h1 >

    <table cellpadding = '5' >
        <tr >
            <th > Image</th >
            <th > Item</th >
            <th > Bids</th >
            <th > Price</th >
            <th > End Date for this Item </th >
        </tr >

<?php
if (!$items) {
    echo "<tr><td colspan=4>No items! </td></tr>";
} else {
    //iterate over each item object
    //@var $item \App\Models\Item
    foreach ($items as $item) {
        echo "<tr>";
        //load image objects into item
        //add the code to load the images into $item

        $item->getImages();

        //if there are no images,alert user

        if (!$item->get('imageObjs')) {
            echo "<td>No image</td>";
        } else {
            //return only the first image obj from the array of
            //image objects in the item
            $img = $item->get('imageObjs');
            $firstImg = array_shift($img);

            echo "<td><img src='imgs/" . $firstImg->get('name') . "' width='100'></td>";
        }
        echo "<td>";
        echo "<a href='itemdetails.php?id={$item->get('id')}'>{$item->get('name')}</a>";
        echo "</td>";

        echo "<td>";
        //Load Bid objects into item
        $item->getBids();

        //if thereARE NO BID OBJECTS,ALERT USER
        if (!count($item->get('bidObjs'))) {
            echo "0";
        } else {
            //display the number of bids to the user
            echo count($item->get('bidObjs'));
        }

        echo "</td>";
        echo "<td>" . CONFIG_CURRENCY;
        //IF THERE ARE NO BIDS FOR THE item,display the items starting
        //price,otherwise display the highest bid

        if (!$item->get('bidObjs')) {
            echo sprintf('%.2f', $item->get('price'));
        } else {
            $itemBids = $item->get('bidObjs');
            $highestBid = array_shift($itemBids);
            echo sprintf('%.2f', $highestBid->get('amount'));
        }
        echo "</td>";

        echo "<td>" . date("D jS F Y g.iA", strtotime($item
                ->get('date'))) . "</td>";
        echo "</tr>";
    }
}
echo "</table>";
require_once (__DIR__ .'/../app/Layouts/footer.php');
//INCLUDE THE CODE TO REQUIRE THE FOOTER.PHP HERE
?>