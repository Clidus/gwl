<ul class="breadcrumb">
    <li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb"><span itemprop="title"><a href="<?php echo $baseUrl ?>">Home</a></span></li>       
    <li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb" class="active"><span itemprop="title"><?php echo $user->Username ?></span></li>
</ul>

<div class="row">
    <div class="col-sm-8">
		<h2><?php echo $user->Username ?></h2>
	</div>
	<div class="col-sm-4">
		<?php 
			if($sessionUserID != null && $sessionUserID == $user->UserID) 
			{
				echo "<a href='" . $baseUrl . "user/edit'>Edit Profile</a>";
			} 
		?>
	</div>
</div>