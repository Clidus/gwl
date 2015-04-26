<ul class="breadcrumb">
    <li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb"><span itemprop="title"><a href="/">Home</a></span></li>       
    <li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb" class="active"><span itemprop="title"><?php echo $user->Username ?></span></li>
</ul>

<h2><?php echo $user->Username ?></h2>

<div class="row">
	<div class="col-sm-3">
		<img src="/uploads/<?php echo $user->ProfileImage ?>" class="largeProfileImage imageShadow" />
		<ul class="nav nav-pills nav-stacked profileNav">
			<li id="navFeed"><a href="/user/<?php echo $user->UserID ?>">Feed</a></li>
			<li id="navCollection"><a href="/user/<?php echo $user->UserID ?>/collection">Collection</a></li>
			<li id="navPlatforms"><a href="/user/<?php echo $user->UserID ?>/platforms">Platforms</a></li>
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
				echo "<div class='userBio'>
					<h4>About</h4>
					" . $user->Bio . "
				</div>";
			}

			if(isset($currentlyPlaying) && $currentlyPlaying != null)
			{
				echo '<h4>Currently Playing</h4>

                <div class="itemGrid clearfix">';

				foreach($currentlyPlaying as $currentlyPlayingGame)
				{
					echo '<div class="itemGridImage pull-left">
                        <a href="/game/' . $currentlyPlayingGame->GBID . '">
                            <img class="itemGridImage imageShadow" src="' . $currentlyPlayingGame->ImageSmall . '" alt="' . $currentlyPlayingGame->Name . '" title="' . $currentlyPlayingGame->Name . '">
                            <span class="label label-' . $currentlyPlayingGame->StatusStyle . ' itemGridLabel">' . $currentlyPlayingGame->StatusNameShort . '</span>
                        </a>
                    </div>';
				}

				echo '</div>';
			}
		?>
	</div>
	<div class="col-sm-9"> 