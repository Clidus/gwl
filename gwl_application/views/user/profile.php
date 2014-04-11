<ul class="breadcrumb">
    <li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb"><span itemprop="title"><a href="<?php echo $baseUrl ?>">Home</a></span></li>       
    <li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb" class="active"><span itemprop="title"><?php echo $user->Username ?></span></li>
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
	</div>
	<div class="col-sm-8"> 
		<ul class="nav nav-tabs profileTabs">
			<li class="active"><a href="">Feed</a></li>
			<li><a href="">Collection</a></li>
			<?php 
				if($sessionUserID != null && $sessionUserID == $user->UserID) 
				{
					echo "<li><a href='" . $baseUrl . "user/edit'>Settings</a></li>";
				} 
			?>
		</ul>  
    	<?php 
	    	foreach ($events as $event)
			{
		?>
		<div class="panel panel-default"> 
            <div class="panel-body media">
				<a class='pull-left' href='#'>
					<img class='media-object gameBoxArt eventImage' src='<?php echo $event->ImageSmall ?>' />
				</a>
				<div class="media-body">
					<?php
			    		echo '<p><b>' . $event->Username;
			    		
			    		// events
				    	$i = 1;
				    	foreach($event->eventItems as $item) 
				    	{
				    		echo $item;
			    			if($i === count($event->eventItems)-1) {
							    echo " and ";
							} else if($i < count($event->eventItems)) {
							    echo ", ";
							}
							$i++;
				    	}
				    	
				    	// game name
				    	echo ' <a href="' . $baseUrl . 'game/' . $event->GBID . '">' . $event->Name . '</a></b>';
			    		
			    		// platforms
			    		if(count($event->platforms) > 0) {
			    			echo " on ";
			    			$i = 1;
				    		foreach($event->platforms as $platfrom)
				    		{
				    			echo $platfrom->Abbreviation;
				    			if($i !== count($event->platforms)) {
								    echo ", ";
								}
								$i++;
				    		}
				    	}

				    	echo '</p>';
			    		echo '<p class="gameDeck">' . $event->Deck . '</p>';
			    		echo '<p class="datestamp">' . $event->DateStampFormatted . '</p>';
					?>
				</div>
			</div>
		</div>
		<?php
			}
    	?>
    </div>
</div>