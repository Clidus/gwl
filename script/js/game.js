$(document).ready(function() {

    /* platform checkbox change */
    $(".panel-footer :checkbox").change(function(){
    
        // get the game and platform id out of the checkbox id
        match = this.id.match("platform_([0-9]+)_([0-9]+)");
        // if ids found and checkbox checked
        if(match.length == 3)
        {
            $(this).prop('disabled', true); // disable checkbox
            var checkbox = this;
            if(this.checked) {
                // add platform
                $.ajax({
                    type : 'POST',
                    url : '/games/addPlatform',
                    dataType : 'json',
                    data: {
                        GBID: match[1],
                        platformID: match[2]
                    },
                    success : function(data){
                        if (data.error === true) {
                            $(checkbox).prop('disabled', false); // enable checkbox
                            $(checkbox).prop('checked', false); // reset to unchecked as add failed
                            showErrorModal(data.errorMessage, data.errorProgressURL, data.errorProgressCTA);
                        } else {
                            $(checkbox).prop('disabled', false); // enable checkbox
                        }
                    },
                    error : function(XMLHttpRequest, textStatus, errorThrown) {
                        $(checkbox).prop('disabled', false); // enable checkbox
                        $(checkbox).prop('checked', false); // reset to unchecked as add failed
                        showErrorModal('Well shit. Some kind of error gone done happened. Please try again.');
                    }
                });
            } else {
                // remove platform
                $.ajax({
                    type : 'POST',
                    url : '/games/removePlatform',
                    dataType : 'json',
                    data: {
                        GBID: match[1],
                        platformID: match[2]
                    },
                    success : function(data){
                        if (data.error === true) {
                            $(checkbox).prop('disabled', false); // enable checkbox
                            $(checkbox).prop('checked', true); // reset to checked as remove failed
                            showErrorModal(data.errorMessage);
                        } else {
                            $(checkbox).prop('disabled', false); // enable checkbox
                        }
                    },
                    error : function(XMLHttpRequest, textStatus, errorThrown) {
                        $(checkbox).prop('disabled', false); // enable checkbox
                        $(checkbox).prop('checked', true); // reset to checked as remove failed
                        showErrorModal('Well shit. Some kind of error gone done happened. Please try again.');
                    }
                });
            }
        }
    });
});

/* add/update game status in collection */
function addGame(giantbombID, listID, reloadPage) {
    $('#gameButton' + giantbombID).addClass('disabled').html('Saving...');
    $.ajax({
        type : 'POST',
        url : '/games/add',
        dataType : 'json',
        data: {
            GBID: giantbombID,
            listID: listID
        },
        success : function(data){
            if (data.error === true) {
                showErrorModal(data.errorMessage);
            } else {
                if(reloadPage) {
                    location.reload();
                } else {
                    // update list button label/colour
                    $('#gameButton' + giantbombID).html(data.listName + ' <span class="caret"></span>').removeClass().addClass("btn btn-" + data.listStyle + " dropdown-toggle");
                    // display collection status button
                    $('#inCollectionControls' + giantbombID).removeClass("hidden");
                    // enable platform checkboxes
                    $('#platforms' + giantbombID).find('input[type=checkbox]').prop('readonly', false);
                    // if a platform was auto-selected, update checkbox
                    if(data.autoSelectPlatform != null)
                    {
                        $('#platform_' + giantbombID + '_' + data.autoSelectPlatform).prop('checked', true);
                    }
                }
            }
        },
        error : function(XMLHttpRequest, textStatus, errorThrown) {
            showErrorModal('Well shit. Some kind of error gone done happened. Please try again.');
        }
    });
}

/* update game played status */
function changeStatus(giantbombID, statusID) {
    $('#statusButton' + giantbombID).addClass('disabled').html('Saving...');
    $.ajax({
        type : 'POST',
        url : '/games/changeStatus',
        dataType : 'json',
        data: {
            GBID: giantbombID,
            statusID: statusID
        },
        success : function(data){
            if (data.error === true) {
                showErrorModal(data.errorMessage);
            } else {
                $('#statusButton' + giantbombID).html(data.statusName + ' <span class="caret"></span>').removeClass().addClass("btn btn-" + data.statusStyle + " dropdown-toggle");
            }
        },
        error : function(XMLHttpRequest, textStatus, errorThrown) {
            showErrorModal('Well shit. Some kind of error gone done happened. Please try again.');
        }
    });
}

/* display warning modal for removing game */
function showRemoveGameWarning(giantbombID) {
    $('#removeGameButtonPlaceholder').html("<a id='removeGameButton" + giantbombID + "' onclick='javascript:removeFromCollection(" + giantbombID + ");'' class='btn btn-danger'>Remove from Collection</a>");
    $('#removeGameModal').modal();
}

/* remove game from collection */
function removeFromCollection(giantbombID) {
    $('#removeGameButton' + giantbombID).addClass('disabled').html('Removing...');
    $.ajax({
        type : 'POST',
        url : '/games/remove',
        dataType : 'json',
        data: {
            GBID: giantbombID
        },
        success : function(data){
            if (data.error === true) {
                $('#removeGameModal').modal('hide');
                showErrorModal(data.errorMessage);
            } else {
                // reload page to refresh state
                location.reload();
            }
        },
        error : function(XMLHttpRequest, textStatus, errorThrown) {
            showErrorModal('Well shit. Some kind of error gone done happened. Please try again.');
        }
    });
}

/* save progression information */
function saveProgression(giantbombID) {
    $('#progressionSaveButton').addClass('disabled').html('Saving...');
    $.ajax({
        type : 'POST',
        url : '/games/saveProgression',
        dataType : 'json',
        data: {
            GBID: giantbombID,
            currentlyPlaying: $('#currentlyPlayingInput').val(),
            hoursPlayed: $('#hoursPlayedInput').val(),
            dateCompleted: $('#dateCompletedInput').val()
        },
        success : function(data){
            if (data.error === true) {
                showErrorModal(data.errorMessage);
            } else {
                $('#progressionSaveButton').removeClass('disabled').html('Saved');
            }
        },
        error : function(XMLHttpRequest, textStatus, errorThrown) {
            showErrorModal('Well shit. Some kind of error gone done happened. Please try again.');
        }
    });
}