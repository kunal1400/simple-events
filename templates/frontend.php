<form method='post' class='simpleEventAjaxForm'>
	<div class="successMsg" style="color:green;"></div>
	<input type='hidden' name='action' value='submit_simple_event'>
	<input type='hidden' name='postId' value='<?php echo $a['id'] ?>'>
	<table>
		<tr>
			<td><input type='text' class='dmp-event-name' name='userName' placeholder='Your Name' required></td>
		</tr>
		<tr>
			<td><input type='email' class='dmp-event-email' name='userEmail' placeholder='Your email address' required></td>
		</tr>
		<tr>
			<td><input type='text' class='dmp-event-company' name='userCompany' placeholder='Company'></td>
		</tr>
		<tr>
			<td><textarea name='userDescription' class='dmp-event-comments' placeholder='Useful information, food allergy, special needs etc'></textarea></td>
		</tr>
		<tr>
			<td><input type='checkbox' class='dmp-event-confirm-terms' required>Terms accepted</td>
		</tr>
		<tr>
			<td><button type='submit' class='dmp-event-submit'>Submit</button></td>
		</tr>
	</table>		
</form>