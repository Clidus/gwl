 		<ul class="nav nav-tabs profileTabs">
			<li class="active"><a href="/user/settings">Profile</a></li>
			<li><a href="/user/settings/image">Image</a></li>
			<li><a href="/user/settings/password">Password</a></li>
		</ul>

		<?php 
		    echo validation_errors(); 
		    if($errorMessage != '')
		    {
		        echo '<div class="alert alert-danger">' . $errorMessage . '</div>';
		    } 
		?>

		<?php echo form_open('user/settings'); ?>
			<div class="form-group">
				<label for="usernameInput">Username</label>
				<input type="text" class="form-control" maxlength="50" name="username" id="usernameInput" placeholder="Username" value="<?php echo $user->Username ?>">
			</div>
			<div class="form-group">
				<label for="emailInput">Email</label>
				<input type="email" class="form-control" maxlength="100" id="emailInput" name="email" placeholder="Email" value="<?php echo $user->Email ?>">
			</div>
			<div class="form-group">
	            <label for="dateFormat">Date Format</label>
	            <select class="form-control" id="dateFormat" name="dateFormat">
	                <option value="1">Swatch Internet Time</option>
	                <option value="2">Unix time</option>
	                <option value="3">Time since</option>
	                <option value="4">Database</option>
	                <option value="5">English</option>
	                <option value="6">American</option>
	            </select>
	        </div>
	        <div class="form-group">
	            <label for="bio">Bio</label>
	            <textarea id="bio" rows="5" placeholder="Say something about you..." class="form-control" name="bio"><?php echo $user->Bio ?></textarea>
	        </div>
			<button type="submit" name="register" class="btn btn-default">Submit</button>
		</form>
	</div>
</div>