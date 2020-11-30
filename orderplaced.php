<?php
    include 'header.php';
    include 'connection.php';
    
    
?>
    <div class="container justify-content-center">
        <div class="row">
            <h1>Thank you for your order (#<?php echo $_SESSION['customer_order_id'];?>)</h1>;
        </div>
        <div class="row">
            <h2>Please keep a copy of your order # to look up shipping status and assist with any customer service questions.</h2>
        </div>
    </div>

    <?php
        $_SESSION = array();
        include 'footer.php';
    ?>