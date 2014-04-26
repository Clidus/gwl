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
			<li class="active"><a href="<?php echo $baseUrl ?>user/settings">Profile</a></li>
			<li><a href="<?php echo $baseUrl ?>user/settings/image">Image</a></li>
			<li><a href="<?php echo $baseUrl ?>user/settings/password">Password</a></li>
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