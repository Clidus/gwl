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
							<div class="pull-left">
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
						<div id="newComment' . $post->PostID . '"></div>
						<div class="pull-left">
							<img src="/uploads/' . $sessionProfileImage . '" class="tinyIconImage imageShadow" />
						</div>
						<button type="button" class="btn btn-default pull-right" onclick="javascript:postComment(' . $post->PostID . ', 1);">Post</button>
						<div class="media-body">
							<textarea id="commentField' . $post->PostID . '" rows="1" placeholder="Say something..." class="form-control textAreaAutoGrow" name="post"></textarea>
						</div>';
				} else {
					echo 'Please <a href="/login">login</a> to post a comment.';
				}
			?>
		</div>
	</div>