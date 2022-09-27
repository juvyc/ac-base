<div class="container">
	<form method="post">
		<?php echo $_form_fields; ?>
	</form>
	
	<br/>
	<br/>
	<h2>List of Lead</h2>
	<table class="table">
	
		
		<?php while($_row = $_data_results->fetch_object()){ ?>
		<tr>
			<td><?php echo $_row->type; ?></td>
			<td><?php echo $_row->first_name; ?></td>
			<td><?php echo $_row->last_name; ?></td>
			<td><?php echo $_row->email; ?></td>
			<td><a href="?cid=<?php echo $_row->id; ?>">Edit</a></td>
		</tr>
		<?php } ?>
	</table>
</div>