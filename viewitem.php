<?php
    include 'connection.php';
    include 'header.php';
    session_start();

    //Get item ID
    $item_id = mysqli_real_escape_string($conn, $_GET['id']);

    $sql = "SELECT * FROM product WHERE product_id = '$item_id'";

    $result = mysqli_query($conn, $sql);
    if($result){
        if(mysqli_num_rows($result) == 0){
            echo 'There\'s not any information on this item.';
        }else{
            $item_details = mysqli_fetch_assoc($result);
            echo '
                <div class="container">
                    <div class="row d-flex align-items-center">
                    <div class="col">
                        <h3 class="text-center">' . $item_details['product_name'] . '</h3>
                    </div>
                    </div>
                </div>
                <div class="container">
                    <div class="row">
                        <div class="col-6">
                            <img src="' . $item_details['product_img'] . '" alt="product image" class="productimg">
                        </div>
                        <div class="col-6">
                            <p>' . $item_details['product_description'] . '</p>
                            <p>' . $item_details['price'] . '</p>
                            <form method="post" action="index.php?action=add&product_id=' . $item_details['product_id'] . '">    
                                <div class="pull-right"><input type="submit" value="Add to Cart" class="btnAddAction"/>
                                    <select name="quantity" id="quantity">
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                        <option value="4">4</option>
                                        <option value="5">5</option>
                                        <option value="6">6</option>
                                        <option value="7">7</option>
                                        <option value="8">8</option>
                                        <option value="9">9</option>
                                        <option value="10">10</option>
                                    </select>
                                </div>
                            </form>
                                                                
                        </div>
                    </div>
                </div>';
        }
    }else{
        echo 'Something went wrong, please try again later.';
    }


?>

<?php
    include 'footer.php';
?>