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
			<div class="panel panel-default userEvent"> 
				<div class="panel-body media">
					<div class="clearfix eventTitle">
						<div class="pull-left">
							<a href='<?php echo $event->UserUrl ?>'>
								<img class='media-object imageShadow tinyIconImage' src='<?php echo $event->UserImage ?>' />
							</a>
						</div>
						<div class="media-body eventComment">
							<b><?php echo $event->Username ?></b> <?php echo '<b>' . $event->Label . '</b> ' . $event->GameName . $event->PlatformsLabel ?>
						</div>
					</div>
					<div class="clearfix eventCommentDisplay">
						<div class="media-left">
							<a href='<?php echo $event->GameUrl ?>'>
								<img class='media-object imageShadow eventImage' src='<?php echo $event->GameImage ?>' />
							</a>
						</div>
						<div class="media-body eventComment">
							<p class="gameDeck"><?php echo $event->Deck ?></p>
							<p class="datestamp pull-right"><?php echo $event->DateStampFormatted ?></p>
						</div>
					</div>
				</div>
				<?php
					if(count($event->comments) > 0 || $sessionUserID != null)
						echo '<div class="panel-footer media">';

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
								<div class="media-left">
									<img src="/uploads/' . $comment->ProfileImage . '" class="tinyIconImage imageShadow" />
								</div>
								<div class="media-body eventComment">
									<a href="/user/' . $comment->UserID . '">' . $comment->Username . '</a></b> 
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
							<div class="media-left">
								<img src="/uploads/' . $sessionProfileImage . '" class="tinyIconImage imageShadow" />
							</div>
							<div class="media-body commentFieldContainer">
								<textarea id="commentField' . $event->EventID . '" rows="1" placeholder="Say something..." class="form-control textAreaAutoGrow" name="post"></textarea>
							</div>
							<div class="media-right">
								<button type="button" class="btn btn-default" onclick="javascript:postComment(' . $event->EventID . ', 2);">Post</button>
							</div>';
					} 

					if(count($event->comments) > 0 || $sessionUserID != null)
						echo '</div>';
				?>
			</div>
<?php
		}

		// user profile
		if(isset($user))
			$url = '/user/' . $user->UserID . '/';
		// game page
		else if(isset($game))
			$url = '/game/' . $game->id . '/';
		// homepage
		else
			$url = '/feed/';

		echo '<ul class="pager">';
		if($pageNumber > 1) echo '<li class="previous"><a href="' . $url . ($pageNumber-1) . '">&larr; Newer</a></li>';
		echo '<li class="next"><a href="' . $url . ($pageNumber+1) . '">Older &rarr;</a></li></ul>';
	}
?>