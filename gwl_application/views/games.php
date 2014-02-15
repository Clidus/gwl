<!-- Modal -->
<div class="modal fade" id="removeGameModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">This is serious business duder.</h4>
      </div>
      <div class="modal-body">
        Are you sure you want to remove this game from your collection? If you have sold it, consider changing it's status to "Played".
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
        <span id="removeGameButtonPlaceholder"></span>
      </div>
    </div>
  </div>
</div>

<ul class="breadcrumb">
    <li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb"><span itemprop="title"><a href="<?php echo $baseUrl ?>">Home</a></span></li>       
    <li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb" class="active"><span itemprop="title"><?php echo $game->name ?></span></li>
</ul>

<h2><?php echo $game->name ?></h2>

<div class="row">
    <div class="col-sm-4">
        <img class="gameBoxArt gamePageBoxArt" src="<?php if(is_object($game->image)) echo $game->image->small_url; ?>">
        <h4>Game Details</h4>
        <ul class="list-group">
            <li class="list-group-item clearfix">
                <div class="pull-left gamePageFactsLabel">
                    <b>First Release Date:</b>
                </div>
                <div class="media-body gamePageFactsDetails">
                    <?php 
                        if(property_exists($game, "original_release_date") && $game->original_release_date != null)
                            echo date_format(date_create($game->original_release_date), 'jS F, Y') ?>
                </div>
            </li>
            <li class="list-group-item clearfix">
                <div class="pull-left gamePageFactsLabel">
                    <b>Developer:</b>
                </div>
                <div class="media-body gamePageFactsDetails">
                    <?php
                        if(property_exists($game, "developers") && $game->developers != null)
                            foreach($game->developers as $item)
                                echo $item->name . "<br />";    
                    ?>
                </div>
            </li>
            <li class="list-group-item clearfix">
                <div class="pull-left gamePageFactsLabel">
                    <b>Publisher:</b>
                </div>
                <div class="media-body gamePageFactsDetails">
                    <?php
                        if(property_exists($game, "publishers") && $game->publishers != null)
                            foreach($game->publishers as $item)
                                echo $item->name . "<br />";    
                    ?>
                </div>
            </li>
            <li class="list-group-item clearfix">
                <div class="pull-left gamePageFactsLabel">
                    <b>Genre:</b>
                </div>
                <div class="media-body gamePageFactsDetails">
                    <?php
                        if(property_exists($game, "genres") && $game->genres != null)
                            foreach($game->genres as $item)
                                echo $item->name . "<br />";    
                    ?>
                </div>
            </li>
            <li class="list-group-item clearfix">
                <div class="pull-left gamePageFactsLabel">
                    <b>Theme:</b>
                </div>
                <div class="media-body gamePageFactsDetails">
                    <?php
                        if(property_exists($game, "themes") && $game->themes != null)
                            foreach($game->themes as $item)
                                echo $item->name . "<br />";    
                    ?>
                </div>
            </li>
            <li class="list-group-item clearfix">
                <div class="pull-left gamePageFactsLabel">
                    <b>Franchise:</b>
                </div>
                <div class="media-body gamePageFactsDetails">
                    <?php
                        if(property_exists($game, "franchises") && $game->franchises != null)
                            foreach($game->franchises as $item)
                                echo $item->name . "<br />";    
                    ?>
                </div>
            </li>
        </ul>
    </div>
    <div class="col-sm-8">
        <div class="panel panel-default"> 
            <div class="panel-body">
              <p><?php echo $game->deck; ?></p>
              <p class="readMoreOn"><a href="<?php echo $game->site_detail_url; ?>" target="_blank">Read more on GiantBomb.com.</a></p>         
            </div>  
            <?php    
                if(property_exists($game, "platforms") && $game->platforms != null)
                {
                    echo "<div class='panel-footer'>";
                    foreach($game->platforms as $gbPlatform)
                    {
                        echo "<span class='label label-info'>" . $gbPlatform->name . "</span> ";  
                    }
                    echo "</div>";  
                }
            ?>                                
        </div>                 
        <?php if($sessionUserID > 0) { ?>
            <div class="pull-right">  
                <div class='btn-group searchResultButton'>
                    <button id='gameButton<?php echo $game->id ?>' data-toggle='dropdown' class='btn btn-<?php echo $game->listStyle ?> dropdown-toggle'><?php echo $game->listLabel ?> <span class='caret'></span></button>
                    <ul class="dropdown-menu">
                        <li><a onclick="javascript:addGame(<?php echo $game->id . ", '" . $game->api_detail_url ?>', null, 1);">Own</a></li>
                        <li><a onclick="javascript:addGame(<?php echo $game->id . ", '" . $game->api_detail_url ?>', null, 2);">Want</a></li>
                        <li><a onclick="javascript:addGame(<?php echo $game->id . ", '" . $game->api_detail_url ?>', null, 3);">Borrowed</a></li>
                        <li><a onclick="javascript:addGame(<?php echo $game->id . ", '" . $game->api_detail_url ?>', null, 4);">Lent</a></li>
                        <li><a onclick="javascript:addGame(<?php echo $game->id . ", '" . $game->api_detail_url ?>', null, 5);">Played</a></li>
                    </ul>
                </div> 
                <span id="inCollectionControls<?php echo $game->id ?>" class="<?php if($game->listID == 0) echo "hidden" ?>">
                    <div id='statusButtonGroup<?php echo $game->id ?>' class='btn-group searchResultButton'>
                        <button id='statusButton<?php echo $game->id ?>' data-toggle='dropdown' class='btn btn-<?php echo $game->statusStyle ?> dropdown-toggle'><?php echo $game->statusLabel  ?> <span class='caret'></span></button>
                        <ul class='dropdown-menu'>
                            <li><a onclick="javascript:changeStatus(<?php echo $game->id ?>, 1);">Unplayed</a></li>
                            <li><a onclick="javascript:changeStatus(<?php echo $game->id ?>, 2);">Unfinished</a></li>
                            <li><a onclick="javascript:changeStatus(<?php echo $game->id ?>, 3);">Complete</a></li>
                            <li><a onclick="javascript:changeStatus(<?php echo $game->id ?>, 4);">Uncompletable</a></li>
                        </ul>
                    </div> 
                    <a onclick="javascript:showRemoveGameWarning(<?php echo $game->id ?>);" class='btn btn-danger searchResultButton'>Remove from Collection</a>
                </span>
            </div>
        <?php } ?>
    </div>
</div>