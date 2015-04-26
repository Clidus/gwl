		<div class="row collectionStats">
			<div class="col-xs-3">
				<span id="collectionCount"></span>
				<p>Collection</p>
			</div>
			<div class="col-xs-3">
				<span id="completeCount"></span>
				<p>Completed</p>
			</div>
			<div class="col-xs-3">
				<span id="backlogCount"></span>
				<p>Backlog</p>
			</div>
			<div class="col-xs-3">
				<span id="wantCount"></span>
				<p>Want</p>
			</div>
		</div>

		<div class="progress">
			<div id="completionPercentage" class="progress-bar progress-bar-success" role="progressbar" aria-valuemin="0" aria-valuemax="100">
				<span id="completionPercentageLabel"></span>% Complete
			</div>
		</div>

		<div class="row">
			<div class="col-sm-8">
				<?php 
					if($sessionUserID != null && $sessionUserID == $user->UserID) 
					{
						echo "<a class='btn btn-primary btn-fullWidth' href='/search'>Add Games &raquo;</a>";
					}
				?>
				<div id="gameCollection"></div>
			</div>
			<div class="col-sm-4">
			
			</div>
		</div>
	</div>
</div>