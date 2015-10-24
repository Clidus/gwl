	<div class="col-sm-4">
		<div class="blogLogo">
			<img src="/images/gwl_logo_blog.png" alt="Gaming with Lemons" title="Gaming with Lemons" />
		</div>

		<h2>Recent Posts</h2>
		<?php
			foreach($recentPosts as $post)
			{
		?>
				<p><a href="/blog/<?php echo $post->URL ?>"><?php echo $post->Title ?></a></p>
		<?php
			}
		?>
		<p><a href="/blog/archive">Blog Archive</a>

		<h2>Development Blog</h2>

		<p>Gaming with Lemons is in active development. You can read about the recent changes and improvements bellow, or check out our <a href="/changelog">Changelog</a>.</p>

		<p><a href="/blog/version-0-4">Version 0.4</a></p>

		<p><a href="/blog/version-0-3">Version 0.3</a></p>

		<p><a href="/blog/version-0-2">Version 0.2</a></p>

		<p><a href="/blog/alpha-release">Alpha Release</a></p>
	</div>
</div>