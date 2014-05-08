		<ul class="nav nav-tabs profileTabs">
			<li><a href="/user/settings">Profile</a></li>
			<li><a href="/user/settings/image">Image</a></li>
			<li class="active"><a href="/user/settings/password">Password</a></li>
		</ul>

		<?php 
		    echo validation_errors(); 
		    if($success == true)
		    {
		    	echo '<div class="alert alert-success">Password updated!</div>';
		    }
		    else if($errorMessage != '')
		    {
		        echo '<div class="alert alert-danger">' . $errorMessage . '</div>';
		    }
		?>

		<div class="alert alert-warning">
			Please use a unique, strong password for this site. We recommend <a href="https://lastpass.com/" target="_blank">LastPass</a>.
		</div>

		<?php echo form_open('user/settings/password'); ?>
			<div class="form-group">
				<label for="passwordInput">Old Password</label>
				<input type="password" class="form-control" maxlength="50" name="oldPassword" id="oldPassword" placeholder="Old Password">
			</div>
			<div class="form-group">
				<label for="passwordInput">New Password</label>
				<input type="password" class="form-control" maxlength="50" name="newPassword" id="newPassword" placeholder="New Password">
			</div>
			<div class="form-group">
				<label for="conpasswordInput">Confirm New Password</label>
				<input type="password" class="form-control" maxlength="50" name="confirmNewPassword" id="confirmNewPassword" placeholder="Confirm New Password">
			</div>
			<button type="submit" name="register" class="btn btn-default">Submit</button>
		</form>
	</div>
</div>