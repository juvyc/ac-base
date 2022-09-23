<div class="container">
	<form method="post">
		<?php echo $_form_fields; ?>
	</form>
	
	<br/>
	<br/>
	<h2>List of Customers</h2>
	<table class="table">
		<tr>
			<th>Customer Name</th>
			<th>Customer Address</th>
			<th>Phone</th>
			<th>...</th>
		</tr>
		
		<?php foreach($customers_lists as $_row){ ?>
		<tr>
			<td><?php echo $_row->customer_name; ?></td>
			<td><?php echo $_row->address; ?></td>
			<td><?php echo $_row->phone; ?></td>
			<td><a href="?cid=<?php echo $_row->id; ?>">Edit</a></td>
		</tr>
		<?php } ?>
	</table>
</div>