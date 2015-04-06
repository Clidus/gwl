$(document).ready(function() {
    $('.progress-bar').each(function() {
        var bar = $(this);
        bar.css('width', bar.attr("data-percentage")+'%');
    });
});