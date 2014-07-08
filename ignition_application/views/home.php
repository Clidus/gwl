<!--<div class="alert alert-info">
	<b>Update:</b> <a href="/blog/version-0-2-0">Check out what is new in Version 0.2.0</a>.
</div>-->

<div class="jumbotron" id="homepageJumbotron">
	<?php if($sessionUserID == null) { ?>
		<h1>Gaming with Lemons</h1>
		<p>Open source video game collection, wishlist and backlog tracker.</p>
		<p>
			<a class="btn btn-lg btn-primary" href="https://www.youtube.com/watch?v=CVQPpz-aYAQ">Watch the Demo &raquo;</a>
			<a class="btn btn-lg btn-success" href="/register">Join the Alpha &raquo;</a>
		</p>
	<?php } else { ?>
		<h1>Welcome to the Alpha</h1>
		<p>Get started by searching for some games.</p>
		<p>
			<a class="btn btn-lg btn-primary" href="/search">Start adding games &raquo;</a>
			<a class="btn btn-lg btn-info" href="/user/<?php echo $sessionUserID ?>/collection">View your collection &raquo;</a>
		</p>
	<?php } ?>
</div>

<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title">What's Happening</h3>
	</div>
	<div class="panel-body userActivity">
		<?php 
			foreach ($events as $event)
			{
				echo '
				<div class="clearfix userActivityItem">
					<a class="pull-left" href="' . $event->UserUrl . '">
						<img class="media-object gameBoxArt tinyIconImage" src="' . $event->UserImage . '" />
					</a>
					<a class="pull-left" href="' . $event->GameUrl . '">
						<img class="media-object gameBoxArt tinyIconImage" src="' . $event->GameImage . '" />
					</a>
					<div class="media-body clearfix">
						<b>' . $event->Username . $event->Label . '</b> ' . $event->GameName . '<span class="userActivityDateStamp">' . $event->DateStampFormatted . '</span>
					</div>
				</div>';
			}
		?>
	</div>
</div>

<div class="row">
	<div class="col-md-4">
		<div class="thumbnail">
			<img src="/images/Lemongrab.png">
		</div>
		<h2>For Real People</h2>
		<p>Designed with real people in mind. Own games on multiple platforms, add games efficiently from a search result, view your collection on desktop and mobile.</p>
	</div>
	<div class="col-md-4">
		<div class="thumbnail">
			<img src="/images/GiantBomb.png">
		</div>
		<h2>Structured Data</h2>
		<p>Using <a href="http://www.giantbomb.com/api">Giant Bomb’s API</a> - the world’s best user-editable video game database - your collection is stored in a structured way that creates useful, powerful information. And it’s all exportable when you want to leave.</p>
	</div>
	<div class="col-md-4">
		<div class="thumbnail">
			<img src="/images/Octocat.png">
		</div>
		<h2>Open Source</h2>
		<p>Gaming with Lemons is free and open source software available on GitHub. <a href="https://github.com/Clidus/gwl">Fork us today!</a></p>
	</div>
</div>