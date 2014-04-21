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
			if(count($events) == 0 && $pageNumber > 1)
			{
				echo "<div class='alert alert-warning'>Nothing more to see duder!</div>";
			}
			else if(count($events) == 0)
			{
				echo "<div class='alert alert-warning'>Looks like this duder hasn't done anything yet!</div>";
			} else {
	    		// loop through user events
		    	foreach ($events as $event)
				{
		?>
					<div class="panel panel-default"> 
			            <div class="panel-body media">
							<a class='pull-left' href='<?php echo $baseUrl . 'game/' . $event->GBID ?>'>
								<img class='media-object gameBoxArt eventImage' src='<?php echo $event->ImageSmall ?>' />
							</a>
							<div class="media-body clearfix eventDetail">
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
											<img src="' . $baseUrl . 'uploads/' . $comment->ProfileImage . '" class="commentProfileImage gameBoxArt" />
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
											<img src="' . $baseUrl . 'uploads/' . $sessionProfileImage . '" class="commentProfileImage gameBoxArt" />
										</div>
										<button type="button" class="btn btn-default pull-right" onclick="javascript:postComment(' . $event->EventID . ');">Post</button>
										<div class="media-body">
											<textarea id="commentField' . $event->EventID . '" rows="1" placeholder="Say something..." class="form-control textAreaAutoGrow" name="post"></textarea>
										</div>
									';
								} 
							?>
						</div>
					</div>
		<?php
				}
				echo '<ul class="pager">';
				if($pageNumber > 1) echo '<li class="previous"><a href="' . $baseUrl . 'user/' . $user->UserID . '/' . ($pageNumber-1) . '">&larr; Newer</a></li>';
				echo '<li class="next"><a href="' . $baseUrl . 'user/' . $user->UserID . '/' . ($pageNumber+1) . '">Older &rarr;</a></li></ul>';
			}
    	?>
    </div>
</div>