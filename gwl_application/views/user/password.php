<ul class="breadcrumb">
    <li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb"><span itemprop="title"><a href="<?php echo $baseUrl ?>">Home</a></span></li>       
    <li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb"><span itemprop="title"><a href="<?php echo $baseUrl . "user/" . $sessionUserID ?>"><?php echo $sessionUsername ?></a></span></li>
    <li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb" class="active"><span itemprop="title">Settings</span></li>
</ul>

<h2><?php echo $user->Username ?></h2>

<div class="row">
	<div class="col-sm-4">
		<?php 
			if($user->ProfileImage != null) 
			{
				echo "<img src='" . $baseUrl . '/uploads/' . $user->ProfileImage . "' class='largeProfileImage gameBoxArt' />";
			}
			if($user->Bio != null) 
			{
				echo "<div class='userBio'>" . $user->Bio . "</div>";
			}
		?>
		<ul class="nav nav-pills nav-stacked profileNav">
			<li><a href="<?php echo $baseUrl . "user/" . $sessionUserID ?>">Feed</a></li>
			<li><a href="">Collection</a></li>
			<li class="active"><a href="<?php echo $baseUrl ?>user/settings">Settings</a></li>
		</ul>
	</div>
	<div class="col-sm-8"> 
		<ul class="nav nav-tabs profileTabs">
			<li><a href="<?php echo $baseUrl ?>user/settings">Profile</a></li>
			<li><a href="<?php echo $baseUrl ?>user/settings/image">Image</a></li>
			<li class="active"><a href="<?php echo $baseUrl ?>user/settings/password">Password</a></li>
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