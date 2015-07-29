		<?php
			if(count($platforms) == 0)
			{
				echo "<div class='alert alert-warning'>No games found handsome.</div>";
			} else {
				foreach($platforms as $platform)
				{
					echo '<div class="row">
						<div class="col-xs-12">
							<p><a href="/user/1/collection#/platforms=' . $platform->PlatformID . '">' . $platform->Name . '</a></p>
						</div>
					</div>

					<div class="row collectionStats">
						<div class="col-xs-4">
							<a href=""><img src="/images/platforms/' . $platform->Image . '" class="imageShadow platformLogo" /></a>
						</div>
						<div class="col-xs-2">
							<span>' . $platform->Collection . '</span>
							<p>Collection</p>
						</div>
						<div class="col-xs-2">
							<span>' . $platform->Completed . '</span>
							<p>Completed</p>
						</div>
						<div class="col-xs-2">
							<span>' . $platform->Backlog . '</span>
							<p>Backlog</p>
						</div>
						<div class="col-xs-2">
							<span>' . $platform->Want . '</span>
							<p>Want</p>
						</div>
					</div>

					<div class="progress">
						<div style="width: 0%" class="progress-bar progress-bar-success" role="progressbar" aria-valuemin="0" aria-valuemax="100" data-percentage="' . $platform->Percentage . '">
							' . $platform->Percentage . '% Complete
						</div>
					</div>';
				}
			}
		?>
	</div>
</div>