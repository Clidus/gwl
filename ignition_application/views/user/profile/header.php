<ul class="breadcrumb">
    <li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb"><span itemprop="title"><a href="/">Home</a></span></li>       
    <li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb" class="active"><span itemprop="title"><?php echo $user->Username ?></span></li>
</ul>

<h2><?php echo $user->Username ?></h2>

<div class="row">
	<div class="col-sm-4">
		<img src="/uploads/<?php echo $user->ProfileImage ?>" class="largeProfileImage gameBoxArt" />
		<ul class="nav nav-pills nav-stacked profileNav">
			<li id="navFeed"><a href="/user/<?php echo $user->UserID ?>">Feed</a></li>
			<li id="navCollection"><a href="/user/<?php echo $user->UserID ?>/collection">Collection</a></li>
			<?php 
				// if logged in and this user
				if($sessionUserID != null && $sessionUserID == $user->UserID) 
				{
					echo "<li id='navSettings'><a href='/user/settings'>Settings</a></li>";
				} 
			?>
		</ul>
		<?php
			// if logged in and not this user
			if($sessionUserID != null && $sessionUserID != $user->UserID) 
			{
				// user is following user
				if($user->ChildUserID != null)
				{
					$label = "Following";
					$style = "success";
				} else {
					$label = "Follow";
					$style = "default";
				}
				echo '<a onclick="javascript:changeFollowingStatus(' . $user->UserID . ');" id="followButton" class="btn btn-' . $style . ' btn-fullWidth"><span class="glyphicon glyphicon-star"></span> ' . $label . '</a>';
			} 

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
								<a href="/game/' . $currentlyPlayingGame->GBID . '">
									<img src="' . $currentlyPlayingGame->ImageSmall . '" class="tinyIconImage gameBoxArt" />
								</a>
							</div>
							<div class="media-body currentlyPlayingTitle">
								<a href="/game/' . $currentlyPlayingGame->GBID . '">' . $currentlyPlayingGame->Name . '</a>
							</div>
						</div>';
				}
			}
		?>
	</div>
	<div class="col-sm-8"> 