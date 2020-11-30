
<?php
    include 'header.php';
    include 'connection.php';

    if(!empty($_GET['action'])){
        switch($_GET['action']){
                case 'remove':
                    if(!empty($_SESSION['cart_item'])) {
                        foreach($_SESSION['cart_item'] as $k => $v) {
                                if($_GET['product_id'] == $_SESSION['cart_item'][$k]['product_id'])
                                    unset($_SESSION['cart_item'][$k]);				
                                if(empty($_SESSION['cart_item']))
                                    unset($_SESSION['cart_item']);
                        }
                    }
                break;

                case 'empty':
                    $_SESSION = array(); //unset all session variables
                break;
        }
    }
    

    //check to see that the cart isn't empty; show cart if not-empty


    if(isset($_SESSION['cart_item'])){
        $total_quantity = 0;
        $total_price = 0;
    ?>

        <table class="tbl-cart" cellpadding="10" cellspacing="1">
        <tbody>
        <tr>
        <th style="text-align:left;">Item</th>
        <th style="text-align:right;" width="5%">Quantity</th>
        <th style="text-align:right;" width="10%">Unit Price</th>
        <th style="text-align:right;" width="10%">Price</th>
        <th style="text-align:center;" width="5%">Remove</th>
        </tr>

    <?php
        foreach($_SESSION['cart_item'] as $item){
            $item_price = $item['quantity']*$item['price'];
            ?>
            <tr>
				<td><img src="<?php echo $item['image']; ?>" class="img-thumbnail checkout" /><?php echo $item['name']; ?></td>
				<td style="text-align:right;"><?php echo $item['quantity']; ?></td>
				<td  style="text-align:right;"><?php echo "$ ".$item['price']; ?></td>
				<td  style="text-align:right;"><?php echo "$ ". number_format($item_price,2); ?></td>
				<td style="text-align:center;"><a href="viewcart.php?action=remove&product_id=<?php echo $item['product_id']; ?>" class="btnRemoveAction">
                        <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-x-circle" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M8 15A7 7 0 1 0 8 1a7 7 0 0 0 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                        <path fill-rule="evenodd" d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                        </svg>
                </a></td>
				</tr>
				<?php
				$total_quantity += $item["quantity"];
				$total_price += ($item["price"]*$item["quantity"]);
        }
        $_SESSION['cart_total_items'] = $total_quantity;
        $_SESSION['cart_total_price'] = $total_price;
    
	?>
        <tr>
            <td colspan="2" align="right">Total:</td>
            <td align="right"><?php echo $total_quantity; ?></td>
            <td align="right" colspan="2"><strong><?php echo "$ ".number_format($total_price, 2); ?></strong></td>
            <td></td>
        </tr>
        <tr>
            <td>
                <a href="viewcart.php?action=empty">Empty Cart</a>
            <td>
            <td align="right"> 
                <a class="btn btn-primary" href="checkout.php">Checkout</a>
            <td>
        </tr>
</tbody>
</table>

<?php
    }else{
        echo '<br><h4 class="text-center">There\'s nothing in your cart, <a href="index.php"> Continue Shopping!</a></h4>';
    }
    include 'footer.php';
?>