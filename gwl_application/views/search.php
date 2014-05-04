<ul class="breadcrumb">
    <li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb"><span itemprop="title"><a href="<?php echo $baseUrl ?>">Home</a></span></li>       
    <li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb" class="active"><span itemprop="title"><?php echo $pagetitle ?></span></li>
</ul>

<div class="row">
    <div class="col-sm-8">
        <h2>Search</h2>
        <form role="form-inline" method="post" action="<?php echo $baseUrl ?>search/">
            <div class="input-group">
                <input type="search" class="form-control" name="query" placeholder="Search" value="<?php echo $searchQuery ?>">
                <span class="input-group-btn">
                    <button type="submit" class="btn btn-default">Submit</button>
                </span>
            </div>
        </form>  
        <div class="searchResults">
            <?php
                if($searchQuery != '') 
                {
                    if($searchResults != null){    
                        // search results                                                                                                   
                        foreach($searchResults->results as $game)
                        {     
                            ?>

                            <div class="clearfix">    
                                <a href='<?php echo $baseUrl . "game/" . $game->id ?>'>
                                    <img class="media-object pull-left gameBoxArt searchResultImage" src="<?php if(is_object($game->image)) echo $game->image->small_url; ?>">
                                </a>    
                                <div class="pull-left searchResultBody">  
                                    <h4><?php echo "<a href='" . $baseUrl . "game/" . $game->id . "'>" . $game->name . "</a>" ?></h4>
                                    <div class="panel panel-default"> 
                                        <div class="panel-body">
                                            <p><?php echo $game->deck; ?></p>   
                                            <p><a href="<?php echo $game->site_detail_url; ?>" target="_blank">Read more on GiantBomb.com.</a></p>
                                            <?php if($sessionUserID > 0) { ?>
                                                <div class="pull-right">  
                                                    <div class='btn-group'>
                                                        <button id='gameButton<?php echo $game->id ?>' data-toggle='dropdown' class='btn btn-<?php echo $game->listStyle ?> dropdown-toggle'><?php echo $game->listLabel ?> <span class='caret'></span></button>
                                                        <ul class="dropdown-menu">
                                                            <li><a onclick="javascript:addGame(<?php echo $game->id ?>, 1, false);">Own</a></li>
                                                            <li><a onclick="javascript:addGame(<?php echo $game->id ?>, 2, false);">Want</a></li>
                                                            <li><a onclick="javascript:addGame(<?php echo $game->id ?>, 3, false);">Borrowed</a></li>
                                                            <li><a onclick="javascript:addGame(<?php echo $game->id ?>, 4, false);">Lent</a></li>
                                                            <li><a onclick="javascript:addGame(<?php echo $game->id ?>, 5, false);">Played</a></li>
                                                        </ul>
                                                    </div> 
                                                    <span id="inCollectionControls<?php echo $game->id ?>" class="<?php if($game->listID == 0) echo "hidden" ?>">
                                                        <div id="statusButtonGroup<?php echo $game->id ?>" class="btn-group">
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
                                                    if($game->listID == 0) echo " disabled";
                                                    echo "> <span class='label label-info'>" . $platform->name . "</span></label> ";  
                                                }
                                                echo "</div>";  
                                            }
                                        ?>                                              
                                    </div>   
                                </div>
                            </div>                            
                            <hr>

                            <?php
                        } 

                        // paging
                        $numberOfPages = ceil($searchResults->number_of_total_results/10); 
                        echo '<ul class="pagination">';
                        if($searchPage-1 > 0)
                        {
                            echo "<li><a href='" . $baseUrl . "search/" . $searchQuery . "/" . ($searchPage-1) . "/'>«</a></li>";    
                        }
                        $i = 0;
                        while($i < $numberOfPages)
                        {    
                            $i++; 
                            if($i == $searchPage){            
                                echo "<li class='active'>";
                            } else {                  
                                echo "<li>";
                            }     
                            echo "<a href='" . $baseUrl . "search/" . $searchQuery . "/" . $i . "/'>" . $i . "</a></li>";                                                                                           
                        }
                        if($searchPage+1 <= $numberOfPages)
                        {
                            echo "<li><a href='" . $baseUrl . "search/" . $searchQuery . "/" . ($searchPage+1) . "/'>»</a></li>";    
                        }
                        echo '</ul>';
                    } else {
                        echo '<div class="alert alert-danger">Sorry duder, nothing was found.<a class="close" data-dismiss="alert" href="#">&times;</a></div>';                    
                    }   
                }
            ?>
        </div>
    </div>
    <div class="col-sm-4">
        <h2>Build your collection!</h2>
        <p>Keep track of the games you've completed and your backlog of shame by building your collection. It's real simple, just tag a game under one of our five categories.</p>
        
        <div class="media">
          <a class="pull-left" href="#">
            <span class='btn btn-success collectionStatusBadge media-object'>Own</span> 
          </a>
          <div class="media-body">
            You own the game. It's sitting right over there, on your shelf. You're pretty pleased with yourself.
          </div>
        </div>

        <div class="media">
          <a class="pull-left" href="#">
            <span class='btn btn-warning collectionStatusBadge media-object'>Want</span> 
          </a>
          <div class="media-body">
            Your wish list. You can't wait to get hold of this hot puppy. Why don't you have it yet?
          </div>
        </div>

        <div class="media">
          <a class="pull-left" href="#">
            <span class='btn btn-info collectionStatusBadge media-object'>Borrowed</span> 
          </a>
          <div class="media-body">
            You're borrowing this game from a friend. You wanted to play it, but not enough to buy it apparently.
          </div>
        </div>

        <div class="media">
          <a class="pull-left" href="#">
            <span class='btn btn-danger collectionStatusBadge media-object'>Lent</span> 
          </a>
          <div class="media-body">
            You've lent this game out to someone. You are going to get it back right?
          </div>
        </div>
        
        <div class="media">
          <a class="pull-left" href="#">
            <span class='btn btn-primary collectionStatusBadge media-object'>Played</span> 
          </a>
          <div class="media-body">
            You played this game, but you don't own it. Maybe you traded it in for the new hot jam. Or you just flushed it down a toilet, because it was that terrible.
          </div>
        </div>
    </div>
</div>