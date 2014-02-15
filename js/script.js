/* add/update game status in collection */
function addGame(giantbombID, detailUrl, currentListID, listID) {
    $.ajax({
        type : 'POST',
        url : baseUrl + 'games/add',
        dataType : 'json',
        data: {
            gbID: giantbombID,
            apiDetail: detailUrl,
            listID: listID
        },
        success : function(data){
            if (data.error === true) {
                alert(data.errorMessage);
            } else {
                // if not in collection (listID == 0), redirect as page needs to change
                if(currentListID != null && currentListID == 0) {
                    window.location = document.URL;
                } else {
                    switch(listID)
                    {
                        case 1:
                            buttonLabel = "Own";
                            buttonStyle = "success";
                            break;
                        case 2:
                            buttonLabel = "Want";
                            buttonStyle = "warning";
                            break;
                        case 3:
                            buttonLabel = "Borrowed";
                            buttonStyle = "info";
                            break;
                        case 4:
                            buttonLabel = "Lent";
                            buttonStyle = "danger";
                            break;
                        case 5:
                            buttonLabel = "Played";
                            buttonStyle = "primary";
                            break;
                    }
                    $('#gameButton' + giantbombID).html(buttonLabel + ' <span class="caret"></span>');
                    $('#gameButton' + giantbombID).removeClass();
                    $('#gameButton' + giantbombID).addClass("btn btn-" + buttonStyle + " dropdown-toggle");
                    $('#inCollectionControls' + giantbombID).removeClass("hidden");
                }
            }
        },
        error : function(XMLHttpRequest, textStatus, errorThrown) {
            alert('Error');
        }
    });
}

/* update game played status */
function changeStatus(giantbombID, statusID) {
    $.ajax({
        type : 'POST',
        url : baseUrl + 'games/changeStatus',
        dataType : 'json',
        data: {
            gbID: giantbombID,
            statusID: statusID
        },
        success : function(data){
            if (data.error === true) {
                alert(data.errorMessage);
            } else {
                switch(statusID)
                {
                    case 1:
                        buttonLabel = "Unplayed";
                        buttonStyle = "default";
                        break;
                    case 2:
                        buttonLabel = "Unfinished";
                        buttonStyle = "warning";
                        break;
                    case 3:
                        buttonLabel = "Complete";
                        buttonStyle = "success";
                        break;
                    case 4:
                        buttonLabel = "Uncompletable";
                        buttonStyle = "primary";
                        break;
                }
                $('#statusButton' + giantbombID).html(buttonLabel + ' <span class="caret"></span>');
                $('#statusButton' + giantbombID).removeClass();
                $('#statusButton' + giantbombID).addClass("btn btn-" + buttonStyle + " dropdown-toggle");
            }
        },
        error : function(XMLHttpRequest, textStatus, errorThrown) {
            alert('Error');
        }
    });
}

/* update game played status */
function removeFromCollection(giantbombID) {
    $.ajax({
        type : 'POST',
        url : baseUrl + 'games/remove',
        dataType : 'json',
        data: {
            gbID: giantbombID
        },
        success : function(data){
            if (data.error === true) {
                alert(data.errorMessage);
            } else {
                // redirect to same page to refresh state
                window.location = document.URL;
            }
        },
        error : function(XMLHttpRequest, textStatus, errorThrown) {
            alert('Error');
        }
    });
}

/* delete blog post */
function deleteBlogPost(ID) {
    $.ajax({
        type : 'POST',
        url : baseUrl + 'admin/deleteBlogPost',
        dataType : 'json',
        data: {
            postID: ID
        },
        success : function(data){
            if (data.error === true) {
                alert(data.errorMessage);
            } else {
                window.location = baseUrl + 'admin/blog/edit';
            }
        },
        error : function(XMLHttpRequest, textStatus, errorThrown) {
            alert('Error');
        }
    });
}