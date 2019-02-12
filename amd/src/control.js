// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Provides the block_quote/control module
 *
 * @package     block_quote
 * @category    output
 * @copyright   2018 David Mudrák <david@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * @module block_quote/control
 */
define(['jquery', 'core/log', 'core/ajax', 'core/templates', 'core/str'], function($, Log, Ajax, Templates, Str) {
    'use strict';

    /**
     * Controls a single Quote block instance contents.
     *
     * @constructor
     * @param {jQuery} region
     */
    function QuoteControl(region) {
        let self = this;
        self.region = region;
    }

    /**
     * Initializes the block controls.
     */
    function init(instanceid) {
        // Log.debug('block_quote/control: initializing controls of the quote block instance ' + instanceid);

        let region = $('[data-region="block_quote-instance-' + instanceid +'"]').first();

        if (!region.length) {
            Log.error('block_quote/control: wrapping region not found!');
            return;
        }

        let control = new QuoteControl(region);
        control.main();
    }

    /**
     * Run the controller.
     *
     */
    QuoteControl.prototype.main = function () {
        let self = this;

        self.addTextForm = self.region.find('[data-control="addform"]').first();
        self.addTextInput = self.addTextForm.find('input').first();
        self.addTextButton = self.addTextForm.find('button').first();
        self.itemsList = self.region.find('ul').first();

        self.initAddFeatures();
        self.initEditFeatures();
    };

    /**
     * Initialize the controls for adding a new quote item.
     *
     * @method
     */
    QuoteControl.prototype.initAddFeatures = function () {
        let self = this;

        self.addTextForm.on('submit', function(e) {
            e.preventDefault();
            self.addNewQuote();
        });

        self.addTextButton.on('click', function() {
            self.addTextForm.submit();
        });
    };

    /**
     * Initialize the controls for modifying existing items.
     *
     * @method
     */
    QuoteControl.prototype.initEditFeatures = function () {
        let self = this;

        self.itemsList.on('click', '[data-item]', function(e) {
            e.preventDefault();
            e.stopPropagation();
            let id = $(e.currentTarget).attr('data-item');
            self.toggleItem(id);
        });

        self.itemsList.on('click', '[data-control="delete"]', function(e) {
            e.preventDefault();
            e.stopPropagation();
            let id = $(e.currentTarget).closest('[data-item]').attr('data-item');
            self.deleteItem(id);
        });
    };

    /**
     * Add a new quote item.
     *
     * @method
     * @return {Deferred}
     */
    QuoteControl.prototype.addNewQuote = function () {
        let self = this;
        let text = $.trim(self.addTextInput.val());

        if (!text) {
            return Str.get_string('placeholdermore', 'block_quote').then(function(text) {
                self.addTextInput.prop('placeholder', text);
                return $.Deferred().resolve();
            });
        }

        self.addTextInput.prop('disabled', true);

        return Ajax.call([{
            methodname: 'block_quote_add_item',
            args: {
                quotetext: text
            }

        }])[0].fail(function(reason) {
            Log.error('block_quote/control: unable to add the item');
            Log.debug(reason);
            self.addTextButton.addClass('btn-danger');
            self.addTextButton.html('<i class="fa fa-exclamation-circle" aria-hidden="true"></i>');
            return $.Deferred().reject();

        }).then(function(response) {
            return Templates.render('block_quote/item', response).fail(function(reason) {
                Log.error('block_quote/control: unable to render the new item:' + reason);
            });

        }).then(function(item) {
            self.itemsList.prepend(item);
            self.addTextInput.val('');
            self.addTextInput.prop('disabled', false);
            self.addTextInput.focus();
            return $.Deferred().resolve();
        });
    };

    /**
     * Toggle the done status of the given item.
     *
     * @method
     * @return {Deferred}
     */
    QuoteControl.prototype.toggleItem = function (id) {
        let self = this;

        if (!id) {
            return $.Deferred().resolve();
        }

        return Ajax.call([{
            methodname: 'block_quote_toggle_item',
            args: {
                id: id
            }

        }])[0].fail(function(reason) {
            Log.error('block_quote/control: unable to toggle the item');
            Log.debug(reason);
            return $.Deferred().reject();

        }).then(function(response) {
            return Templates.render('block_quote/item', response).fail(function(reason) {
                Log.error('block_quote/control: unable to render the new item:' + reason);
            });

        }).then(function(item) {
            self.itemsList.find('[data-item="' + id + '"]').replaceWith(item);
            return $.Deferred().resolve();
        });
    };

    /**
     * Delete the given item.
     *
     * @method
     * @return {Deferred}
     */
    QuoteControl.prototype.deleteItem = function (id) {
        let self = this;

        if (!id) {
            return $.Deferred().resolve();
        }

        return Ajax.call([{
            methodname: 'block_quote_delete_item',
            args: {
                id: id
            }

        }])[0].fail(function(reason) {
            Log.error('block_quote/control: unable to delete the item');
            Log.debug(reason);
            return $.Deferred().reject();

        }).then(function(deletedid) {
            self.itemsList.find('[data-item="' + deletedid + '"]').remove();
            return $.Deferred().resolve();
        });
    };

    return {
        init: init
    };
});