function postComment(linkID, commentTypeID) {
    var comment = $('#commentField' + linkID).val();
    var name = $('#nameInput' + linkID).val();
    var email = $('#emailInput' + linkID).val();
    if(comment == "") {
        showErrorModal('You need to type something duder!');
        return;
    }

    $.ajax({
        type : 'POST',
        url : '/user/comment',
        dataType : 'json',
        data: {
            linkID: linkID,
            comment: comment,
            commentTypeID: commentTypeID,
            name: name,
            email: email
        },
        success : function(data){
            if (data.error === true) {
                showErrorModal(data.errorMessage, data.errorProgressURL, data.errorProgressCTA);
            } else {
                var newComment;
                newComment = '<div class="clearfix eventCommentDisplay">';
                newComment += '     <div class="media-left">';
                newComment += '         <img src="/uploads/' + data.profileImage + '" class="tinyIconImage imageShadow" />';
                newComment += '     </div>';
                newComment += '     <div class="media-body eventComment">';
                
                if(data.registeredUser) 
                    newComment += '         <a href="/user/' + data.userID + '"><b>' + data.username + '</b></a>' + data.comment;
                else
                    newComment += '         <b>' + data.username + '</b>' + data.comment;


                newComment += '         <span class="datestamp pull-right">Just now</span>';
                newComment += '     </div>';
                newComment += '</div>';
                $( "#newComment" + linkID).append(newComment);
                $('#commentField' + linkID).val("");
            }
        },
        error : function(XMLHttpRequest, textStatus, errorThrown) {
            showErrorModal('Well nuts. It appears that something didnt go to plan. Please call the coast guard imediately.');
        }
    });
}

function showComments(linkID) {
    $('#hiddenComments' + linkID).removeClass("hidden");
    $('#hiddenCommentLink' + linkID).addClass("hidden");
}