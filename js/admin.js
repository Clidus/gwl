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
                showErrorModal(data.errorMessage,false,false);
            } else {
                window.location = baseUrl + 'admin/blog/edit';
            }
        },
        error : function(XMLHttpRequest, textStatus, errorThrown) {
            showErrorModal('Well shit. Some kind of error gone done happened. Please try again.');
        }
    });
}