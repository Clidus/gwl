<!-- Modal -->
<div class="modal fade" id="removeGameModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">This is serious business duder.</h4>
      </div>
      <div class="modal-body">
        Are you sure you want to remove this game from your collection? If you have sold it, consider changing its status to "Played".
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
        <span id="removeGameButtonPlaceholder"></span>
      </div>
    </div>
  </div>
</div>

<ul class="breadcrumb">
    <li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb"><span itemprop="title"><a href="/">Home</a></span></li>       
    <li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb" class="active"><span itemprop="title"><?php echo $game->name ?></span></li>
</ul>

<h2><?php echo $game->name ?></h2>

<div class="row">
    <div class="col-sm-4">
        <img class="imageShadow gamePageBoxArt" src="<?php if(is_object($game->image)) echo $game->image->small_url; ?>">
        <div class="panel panel-default"> 
            <div class="panel-body">
              <p><?php echo $game->deck; ?></p>
              <p><a href="<?php echo $game->site_detail_url; ?>" target="_blank">Read more on GiantBomb.com.</a></p>   
              <?php if($sessionUserID > 0) { ?>
                  <div class="pull-right">  
                        <div class='btn-group'>
                            <button id='gameButton<?php echo $game->id ?>' data-toggle='dropdown' class='btn btn-<?php echo $game->listStyle ?> dropdown-toggle'><?php echo $game->listLabel ?> <span class='caret'></span></button>
                            <ul class="dropdown-menu">
                                <li><a onclick="javascript:addGame(<?php echo $game->id ?>, 1, true);">Own</a></li>
                                <li><a onclick="javascript:addGame(<?php echo $game->id ?>, 2, true);">Want</a></li>
                                <li><a onclick="javascript:addGame(<?php echo $game->id ?>, 3, true);">Borrowed</a></li>
                                <li><a onclick="javascript:addGame(<?php echo $game->id ?>, 4, true);">Lent</a></li>
                                <li><a onclick="javascript:addGame(<?php echo $game->id ?>, 5, true);">Played</a></li>
                            </ul>
                        </div> 
                        <span id="inCollectionControls<?php echo $game->id ?>" class="<?php if($game->listID == 0) echo "hidden" ?>">
                            <div id='statusButtonGroup<?php echo $game->id ?>' class='btn-group'>
                                <button id='statusButton<?php echo $game->id ?>' data-toggle='dropdown' class='btn btn-<?php echo $game->statusStyle ?> dropdown-toggle'><?php echo $game->statusLabel  ?> <span class='caret'></span></button>
                                <ul class='dropdown-menu'>
                                    <li><a onclick="javascript:changeStatus(<?php echo $game->id ?>, 1);">Unplayed</a></li>
                                    <li><a onclick="javascript:changeStatus(<?php echo $game->id ?>, 2);">Unfinished</a></li>
                                    <li><a onclick="javascript:changeStatus(<?php echo $game->id ?>, 3);">Complete</a></li>
                                    <li><a onclick="javascript:changeStatus(<?php echo $game->id ?>, 4);">Uncompletable</a></li>
                                </ul>
                            </div>
                        </span>
                    </div>   
                <?php } ?>   
            </div>  
            <?php    
                if(property_exists($game, "platforms") && $game->platforms != null)
                {
                    echo "<div id='platforms" . $game->id . "' class='panel-footer'>";
                    foreach($game->platforms as $platform)
                    {
                        echo "<label><input id='platform_" . $game->id . "_" . $platform->id . "' type='checkbox'";
                        if($platform->inCollection) echo " checked";
                        if($game->listID == 0) echo " readonly";
                        echo "> <span class='label label-info'>" . $platform->name . "</span></label> ";  
                    }
                    echo "</div>";  
                }
            ?>                                
        </div> 
        <?php if($sessionUserID > 0 && $game->listID > 0) { ?>
            <div class="panel panel-default"> 
                <div class="panel-body">
                    <div class="form-group">
                        <label for="currentlyPlayingInput">Currently Playing?</label>
                        <select class="form-control" id="currentlyPlayingInput">
                            <option value="false" <?php if(!$game->currentlyPlaying) echo "selected"; ?>>No</option>
                            <option value="true" <?php if($game->currentlyPlaying) echo "selected"; ?>>Yes</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="hoursPlayedInput">Hours Played</label>
                        <input type="text" class="form-control" id="hoursPlayedInput" value="<?php echo $game->hoursPlayed; ?>">
                    </div>
                    <div class="form-group">
                        <label for="dateCompletedInput">Date Completed</label>
                        <input type="date" class="form-control" id="dateCompletedInput" placeholder="dd/mm/yyyy" value="<?php echo $game->dateComplete; ?>">
                    </div>
                    <a onclick="javascript:saveProgression(<?php echo $game->id ?>);" class='btn btn-success progressionSaveButton' id='progressionSaveButton'>Save</a>                
                </div>
            </div>
            <a onclick='javascript:showRemoveGameWarning(<?php echo $game->id; ?>);' class='btn btn-danger btn-fullWidth'>Remove from Collection</a>
        <?php } ?> 
    </div>
    <div class="col-sm-8">
        <?php
            if($users != null)
            {
                echo '<h4>Who\'s played this?</h4>

                <div class="itemGrid clearfix">';

                foreach($users as $user)
                {
                    echo '<div class="itemGridImage pull-left">
                        <a href="/user/'. $user->UserID . '">
                            <img class="itemGridImage imageShadow" src="/uploads/' . $user->ProfileImage . '" alt="' . $user->UserName . '" title="' . $user->UserName . '">
                            <span class="label label-' . $user->StatusStyle . ' itemGridLabel">' . $user->StatusNameShort . '</span>
                        </a>
                    </div>';
                }

                echo '</div>';
            }
        ?>
    
    <h4>What's Happening?</h4>