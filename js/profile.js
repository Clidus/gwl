function postComment(eventID) {
    var comment = $('#commentField' + eventID).val();
    if(comment == "") {
        showErrorModal('You need to type something duder!');
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
                alert("Boom!");
            }
        },
        error : function(XMLHttpRequest, textStatus, errorThrown) {
            showErrorModal('Well nuts. It appears that something didnt go to plan. Please call the coast guard imediately.');
        }
    });
}