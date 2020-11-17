
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
                    unset($_SESSION['cart_item']);
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
				<td style="text-align:center;"><a href="viewcart.php?action=remove&product_id=<?php echo $item['product_id']; ?>" class="btnRemoveAction"><img src="icon-delete.png" alt="Remove Item" /></a></td>
				</tr>
				<?php
				$total_quantity += $item["quantity"];
				$total_price += ($item["price"]*$item["quantity"]);
        }
    }
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
        </tr>
        <tr>
            <td align="right"> 
                <a href="checkout.php">Checkout</a>
            <td>
        </tr>
</tbody>
</table>

<?php
    include 'footer.php';
?>