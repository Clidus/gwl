		<div class="row collectionStats">
			<div class="col-xs-3">
				<span><?php echo $stats->Collection; ?></span>
				<p>Collection</p>
			</div>
			<div class="col-xs-3">
				<span><?php echo $stats->Completed; ?></span>
				<p>Completed</p>
			</div>
			<div class="col-xs-3">
				<span><?php echo $stats->Backlog; ?></span>
				<p>Backlog</p>
			</div>
			<div class="col-xs-3">
				<span><?php echo $stats->Want; ?></span>
				<p>Want</p>
			</div>
		</div>

		<div class="progress">
			<div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="<?php echo $stats->PercentComplete; ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $stats->PercentComplete; ?>%;">
				<?php echo $stats->PercentComplete; ?>% Complete
			</div>
		</div>

		<div class="row">
			<div class="col-sm-8">
				<a class="btn btn-primary btn-fullWidth" href="/search">Add Games &raquo;</a>
				<div id="gameCollection"></div>
				<div id="gameCollectionViewMore"></div>
			</div>
			<div class="col-sm-4">
				<b>Order By</b>
				<ul class="filters">
					<li>
						<label>
							<input name="orderBy" type="radio" value="releaseDateDesc" checked> 
							Release Date (Newest)
						</label>
					</li>
					<li>
						<label>
							<input name="orderBy" type="radio" value="releaseDateAsc"> 
							Release Date (Oldest)
						</label>
					</li>
					<li>
						<label>
							<input name="orderBy" type="radio" value="nameAsc"> 
							Name (A-Z)
						</label>
					</li>
					<li>
						<label>
							<input name="orderBy" type="radio" value="nameDesc"> 
							Name (Z-A)
						</label>
					</li>
					<li>
						<label>
							<input name="orderBy" type="radio" value="hoursPlayedDesc"> 
							Hours Played (Most)
						</label>
					</li>
					<li>
						<label>
							<input name="orderBy" type="radio" value="hoursPlayedAsc"> 
							Hours Played (Least)
						</label>
					</li>
				</ul>

				<b>List</b>
				<ul class="filters">
					<li>
						<label>
							<input id="filter_list_1" type="checkbox" checked> 
							Own
						</label>
					</li>
					<li>
						<label>
							<input id="filter_list_2" type="checkbox" checked> 
							Want
						</label>
					</li>
					<li>
						<label>
							<input id="filter_list_3" type="checkbox" checked> 
							Borrowed
						</label>
					</li>
					<li>
						<label>
							<input id="filter_list_4" type="checkbox" checked> 
							Lent
						</label>
					</li>
					<li>
						<label>
							<input id="filter_list_5" type="checkbox" checked> 
							Played
						</label>
					</li>
				</ul>

				<b>Completion</b>
				<ul class="filters">
					<li>
						<label>
							<input id="filter_status_1" type="checkbox" checked> 
							Unplayed
						</label>
					</li>
					<li>
						<label>
							<input id="filter_status_2" type="checkbox" checked> 
							Unfinished
						</label>
					</li>
					<li>
						<label>
							<input id="filter_status_3" type="checkbox" checked> 
							Complete
						</label>
					</li>
					<li>
						<label>
							<input id="filter_status_4" type="checkbox" checked> 
							Uncompletable
						</label>
					</li>
				</ul>

				<b>Platforms</b>
				<ul class="filters">
					<?php
						foreach($platforms as $platform)
						{
							if($platform->PlatformID == null) $platform->PlatformID = 0;
							if($platform->Abbreviation == null) $platform->Abbreviation = "No Platform";
							echo "<li>
									<label>
										<input id='filter_platform_" . $platform->PlatformID . "' type='checkbox' checked> " 
										. $platform->Abbreviation . " (" . $platform->Games . ")" . 
									"</label>
								</li>";
						}
					?>
				</ul>
			</div>
		</div>
	</div>
</div>