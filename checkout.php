<!--adapted from getbootstrap.com/docs/4.0/examples/checkout/-->
<?php
    include 'header.php';
    include 'connection.php';
    
?>
<!-- Form Validation-->
<?php
    if($_SERVER['REQUEST_METHOD'] == 'POST'){
        $country = $_POST['country'];
        $state = $_POST['state'];
        $namePattern = '/^[a-zA-Z\'\s-]+$/';
        $emailPattern = '/([\w\-]+\@[\w\-]+\.[\w\-]+)/';
        $addressPattern = "/^\d+\s[A-z]+\s[A-z]+$/";
        $zipPattern = "/^[0-9]{5}$/";
        $errors  = array();
        $errmsg  = '';

        //Sanitize User Inputs
        $first_name_clean = test_input($_POST['firstName']);
        $last_name_clean = test_input($_POST['lastName']);
        $email_clean = test_input($_POST['email']);
        $address_clean = test_input($_POST['address']);
        $city_clean = test_input($_POST['city']);
        $zip_clean = test_input($_POST['zip']);
        
        //Validate
        //FIRST NAME
        if(!preg_match($namePattern, $first_name_clean)){
            $errors[] = 'First name is empty or invalid, please enter your name.';
        }
        if(strlen($first_name_clean)>30){
            $errors[] = 'First name must be under 30 characters.';
        } 
        //LASTNAME
        if(!preg_match($namePattern, $last_name_clean)){
            $errors[] = 'Last name is empty or invalid, please enter your name.';
        }
        if(strlen($last_name_clean)>30){
            $errors[] = 'Last name must be under 30 characters.';
        }
        //EMAIL
        if(!preg_match($emailPattern, $email_clean)){
            $errors[] = 'Email must be valid.';
        }
        //ADDRESS
        if(!preg_match($addressPattern, $address_clean)){
            $errors[] = 'Street address must be valid.';
        }
        //CITY
        if(!preg_match($namePattern, $city_clean)){
            $errors[] = 'City must be valid.';
        }
        //ZIP
        if(!preg_match($zipPattern, $zip_clean)){
            $errors[] = 'Zip code must be valid.';
        }
        
        
        if (sizeof($errors) == 0) {
            // you can process your for here and redirect or show a success message
            $first_name_final = mysqli_real_escape_string($conn, $first_name_clean);
            $last_name_final = mysqli_real_escape_string($conn, $last_name_clean);
            $email_final = mysqli_real_escape_string($conn, $email_clean);
            $address_final = mysqli_real_escape_string($conn, $address_clean);
            $city_final = mysqli_real_escape_string($conn, $city_clean);
            $zip_final = mysqli_real_escape_string($conn, $zip_clean);
            $state_final = mysqli_real_escape_string($conn, $_POST['state']);

            $sql = "INSERT INTO final_customer(first_name, last_name, address, city, state, zip, user_email) VALUES ('$first_name_final', '$last_name_final', '$address_final', '$city_final', '$state_final', '$zip_final', '$email_final')";
            
            if ($conn->query($sql) === TRUE) {
                echo "Thank you, your order is one the way!";
                //since the user's info went into the database, put their info into the customer_orders table
                $newuserid = mysqli_insert_id($conn);
                $total_items = $_SESSION['cart_total_items'];
                $total_price = $_SESSION['cart_total_price'];
                echo $total_price;
                $sql_order = "INSERT INTO orders(total, quantity, shipping_status, customer_id) VALUES ('$total_price', '$total_items', 'Processing', '$newuserid')";

                if($conn->query($sql_order) === TRUE){
                    echo "ORDER PLACED";
                    $neworderid = mysqli_insert_id($conn);
                    $_SESSION['customer_order_id'] = $neworderid;
                    //put order details into DB
                    if(!empty($_SESSION['cart_item'])){
                        foreach($_SESSION['cart_item'] as $k => $v) {
                                $cartitem = $_SESSION['cart_item'][$k]['product_id'];
                                $qty = $_SESSION['cart_item'][$k]['quantity'];
                                //handle the instance that a customer bought more than 1 of the same item
                                
                                $sql_process = "INSERT INTO order_has_product(order_id, fk_product_id, total_qty) VALUES ('$neworderid', '$cartitem', '$qty')";
                                
                                if(!$conn->query($sql_process) === TRUE){
                                    echo 'Something went wrong';
                                }
                                
                        }
                        //CLEAR SESSION
                        header('Location: orderplaced.php'); 
                    }else{
                        echo 'An error occurred';
                    }


                    
                }else{
                    echo "ORDER FAILED";
                    echo "Error: " . $sql . "<br>" . $conn->error;
                }
                
            //Conn to DB dfailed
            } else {
                echo "Something went wrong";
                echo "Error: " . $sql . "<br>" . $conn->error;

            }

        } else {
            // one or more errors
            foreach($errors as $error) {
                $errmsg .= $error . '<br />';
            }
        }

    }

    //Function to clean user input
    function test_input($data){
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }









?>

<!--Check to see if cart is empty: display checkout if not empty -->
<?php
    if(empty($_SESSION['cart_item'])){
        echo '<br><div class="container"><h3>Your cart is empty! <a href="index.php">Continue Shopping</h3></a></div>';
    }else{
        ?><!--Close PHP tag and display checkout-->
        <body style="min-height:8000px;">
        <br>
        <h3 class="text-center">Checkout</h3>    
            <div class="container">
                <!--one half shows items in cart-->
                <div class="row">
                    <div class="col-md-4 order-md-2 mb-4">
                        <h4 class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-muted">Your Cart</span>
                            <span class="badge badge-secondary badge-pill">
                                <?php
                                    /*
                                    foreach($_SESSION['cart_item'] as $item){
                                        $item_qty_total += $item['quantity'];
                                    }
                                    echo $item_qty_total;
                                    */
                                    echo $_SESSION['cart_total_items'];
                                    
                                ?>
                            </span>
                        </h4>
                        <!--For each cart item, display it-->
                        <ul class="list-group mb-3">
                            <?php foreach($_SESSION['cart_item'] as $item){ 
                                $item_price = $item['quantity']*$item['price'];?>
                                <li class="list-group-item d-flex justify-content-between lh-condensed">
                                    <div>
                                        <h6 class="my-0"><?php echo $item['name'];?></h6>
                                        <small class="text-muted">QTY: <?php echo $item['quantity'];?></small>
                                    </div>
                                    <span class="text-muted"><?php echo $item_price;?></span>
                                </li>
                            
                            <?php
                            $total_price += ($item["price"]*$item["quantity"]);
                            }
                            ?>
                            <li class="list-group d-flex justify-content-between">
                                <span>Total (USD)</span>
                                <strong><?php echo $total_price;?></strong>
                            </li>
                        </ul>
                    </div>
            
                    <!--show CHECKOUT form-->
                    <div class="col-md-8 order-md-1">
                        <h4 class="mb-3">Shipping Address</h4>
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
                            <?php if ($errmsg != ''): ?>
                                <p style="color: red;"><b>Please correct the following errors:</b><br />
                            <?php echo $errmsg; ?>
                                </p>
                            <?php endif; ?>

                            <div class="row g-3">
                                <div class="col-sm-6">
                                    <label for="firstName" class="form-label">First Name</label>
                                    <input id="firstName" name="firstName" type="text" class="form-control" placeholder="John" value="<?php echo htmlspecialchars($_POST['firstName']);?>" required>
                                </div>
                                <div class="col-sm-6">
                                    <label for="lastName" class="form-label">Last Name</label>
                                    <input id="lastName" name="lastName" type="text" class="form-control" placeholder="Smith" value="<?php echo htmlspecialchars($_POST['lastName']);?>" required>
                                </div>
                                <div class="col-12">
                                    <label for="email" class="form-label">Email</label>
                                    <div class="input-group">
                                        <input id="email" name="email"  type="email" class="form-control" placeholder="jsmith@simpleshop.com" value="<?php echo htmlspecialchars($_POST['email']);?>" required>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label for="address" class="form-label">Street Address</label>
                                    <div class="input-group">
                                        <input id="address" name="address" type="text" class="form-control" placeholder="1234 Main Street" value="<?php echo htmlspecialchars($_POST['address']);?>" required>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label for="city" class="form-label">City</label>
                                    <div class="input-group">
                                        <input id="city" name="city" type="text" class="form-control" placeholder="Pittsburgh" value="<?php echo htmlspecialchars($_POST['city']);?>" required>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <label for="country" class="form-label">Country</label>
                                    <select id="country" name="country" class="form-control" required>
                                        <option value="">Choose...</option>
                                        <option value="USA" <?php if($_POST['country'] == "USA"){echo 'selected="selected"';}?>>USA</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="state" class="form-label">State</label>
                                    <select id="state" name="state" class="form-control" required>
                                        <option value="">Choose...</option> 
                                        <option value="AL" <?php if($_POST['state'] == "AL"){echo 'selected="selected"';}?>>Alabama</option>
                                        <option value="AK" <?php if($_POST['state'] == "AK"){echo 'selected="selected"';}?>>Alaska</option>
                                        <option value="AZ" <?php if($_POST['state'] == "AZ"){echo 'selected="selected"';}?>>Arizona</option>
                                        <option value="AR" <?php if($_POST['state'] == "AR"){echo 'selected="selected"';}?>>Arkansas</option>
                                        <option value="CA" <?php if($_POST['state'] == "CA"){echo 'selected="selected"';}?>>California</option>
                                        <option value="CO" <?php if($_POST['state'] == "CO"){echo 'selected="selected"';}?>>Colorado</option>
                                        <option value="CT" <?php if($_POST['state'] == "CT"){echo 'selected="selected"';}?>>Connecticut</option>
                                        <option value="DE" <?php if($_POST['state'] == "DE"){echo 'selected="selected"';}?>>Delaware</option>
                                        <option value="DC" <?php if($_POST['state'] == "DC"){echo 'selected="selected"';}?>>District Of Columbia</option>
                                        <option value="FL" <?php if($_POST['state'] == "FL"){echo 'selected="selected"';}?>>Florida</option>
                                        <option value="GA" <?php if($_POST['state'] == "GA"){echo 'selected="selected"';}?>>Georgia</option>
                                        <option value="HI" <?php if($_POST['state'] == "HI"){echo 'selected="selected"';}?>>Hawaii</option>
                                        <option value="ID" <?php if($_POST['state'] == "ID"){echo 'selected="selected"';}?>>Idaho</option>
                                        <option value="IL" <?php if($_POST['state'] == "IL"){echo 'selected="selected"';}?>>Illinois</option>
                                        <option value="IN" <?php if($_POST['state'] == "IN"){echo 'selected="selected"';}?>>Indiana</option>
                                        <option value="IA" <?php if($_POST['state'] == "IA"){echo 'selected="selected"';}?>>Iowa</option>
                                        <option value="KS" <?php if($_POST['state'] == "KS"){echo 'selected="selected"';}?>>Kansas</option>
                                        <option value="KY" <?php if($_POST['state'] == "KY"){echo 'selected="selected"';}?>>Kentucky</option>
                                        <option value="LA" <?php if($_POST['state'] == "LA"){echo 'selected="selected"';}?>>Louisiana</option>
                                        <option value="ME" <?php if($_POST['state'] == "ME"){echo 'selected="selected"';}?>>Maine</option>
                                        <option value="MD" <?php if($_POST['state'] == "MD"){echo 'selected="selected"';}?>>Maryland</option>
                                        <option value="MA" <?php if($_POST['state'] == "MA"){echo 'selected="selected"';}?>>Massachusetts</option>
                                        <option value="MI" <?php if($_POST['state'] == "MI"){echo 'selected="selected"';}?>>Michigan</option>
                                        <option value="MN" <?php if($_POST['state'] == "MN"){echo 'selected="selected"';}?>>Minnesota</option>
                                        <option value="MS" <?php if($_POST['state'] == "MS"){echo 'selected="selected"';}?>>Mississippi</option>
                                        <option value="MO" <?php if($_POST['state'] == "MO"){echo 'selected="selected"';}?>>Missouri</option>
                                        <option value="MT" <?php if($_POST['state'] == "MT"){echo 'selected="selected"';}?>>Montana</option>
                                        <option value="NE" <?php if($_POST['state'] == "NE"){echo 'selected="selected"';}?>>Nebraska</option>
                                        <option value="NV" <?php if($_POST['state'] == "NV"){echo 'selected="selected"';}?>>Nevada</option>
                                        <option value="NH" <?php if($_POST['state'] == "NH"){echo 'selected="selected"';}?>>New Hampshire</option>
                                        <option value="NJ" <?php if($_POST['state'] == "NJ"){echo 'selected="selected"';}?>>New Jersey</option>
                                        <option value="NM" <?php if($_POST['state'] == "NM"){echo 'selected="selected"';}?>>New Mexico</option>
                                        <option value="NY" <?php if($_POST['state'] == "NY"){echo 'selected="selected"';}?>>New York</option>
                                        <option value="NC" <?php if($_POST['state'] == "NC"){echo 'selected="selected"';}?>>North Carolina</option>
                                        <option value="ND" <?php if($_POST['state'] == "ND"){echo 'selected="selected"';}?>>North Dakota</option>
                                        <option value="OH" <?php if($_POST['state'] == "OH"){echo 'selected="selected"';}?>>Ohio</option>
                                        <option value="OK" <?php if($_POST['state'] == "OK"){echo 'selected="selected"';}?>>Oklahoma</option>
                                        <option value="OR" <?php if($_POST['state'] == "OR"){echo 'selected="selected"';}?>>Oregon</option>
                                        <option value="PA" <?php if($_POST['state'] == "PA"){echo 'selected="selected"';}?>>Pennsylvania</option>
                                        <option value="RI" <?php if($_POST['state'] == "RI"){echo 'selected="selected"';}?>>Rhode Island</option>
                                        <option value="SC" <?php if($_POST['state'] == "SC"){echo 'selected="selected"';}?>>South Carolina</option>
                                        <option value="SD" <?php if($_POST['state'] == "SD"){echo 'selected="selected"';}?>>South Dakota</option>
                                        <option value="TN"<?php if($_POST['state'] == "TN"){echo 'selected="selected"';}?>> Tennessee</option>
                                        <option value="TX" <?php if($_POST['state'] == "TX"){echo 'selected="selected"';}?>>Texas</option>
                                        <option value="UT" <?php if($_POST['state'] == "UT"){echo 'selected="selected"';}?>>Utah</option>
                                        <option value="VT" <?php if($_POST['state'] == "VT"){echo 'selected="selected"';}?>>Vermont</option>
                                        <option value="VA" <?php if($_POST['state'] == "VA"){echo 'selected="selected"';}?>>Virginia</option>
                                        <option value="WA" <?php if($_POST['state'] == "WA"){echo 'selected="selected"';}?>>Washington</option>
                                        <option value="WV" <?php if($_POST['state'] == "WV"){echo 'selected="selected"';}?>>West Virginia</option>
                                        <option value="WI" <?php if($_POST['state'] == "WI"){echo 'selected="selected"';}?>>Wisconsin</option>
                                        <option value="WY" <?php if($_POST['state'] == "WY"){echo 'selected="selected"';}?>>Wyoming</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="zip" class="form-label">Zip Code</label>
                                    <input id="zip" name="zip" type="text" class="form-control" placeholder="12345" value="<?php echo htmlspecialchars($_POST['zip']);?>" required>
                                    <div class="invalid-feedback">Valid Zip code is required</div>
                                </div>
                            </div>
                
                                <hr class="my-4">
                                <h4 class="mb-3">Payment</h4>
                                <div class="form-check">
                                    <input type="radio" id="paypal" name="paymentMethod" class="form-check-input" checked required>
                                    <label for="paypal">PayPal</label>
                                </div>
                                <hr class="my-4">
                                <button class="btn btn-primary btn-lg btn-block" name="mybutton" type="submit" id="submit">Confirm</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </body>

<!--Close else statement that checks if cart is empty or not-->
<?php
    }
?>

<?php 
    include 'footer.php';
?>