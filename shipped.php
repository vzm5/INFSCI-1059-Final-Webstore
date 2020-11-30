<?php   
    include 'connection.php';
    include 'headerAdmin.php';
    session_start();

    echo '<div class="container"><br><h3>Shipped Orders:</h3><br>';

    //SQL query to get orders that need to be processed
    $sql = 'SELECT * FROM orders WHERE shipping_status = "Shipped"';
    $result = mysqli_query($conn, $sql);
                    
    //print all orders into rows
    if($result){
        if(mysqli_num_rows($result) == 0){
            echo 'There are no orders previously shipped.';
        }else{
            echo '<div class="col">
                    <table class="table table-striped" border="1">
                    <tr>
                        <th>Order ID</th>
                        <th>Order Status</th>
                        <th>Total</th>
                        <th>Quantity</th>
                    </tr>';
            while($row = mysqli_fetch_assoc($result)){
                echo '<tr>
                        <td><a href="order.php?id=' . $row['order_id'] . '">' . $row['order_id'] . '</a></td>
                        <td>' . $row['shipping_status'] . '</td>
                        <td> $' . $row['total'] . '</td>
                        <td>' . $row['quantity'] . '</td>
                    </tr>';
            }
            echo '</table></div>';
        }
    }else{
        echo 'Something went wrong';
    }
    
?>
</div>
