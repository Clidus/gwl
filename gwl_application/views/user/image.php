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
		<ul class="nav nav-pills nav-stacked">
			<li><a href="<?php echo $baseUrl . "user/" . $sessionUserID ?>">Feed</a></li>
			<li><a href="">Collection</a></li>
			<li class="active"><a href="<?php echo $baseUrl ?>user/settings">Settings</a></li>
		</ul>
	</div>
	<div class="col-sm-8"> 
		<ul class="nav nav-tabs profileTabs">
			<li><a href="<?php echo $baseUrl ?>user/settings">Profile</a></li>
			<li class="active"><a href="<?php echo $baseUrl ?>user/settings/image">Image</a></li>
			<li><a href="<?php echo $baseUrl ?>user/settings/password">Password</a></li>
		</ul>

		<?php 
		    if($errorMessage != '')
		    {
		        echo '<div class="alert alert-danger">' . $errorMessage . '</div>';
		    } 
		?>

		<div class="alert alert-info">
			Please make all images 100MB or less, 1000x1000 or less and a GIF, JPG or PNG. Thanks!
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