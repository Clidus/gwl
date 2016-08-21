/* delete blog post */
function deleteBlogPost(ID) {
    $.ajax({
        type : 'POST',
        url : '/admin/blog/delete',
        dataType : 'json',
        data: {
            postID: ID
        },
        success : function(data){
            if (data.error === true) {
                showErrorModal(data.errorMessage,false,false);
            } else {
                window.location = '/admin/blog/edit/1';
            }
        },
        error : function(XMLHttpRequest, textStatus, errorThrown) {
            showErrorModal('Well shit. Some kind of error gone done happened. Please try again.');
        }
    });
}