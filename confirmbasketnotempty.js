$(document).ready(function(){
    var link_intern = false;
    document.addEventListener("click", function(e) {
	if (e.currentTarget.activeElement.nodeName.toLowerCase() === 'a'  && document.location.host === e.currentTarget.activeElement.host || e.target.href.replace(/[\s\;]/g,"") === 'javascript:void(0)' || e.currentTarget.activeElement.nodeName.toLowerCase() === 'button' || e.currentTarget.activeElement.nodeName.toLowerCase() === 'input' || e.currentTarget.activeElement.nodeName.toLowerCase() === 'body') {
	    link_intern = true;
	}
    }, true);
    $(window).bind('beforeunload', function(e) {
	if(link_intern)
	    return;
	if(e.srcElement.activeElement.className.indexOf("redirect") > -1)
	    return;
	if(parseInt($('.ajax_cart_quantity').text(), 10) == 0)
	    return;
	var e = e || window.event;
	// For IE and Firefox prior to version 4
	if (e) {
	    e.returnValue = $('#confirmbasketnotempty').text();
	}
	return $('#confirmbasketnotempty').text();
    });
});
