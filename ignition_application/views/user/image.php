		<ul class="nav nav-tabs profileTabs">
			<li><a href="/user/settings">Profile</a></li>
			<li class="active"><a href="/user/settings/image">Image</a></li>
			<li><a href="/user/settings/password">Password</a></li>
		</ul>

		<?php 
		    if($errorMessage != '')
		    {
		        echo '<div class="alert alert-danger">' . $errorMessage . '</div>';
		    } 
		?>

		<div class="alert alert-info">
			Please make all images 2MB or less, 1000x1000 or less and a GIF, JPG or PNG. Thanks!
		</div>

		<?php echo form_open_multipart('user/settings/image/upload'); ?>
			<div class="form-group">
	            <label for="bio">Profile Image</label>
	            <input type="file" name="userfile" size="20" />
	        </div>
			<button type="submit" name="register" class="btn btn-default">Submit</button>
		</form>
	</div>
</div>