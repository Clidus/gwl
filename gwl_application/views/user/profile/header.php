<ul class="breadcrumb">
    <li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb"><span itemprop="title"><a href="<?php echo $baseUrl ?>">Home</a></span></li>       
    <li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb" class="active"><span itemprop="title"><?php echo $user->Username ?></span></li>
</ul>

<h2><?php echo $user->Username ?></h2>

<div class="row">
	<div class="col-sm-4">
		<img src="<?php echo $baseUrl . '/uploads/' . $user->ProfileImage ?>" class="largeProfileImage gameBoxArt" />
		<ul class="nav nav-pills nav-stacked profileNav">
			<li id="navFeed"><a href="<?php echo $baseUrl . "user/" . $user->UserID ?>">Feed</a></li>
			<li id="navCollection"><a href="<?php echo $baseUrl . "user/" . $user->UserID ?>/collection">Collection</a></li>
			<?php 
				if($sessionUserID != null && $sessionUserID == $user->UserID) 
				{
					echo "<li id='navSettings'><a href='" . $baseUrl . "user/settings'>Settings</a></li>";
				} 
			?>
		</ul>
		<?php
			if($user->Bio != null) 
			{
				echo "<div class='userBio'>" . $user->Bio . "</div>";
			}
			if($currentlyPlaying != null)
			{
				echo "<p><b>Currently Playing:</b></p>";
				foreach($currentlyPlaying as $currentlyPlayingGame)
				{
					echo '
						<div class="currentlyPlaying clearfix">
							<div class="pull-left">
								<a href="' . $baseUrl . 'game/' . $currentlyPlayingGame->GBID . '">
									<img src="' . $currentlyPlayingGame->ImageSmall . '" class="tinyIconImage gameBoxArt" />
								</a>
							</div>
							<div class="media-body currentlyPlayingTitle">
								<a href="' . $baseUrl . 'game/' . $currentlyPlayingGame->GBID . '">' . $currentlyPlayingGame->Name . '</a>
							</div>
						</div>';
				}
			}
		?>
	</div>
	<div class="col-sm-8"> 