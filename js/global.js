/* display error modal */
function showErrorModal(error) {
    $('#errorModalMessage').html(error);
    $('#errorModal').modal();
}