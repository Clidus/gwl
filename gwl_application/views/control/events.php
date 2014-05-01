<?php 

	if(count($events) == 0 && $pageNumber > 1)
	{
		echo "<div class='alert alert-warning'>Nothing more to see duder!</div>";
	}
	else if(count($events) == 0)
	{
		echo "<div class='alert alert-warning'>Looks like nothing has happened yet!</div>";
	} else {
		// loop through user events
    	foreach ($events as $event)
		{
?>
			<div class="panel panel-default"> 
				<div class="panel-body media">
					<?php 
						// user profile
						if(isset($user))
						{
							$eventUrl = $baseUrl . 'game/' . $event->GBID;
							$eventImage = $event->ImageSmall;
							$eventUserName = $event->Username;
							$eventGameName = '<a href="' . $eventUrl . '">' . $event->Name . '</a></b>';
						}
						// game page
						else 
						{
							$eventUrl = $baseUrl . 'user/' . $event->UserID;
							$eventImage = $baseUrl . 'uploads/' . $event->ProfileImage;
							$eventUserName = '<a href="' . $eventUrl . '">' . $event->Username . '</a></b>';
							$eventGameName = $event->Name;
						}
					?>
					<a class='pull-left' href='<?php echo $eventUrl ?>'>
						<img class='media-object gameBoxArt eventImage' src='<?php echo $eventImage ?>' />
					</a>
					<div class="media-body clearfix eventDetail">
						<?php
							echo '<p><b>' . $eventUserName;

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
							echo ' ' . $eventGameName;

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
							echo '<p class="datestamp pull-right">' . $event->DateStampFormatted . '</p>';
						?>
					</div>
					<?php
						// comments
						// if there are more than two comments, hide all but the most recent one
						if(count($event->comments) > 2) {
							echo '<div class="hidden" id="hiddenComments' . $event->EventID . '">';
							$numberOfHiddenComments = count($event->comments)-1;
						}

						// loop through comments
						for($i = 0; $i < count($event->comments); $i++)
						{
							$comment = $event->comments[$i];

							// if there are hidden comments (comments are hidden when there are more than 2)
							// and this is the last comment, close hidden div (always show last comment) and display link to show all comments
							if(count($event->comments) > 2 && $i == $numberOfHiddenComments)
							{
								echo '</div>';
								echo '<div id="hiddenCommentLink' . $event->EventID . '" class="eventCommentDisplay"><a class="handPointer" onclick="showComments(' . $event->EventID . ');">View ' . $numberOfHiddenComments . ' more comments</a></div>';
							}

							// display comment
							echo '
								<div class="clearfix eventCommentDisplay">
									<div class="pull-left">
										<img src="' . $baseUrl . 'uploads/' . $comment->ProfileImage . '" class="tinyIconImage gameBoxArt" />
									</div>
									<div class="media-body eventComment">
										<a href="' . $baseUrl . 'user/' . $comment->UserID . '">' . $comment->Username . '</a></b> 
										' . $comment->Comment . '
										<span class="datestamp pull-right">' . $comment->DateStampFormatted . '</span>
									</div>
								</div>';
						}

						// if logged in, show comment post box
						if($sessionUserID != null)
						{
							echo '
								<div id="newComment' . $event->EventID . '"></div>
								<div class="pull-left">
									<img src="' . $baseUrl . 'uploads/' . $sessionProfileImage . '" class="tinyIconImage gameBoxArt" />
								</div>
								<button type="button" class="btn btn-default pull-right" onclick="javascript:postComment(' . $event->EventID . ');">Post</button>
								<div class="media-body">
									<textarea id="commentField' . $event->EventID . '" rows="1" placeholder="Say something..." class="form-control textAreaAutoGrow" name="post"></textarea>
								</div>';
						} 
					?>
				</div>
			</div>
<?php
		}

		// user profile
		if(isset($user))
			$url = $baseUrl . 'user/' . $user->UserID . '/';
		// game page
		else
			$url = $baseUrl . 'game/' . $game->id . '/';

		echo '<ul class="pager">';
		if($pageNumber > 1) echo '<li class="previous"><a href="' . $url . ($pageNumber-1) . '">&larr; Newer</a></li>';
		echo '<li class="next"><a href="' . $url . ($pageNumber+1) . '">Older &rarr;</a></li></ul>';
	}
?>