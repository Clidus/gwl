var filters = { lists : [], statuses : [], platforms : [], includeNoPlatforms : true, orderBy: "releaseDateDesc" };
var currentPage = 1;

$(document).ready(function() {
    /* filter checkbox change */
    $(":checkbox").change(function(){
        // get the filter type and filter id out of the checkbox id
        match = this.id.match("filter_([a-z]+)_([0-9]+)");
        // if ids found
        if(match.length == 3)
        {
            $(this).prop('disabled', true); // disable checkbox
            var filterID = parseInt(match[2]); // convert filter id to int
            var listType = match[1];
            var checkbox = this;

            // exception for including or excluding games with no platform
            if(listType == "platform" && filterID == 0)
            {
                if(checkbox.checked) {
                    filters.includeNoPlatforms = true;
                } else {
                    filters.includeNoPlatforms = false;
                }
            } else {
                // set current filter
                var currentFilter;
                switch(listType)
                {
                    case "list":
                        currentFilter = filters.lists;
                        break;
                    case "platform":
                        currentFilter = filters.platforms;
                        break;
                    case "status":
                        currentFilter = filters.statuses;
                        break;
                }

                if(checkbox.checked) {
                    var index = currentFilter.indexOf(filterID); // get array index of filterID
                    currentFilter.splice(index, 1); // remove filterID from array
                } else {
                    currentFilter.push(filterID); // add filterID to array
                }
            }

            currentPage = 1; // filter change, reset to page 1
            loadCollection(currentPage); // load collection
            $(checkbox).prop('disabled', false); // enable checkbox
        }
    });

    /* radio button change */
    $("input[name='orderBy']").change(function() {
        filters.orderBy = $("input[name='orderBy']:checked").val(); // set new order by
        currentPage = 1; // filter change, reset to page 1
        loadCollection(); // load collection
    });
});

function viewMoreGames() {
    currentPage++;
    loadCollection(currentPage);
}

function loadCollection() {
    $.ajax({
        type : 'POST',
        url : baseUrl + 'user/getCollection',
        dataType : 'json',
        data: {
            userID: UserID,
            page: currentPage,
            filters: JSON.stringify(filters)
        },
        success : function(data){
            if (data.error === true) {
                showErrorModal(data.errorMessage);
            } else { 
                var gameCollection = "";
                var collection = data.collection;

                if(collection != null) { 
                    for (i = 0; i < collection.length; ++i) {
                        gameCollection += '<div class="panel panel-default collectionItem media clearfix">';
                        gameCollection += '     <div class="pull-left">';
                        gameCollection += '         <img src="' + collection[i].ImageSmall + '" class="tinyIconImage gameBoxArt" />';
                        gameCollection += '     </div>';
                        gameCollection += '     <div class="media-body eventComment">';
                        gameCollection += '         <a href="' + baseUrl + 'game/' + collection[i].GBID + '">' + collection[i].Name + '</a></b>';
                        
                        // display list of platforms
                        var platforms = collection[i].Platforms;
                        if(platforms != null) 
                        {
                            gameCollection += " on ";
                            for (x = 0; x < platforms.length; x++)
                            {
                                gameCollection += platforms[x].Abbreviation;
                                if(x == platforms.length-2) {
                                    gameCollection += " and ";
                                } else if(x < platforms.length-1) {
                                    gameCollection += ", ";
                                }
                            }
                        }

                        gameCollection += '         <div>';
                        gameCollection += '             <span class="label label-' + collection[i].ListStyle + '">' + collection[i].ListName + '</span>';
                        gameCollection += '             <span class="label label-' + collection[i].StatusStyle + '">' + collection[i].StatusName + '</span>';
                        gameCollection += '         </div>';
                        gameCollection += '     </div>';
                        gameCollection += '</div>';
                    }
                }

                if(currentPage == 1)
                    $('#gameCollection').html(gameCollection);
                else
                    $('#gameCollection').append(gameCollection);

                if(collection.length > 0) {
                    if(collection.length == 30) {
                        $('#gameCollectionViewMore').html("<a class='handPointer' onclick='viewMoreGames()''>View more</a>");
                    } else {
                        $('#gameCollectionViewMore').html("");
                    }
                } else {
                    $('#gameCollectionViewMore').html("<div class='alert alert-warning alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button> No games found handsome.</div>");
                }
            }
        },
        error : function(XMLHttpRequest, textStatus, errorThrown) {
            showErrorModal('Well nuts. It appears that something didnt go to plan. Please call the coast guard imediately.');
        }
    });
}