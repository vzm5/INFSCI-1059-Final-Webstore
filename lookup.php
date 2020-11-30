<?php
    include 'header.php';
    include 'connection.php';

    if($_SERVER['REQUEST_METHOD'] == 'POST'){
        echo '<div class="container">
            <div class="row">
                    <h3>Order Status Lookup</h3>
            </div>';
        $orderid = $_POST['orderid'];
        $sql = "SELECT * FROM orders WHERE '$orderid' = order_id";

        $result = mysqli_query($conn, $sql);

        if($result){
            if(mysqli_num_rows($result) == 0){
                echo "There is nothing to display, something went wrong please check back later or try another order id.";
            }else{
                while($row = mysqli_fetch_assoc($result)){
                    echo '<div class="row"> <h4>Your order is ' . $row['shipping_status'] . '</h4></div>
                        <div class="row"><a href="lookup.php">Look up another order</a></div>
                    </div>';
                }
            }
        }
    }else{
        echo '<div class="container">
        <div class="row">
            <h3>Order Status Lookup</h3>
        </div>
        <div class="row">
            <form action="' . htmlspecialchars($_SERVER["PHP_SELF"]) . '" method="post">
                <div class="form-group">
                    <label for="orderid">Order ID:</label>
                    <input type="number" class="form-control" id="orderid" name="orderid">
                </div>
                <button type="submit" id="submit" name="submit" class="btn btn-primary">Submit</button>
            </form>
        </div>
    </div>';

    }
?>
    

<?php
    include 'footer.php';
?>