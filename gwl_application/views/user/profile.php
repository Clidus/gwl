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
			if($sessionUserID != null && $sessionUserID == $user->UserID) 
			{
				echo "<a href='" . $baseUrl . "user/edit'>Edit Profile</a>";
			} 
		?>
	</div>
	<div class="col-sm-8">   


    	<?php 
	    	foreach ($events as $event)
			{
				echo "<div class='media'>
					<a class='pull-left' href='#'>
						<img class='media-object gameBoxArt' src='" . $event->Image . "' alt='...' style='width: 100px;'>
					</a>
					<div class='media-body'>
						<h4 class='media-heading'>";
				switch($event->ActionID) 
				{
					case 1:
						switch($event->Value)
			            {
			                case 1:
			                    $label = "owns";
			                    $style = "success";
			                    break;
			                case 2:
			                    $label = "wants";
			                    $style = "warning";
			                    break;
			                case 3:
			                    $label = "borrowed";
			                    $style = "info";
			                    break;
			                case 4:
			                    $label = "lent";
			                    $style = "danger";
			                    break;
			                case 5:
			                    $label = "played";
			                    $style = "primary";
			                    break;
			            }
			    		echo $event->Username . ' <span class="label label-' . $style . '">' . $label . '</span> <a href="' . $baseUrl . 'game/' . $event->GBID . '">' . $event->Name . '</a><br />';
				}
				echo "	</h4>	
					</div>
				</div>";
			}
    	?>
    </div>
</div>