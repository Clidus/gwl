/* follow or unfollow a user */
function changeFollowingStatus(userID) {
    $('#followButton').addClass('disabled').html('Saving...');
    $.ajax({
        type : 'POST',
        url : '/user/follow',
        dataType : 'json',
        data: {
            followUserID: userID
        },
        success : function(data){
            if (data.error === true) {
                showErrorModal(data.errorMessage, data.errorProgressURL, data.errorProgressCTA);
            } else {
                var label;
                var style;

                if(data.followingUser)
                {
                    label = "Following";
                    style = "success";
                } else {
                    label = "Follow";
                    style = "default";
                }

                // enable button and show its new status
                $('#followButton').html(label).removeClass().addClass("btn btn-" + style + " btn-fullWidth");
            }
        },
        error : function(XMLHttpRequest, textStatus, errorThrown) {
            showErrorModal('Well shit. Some kind of error gone done happened. Please try again.');
        }
    });
}