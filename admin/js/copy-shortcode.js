/**
 * Copies text to clipboard on click
 *
 * @link https://timeslotplugins.com
 *
 * @package Time Slot
 * @since 1.0.0
 * 
 */

jQuery(function() {

	var tsCopyText = document.querySelector('.ts-copytext');
	
	tsCopyText.addEventListener('click', function(){

		var tsCopySaved = tsCopyText.dataset.copytext;
		var tsCopyTooltip = document.createElement('span');
		tsCopyTooltip.setAttribute('role', 'alert');
		tsCopyTooltip.textContent = 'Copied!';
	
		navigator.clipboard.writeText(tsCopySaved);
		tsCopyText.after(tsCopyTooltip);
		setTimeout( function() {
			tsCopyTooltip.remove();
		}, 1500);

	})

});