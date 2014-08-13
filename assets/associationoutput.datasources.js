(function($, Symphony) {
	'use strict';

	Symphony.Extensions.AssociationOutput = function() {
		var associativeFields = [],
			context, elements, content, output;

		var init = function() {
			context = $('#ds-context');
			elements = Symphony.Elements.contents.find('[name*="xml_elements"]');
			content = elements.parents('fieldset.settings');
			output = Symphony.Elements.contents.find('fieldset.association-output');

			// Reposition associated output
			content.after(output);

			// Contextually display fields
			output.find('select optgroup').each(displayFields);

			// Contextually display field selector
			elements.on('change.associationoutput', displayFieldSelector);
			elements.trigger('change.associationoutput');
		};

		var displayFields = function() {
			var optgroup = $(this),
				select = optgroup.parents('select'),
				section = optgroup.attr('data-label'),
				field = this.label;

			// Store and hide option group
			associativeFields.push(section + ':' + field);
			optgroup.remove();

			// Display contextual option groups
			context.on('change.associationoutput', function() {
				if(this.value == section) {
					var associations = elements.val(),
						visible = (select.find('optgroup[label="' + field + '"]').length > 0),
						selected = ($.inArray(field, associations) > -1);

					if(selected && !visible) {
						optgroup.clone(true).appendTo(select);
					}
					else if(!selected && visible) {
						select.find('optgroup[label="' + field + '"]').remove();
					}
				}
				else {
					select.find('optgroup[label="' + field + '"][data-label!="' + this.value + '"]').remove();
				}
			});
		};

		var displayFieldSelector = function() {
			var selected = elements.val(),
				section = context.val(),
				visible = false;

			if(selected !== null) {
				$.each(selected, function() {
					if($.inArray(section + ':' + this, associativeFields) > -1) {
						visible = true;
					}
				});
			}

			if(visible === true) {
				output.show();
				context.trigger('change.associationoutput');
			}
			else {
				output.hide();
			}
		};

		// API
		return {
			init: init
		};
	}();

	$(document).on('ready.associationoutput', function() {
		Symphony.Extensions.AssociationOutput.init();
	});

})(window.jQuery, window.Symphony);
