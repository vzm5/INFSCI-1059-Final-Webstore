<?php
    include 'header.php';
    include 'connection.php';
    print_r($_SESSION['cart_items']);
    //handle shopping cart
    if(!empty($_GET['action'])){
        switch($_GET['action']){
            case 'add':
                if(!empty($_POST['quantity'])){
                    //get item code
                    $product_id = $_GET['product_id'];
                    $product_id_clean = test_input($product_id);
                    
                    //sanitize the item code before it goes into the database
                    $sql = "SELECT * FROM product WHERE product_id='" . $product_id_clean . "'";

                    $result = mysqli_query($conn, $sql);
                    
                    while($row = mysqli_fetch_assoc($result)){
                        $resultSet[] = $row;
                    }
                    if(empty($resultSet)){
                        echo 'Something went wrong';
                    }else{
                        $itemArray = array($resultSet[0]['product_id']=>
                            array('name'=> $resultSet[0]['product_name'],
                            'product_id' => $resultSet[0]['product_id'],
                            'quantity'  => $_POST['quantity'],
                            'price' => $resultSet[0]['price'],
                            'image' => $resultSet[0]['product_img']));
                    }

                    if(!empty($_SESSION['cart_item'])){
                        if(in_array($resultSet[0]['product_id'], array_keys($_SESSION['cart_item']))){
                            foreach($_SESSION['cart_item'] as $k => $v){
                                if($resultSet[0]['product_id'] == $k){
                                    if(empty($_SESSION['cart_item'][$k]['quantity'])){
                                        $_SESSION['cart_item'][$k]['quantity'] = 0;
                                    }
                                    $_SESSION['cart_item'][$k]['quantity'] += $_POST['quantity'];
                                }
                            }
                        }else{
                            $_SESSION['cart_item'] = array_merge($_SESSION['cart_item'], $itemArray);
                        }
                    }else{
                        $_SESSION['cart_item'] = $itemArray;
                    }
                }
                
            break;

        }
    }
?>
    <div class="container">
        <div class="row">
    <?php
        //access products from database and display them
        $products = array();
        $sql = "SELECT * FROM product";
        $result = mysqli_query($conn, $sql);

        if($result){
            if(mysqli_num_rows($result) == 0){
                echo "There is nothing to display, something went wrong please check back later";
            }else{
                while($row = mysqli_fetch_assoc($result)){
                    $products[] = $row;
                }
                //get products
                foreach($products as $p){
                    $product_id = $p['product_id'];
                    $name = $p['product_name'];
                    $description = $p['product_description'];
                    $product_price = $p['price'];
                    $img = $p['product_img'];
                    ?>
                    <div class="col-lg-3 column productbox">
                        <form method="post" action="index.php?action=add&product_id=<?php echo $product_id;?>">
                        <img src="<?php echo $img;?>" class="w-100 p-3">
                        <div class="producttitle"><?php echo $name;?></div>
                        <div class="productprice">
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
                            <div class="pricetext"><?php echo $product_price;?></div>
                        </div>
                        </form>
                    </div>
                <?php
                }
            }
        }else{
            echo "error";
        }
    ?>
        </div>
    </div>
</body>
<?php   
    //Function to clean user input
    function test_input($data){
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    mysqli_close($conn);
    include 'footer.php';
?>