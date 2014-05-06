function postComment(eventID) {
    var comment = $('#commentField' + eventID).val();
    if(comment == "") {
        showErrorModal('You need to type something duder!',false,false);
        return;
    }

    $.ajax({
        type : 'POST',
        url : baseUrl + 'user/comment',
        dataType : 'json',
        data: {
            eventID: eventID,
            comment: comment
        },
        success : function(data){
            if (data.error === true) {
                showErrorModal(data.errorMessage);
            } else {
                var newComment;
                newComment = '<div class="clearfix eventCommentDisplay">';
                newComment += '     <div class="pull-left">';
                newComment += '         <img src="' + baseUrl + 'uploads/' + data.profileImage + '" class="tinyIconImage gameBoxArt" />';
                newComment += '     </div>';
                newComment += '     <div class="media-body eventComment">';
                newComment += '         <a href="' + baseUrl + 'user/' + data.userID + '">' + data.username + '</a></b>' + data.comment;
                newComment += '         <span class="datestamp pull-right">Just now</span>';
                newComment += '     </div>';
                newComment += '</div>';
                $( "#newComment" + eventID).append(newComment);
                $('#commentField' + eventID).val("");
            }
        },
        error : function(XMLHttpRequest, textStatus, errorThrown) {
            showErrorModal('Well nuts. It appears that something didnt go to plan. Please call the coast guard imediately.');
        }
    });
}

function showComments(eventID) {
    $('#hiddenComments' + eventID).removeClass("hidden");
    $('#hiddenCommentLink' + eventID).addClass("hidden");
}