(function($, Symphony) {
	'use strict';

	$(document).on('ready.associationoutput', function() {
		var context = $('#ds-context'),
			elements = Symphony.Elements.contents.find('[name*="xml_elements"]'),
			content = elements.parents('fieldset.settings'),
			output = Symphony.Elements.contents.find('fieldset.association-output'),
			fromExtensions;

		// Hide Data Source interface from select
		context.find('[data-context="association-output"]').remove();
		fromExtensions = context.find('[data-label="from_extensions"]');
		if(fromExtensions.find('option').length == 0) {
			fromExtensions.remove();
		}

		// Reposition associated output
		content.after(output);

		// Contextually display fields
		output.find('select optgroup').each(function() {
			var optgroup = $(this),
				select = optgroup.parents('select'),
				section = optgroup.attr('data-label');

			// Remove option groups
			optgroup.remove();

			// Display contextual option groups
			context.on('change.associationoutput', function() {
				if(this.value == section) {
					var options = optgroup.clone(true);

					select.find('optgroup[data-label!="' + options.attr('data-label') + '"]').remove();
					select.append(options);
				}
			});
		});

		// Set context
		context.trigger('change.associationoutput');

		// Contextually display field selector
		elements.on('change.associationoutput', function() {
			var selected = elements.val(),
				visible = false;

			if(selected !== null) {
				$.each(selected, function() {
					var associative = output.find('optgroup[label="' + this + '"]');

					if(associative.length) {
						visible = true;
					}
				});
			}

			if(visible === true) {
				output.show();
			}
			else {
				output.hide();
			}
		}).trigger('change.associationoutput');
	});

})(window.jQuery, window.Symphony);
