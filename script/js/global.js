/* display error modal */
function showErrorModal(error,url,cta) {
    $('#errorModalMessage').html(error);
    
    // If a progession URL (eg: "Login") is defined, change the button text and remove the dismissal property so that Bootstrap doesn't override the href
    if(url){
      $('#errorModalDismiss').attr("href",url);
      $('#errorModalDismiss').text(cta);
      $('#errorModalDismiss').attr("data-dismiss","false");
    }
    
    $('#errorModal').modal();
}