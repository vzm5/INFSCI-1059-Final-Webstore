<?php   
    include 'connection.php';
    include 'headerAdmin.php';
    session_start();

    //Get the order id from URL and escape it
    $order_id = mysqli_real_escape_string($conn, $_GET['id']);

    //$fname = $lname = $address = $state = $zip = "";

    //$sql = "SELECT * FROM final_customer, orders WHERE final_customer.customer_id = '$order_id'";
    $sql = "SELECT * FROM orders LEFT JOIN final_customer ON orders.customer_id = final_customer.customer_id WHERE orders.order_id = '$order_id'";
    
    
    $result = mysqli_query($conn, $sql);
                    
    if($result){
        if(mysqli_num_rows($result) == 0){
            echo 'There\'s not any customer information to retrieve.';
        }else{
            $userdetails = mysqli_fetch_assoc($result);
            $fname = $userdetails['first_name'];
            $lname = $userdetails['last_name'];
            $address = $userdetails['address'];
            $city = $userdetails['city'];
            $state = $userdetails['state'];
            $zip = $userdetails['zip'];
            $total = $userdetails['total'];
            $status = $userdetails['shipping_status'];
        }
    }else{
        echo "something went wrong";
    }

    echo '<br><div class="container">
            <div class="row">
                <div class="col">
                    <br><h3> Order #'. $order_id . '</h3><br>';
    ?>
                </div>
                <div class="col">
                        <div class="container" style="background-color: #f8f9fa; border: 1px;">
                            <!--Row to hold first and last name-->
                            <div class="row">
                                <div class="col">
                                    <?php echo $fname; ?>
                                    <?php echo $lname; ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <?php echo $address; ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <?php echo $city; ?>
                                    <?php echo $state; ?>
                                    <?php echo $zip; ?>
                                </div>
                            </div>
                        </div>
                </div>
            </div>
        </div>
    <?php
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $status = $_POST['status'];
            //UPDATE `vzm`.`orders` SET `shipping_status` = 'Processing' WHERE (`order_id` = '44');
            $status_update_sql = "UPDATE orders SET shipping_status = '$status' WHERE (order_id = '$order_id')";

            if(!$conn->query($status_update_sql) === TRUE){
                echo 'Something went wrong while updating shipping status';
            }
        }

    ?>
    
    <div class="container">
        <form action="<?php echo "order.php?id=" . $order_id ?>" method="post">
            <div class="row">
                <div class="col-auto">
                    <select class="form-control" name="status" id="status">
                        <option value="Processing" <?php if($status == "Processing"){echo 'selected="selected"';}?>>Processing</option>
                        <option value="Ready to Ship" <?php if($status == "Ready to Ship"){echo 'selected="selected"';}?>>Ready to Ship</option>
                        <option value="Shipped" <?php if($status == "Shipped"){echo 'selected="selected"';}?>>Shipped</option>
                    </select>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary mb-2">Update Shipping Status</button>
                </div>
            </div>
        </form>
    </div>

<!--Container to hold order information-->
<?php
                $getitems = "SELECT * FROM order_has_product, product WHERE order_has_product.fk_product_id = product.product_id AND order_has_product.order_id = '$order_id'";

                $result2 = mysqli_query($conn, $getitems);

                if($result2){
                    if(mysqli_num_rows($result2) === 0){
                        echo 'No order items to display';
                    }else{
                        echo '<div class="container" style="background-color: #f8f9fa; border: 1px;">
                                <table class="tbl-cart" cellpadding="10" cellspacing="1">
                                    <tbody>
                                        <tr>
                                            <th style="text-align:left;">Item</th>
                                            <th style="text-align:right;" width="5%">Quantity</th>
                                            <th style="text-align:right;" width="10%">Unit Price</th>
                                            <th style="text-align:right;" width="10%">Price</th>
                                        </tr>';
                        while($row = mysqli_fetch_assoc($result2)){
                            echo '<tr>';
                            echo '<td>' . $row['product_name'] . '</td>
                                  <td>' . $row['total_qty'] . '</td>
                                  <td>' . $row['price']     . '</td>
                                  <td>'. $row['price'] * $row['total_qty'] . '</td>
                                  </tr>';
                                  
                        }
                        
                    }
                    
                }else{
                    echo 'Something went wrong displaying products.';
                }

?>


            
        </tbody>
    </table>
    <div class="container" style="background-color: #f8f9fa; border: 1px;">
        <div class="row  float-right ">
            <div class="col font-weight-bold">
                <?php echo 'Order Total: ' . $total; ?>
            </div>
        </div>
    </div>
</div>
    <br>
