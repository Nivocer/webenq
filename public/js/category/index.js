/**
 * Saves the state of the given element. Must implement an action for
 * any element that can be saved.
 * 
 * @param event
 * @param reload Boolean that indicates reloading of the whole page
 */
function saveState(event, reload)
{
	if ($(event.target).hasClass('category')) {
		//alert(event.target.className);
		$('body').addClass('loading');
		 var $data=$(event.target).sortable('toArray');
		 $.post(baseUrl + '/category/order', {category: $.toJSON($data)}, function() {
			 $('body').removeClass('loading');
		 }); 
	}
};