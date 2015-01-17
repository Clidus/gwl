<ul class="breadcrumb">
    <li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb"><span itemprop="title"><a href="/">Home</a></span></li>  
    <li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb"><span itemprop="title"><a href="/blog">Blog</a></span></li>  
    <li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb" class="active"><span itemprop="title"><?php echo $post->Title ?></span></li>   
</ul>

<div class="row">
	<div class="col-sm-8">
		<h2><?php echo $post->Title ?></h2>
		
		<?php if($post->Image != null) { ?>
			<img src="<?php echo $post->Image ?>" class="responsiveImage" title="<?php echo $post->Title ?>" alt="<?php echo $post->Title ?>" />
		<?php } ?>

		<div class="clearfix">
			<?php echo $post->Post ?>
		</div>

		<p class="postFooter">Posted by <a href="/user/<?php echo $post->UserID; ?>"><?php echo $post->Username ?></a> on the <?php echo date_format(date_create($post->Date), 'jS F, Y') ?>.</p>
	
		<div class="panel panel-default panel-footer media">
			<a name="comments"></a>
			<?php
				// comments
				// loop through comments
				foreach($post->comments as $comment)
				{
					// display comment
					echo '
						<div class="clearfix eventCommentDisplay">
							<div class="media-left">
								<img src="/uploads/' . $comment->ProfileImage . '" class="tinyIconImage imageShadow" />
							</div>
							<div class="media-body eventComment">';

								if($comment->RegisteredUser)
									echo '<a href="/user/' . $comment->UserID . '"><b>' . $comment->Username . '</b></a>';
								else
									echo '<b>' . $comment->Username . '</b>';

									echo $comment->Comment . '
							<span class="datestamp pull-right">' . $comment->DateStampFormatted . '</span>
						</div>
					</div>';
				}

				echo '
					<div id="newComment' . $post->PostID . '"></div>
					<div class="media-left">
						<img src="/uploads/' . $sessionProfileImage . '" class="tinyIconImage imageShadow" />
					</div>
					<div class="media-body commentFields">';

						if($sessionUserID == null)
						{
							echo '<input type="text" class="form-control" maxlength="50" name="name" id="nameInput' . $post->PostID . '" placeholder="Name">
							<input type="email" class="form-control" maxlength="100" id="emailInput' . $post->PostID . '" name="email" placeholder="Email">';
						}

						echo '<textarea id="commentField' . $post->PostID . '" rows="1" placeholder="Say something..." class="form-control textAreaAutoGrow" name="post"></textarea>
					</div>
					<div class="media-right">
						<button type="button" class="btn btn-default pull-right" onclick="javascript:postComment(' . $post->PostID . ', 1);">Post</button>
					</div>';
			?>
		</div>
	</div>