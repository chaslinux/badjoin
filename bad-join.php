case 'customers':
	// Quick display of customer list
	echo '<table width=836 align="center" frame="box">';
	echo '<tr><th colspan=6> Customer Report';
	echo '<tr><td><td><td>';
	echo '<th align="left"><a href="reports.php?action=customers&sort=customers_name">Customers Name</a>';
	echo '<th align="left"><a href="reports.php?action=customers&sort=customers_telephone">Phone</a>';
	echo '<th align="left"><a href="reports.php?action=customers&sort=customers_email_address">Email</a>';
	echo '<th align="left"><a href="reports.php?action=customers&sort=total">Orders Total</a>';
	$sql = 'SELECT c.customers_id,
				   CONCAT(c.customers_firstname," ",customers_lastname) AS customers_name,
				   c.customers_telephone,
				   c.customers_email_address,
				   (SELECT SUM(value) FROM orders_total 
			 	    WHERE (class="ot_total") AND orders_id IN 
						(SELECT orders_id FROM orders AS o 
				 		WHERE o.customers_id=c.customers_id)) AS total 
			FROM customers AS c order by total desc limit 100';
	if( isset( $sort )) {
		$sort_sql = ' ORDER BY ' . $sort;
		if( $_SESSION['reports']['sort'] == $sort_sql ) $_SESSION['reports']['sort'] .= ' DESC';
        else $_SESSION['reports']['sort'] = $sort_sql;
    } else unset( $_SESSION['reports']['sort'] );
	$sql .= $_SESSION['reports']['sort'];
	$result = mysql_query( $sql );
	while( $row = mysql_fetch_array( $result, MYSQL_ASSOC )) {
		echo '<tr><form action="customer.php" method="post"><td>';
		echo '<input type="hidden" name="action" value="select_edit_customer">';
		echo '<input type="hidden" name="customers_id" value="' . $row['customers_id'] . '">';
		echo '<input type="submit" value="Edit"></td></form>';
		echo '<form action="customer.php" method="post" onSubmit="return verify_delete(this)"><td>';
		echo '<input type="hidden" name="action" value="delete_customer">';
		echo '<input type="hidden" name="customers_id" value="' . $row['customers_id'] . '">';
		echo '<input type="submit" value="Delete"></td></form>';
		echo '<form action="orders.php" method="get"><td>';
		echo '<input type="hidden" name="action" value="customer_orders">';
		echo '<input type="hidden" name="customer" value="' . $row['customers_id'] . '">';
		echo '<input type="submit" value="Orders"></form>';
		echo '<td>' . $row['customers_name'];
		echo '<td>' . $row['customers_telephone'];
		if( !empty( $row['customers_email_address'] ) && ereg( ".*\@.*\..*", $row['customers_email_address']))
			echo '<td><a href="mailto:' . $row['customers_email_address'] . '">' . 
				$row['customers_email_address'] . '</a>';
		else echo '<td>';
		printf( '<td>$%.2f', $row['total'] );
	}
break;
