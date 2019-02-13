<?php
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
 * Provides {@link block_quote\external\add_item} trait.
 *
 * @package     block_quote
 * @category    external
 * @copyright   2018 David Mudrák <david@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace block_quote\external;

defined('MOODLE_INTERNAL') || die();

use block_quote\event\item_added;
use block_quote\item;
use context_user;
use external_function_parameters;
use external_value;

global $CFG;
require_once($CFG->libdir.'/externallib.php');
/**
 * Trait implementing the external function block_quote_add_item.
 * This trait acts as an ajax endpoint.
 * It checks the passed values, the context and saves the provided string to the db
 */
trait add_item {
    /**
     * Describes the structure of parameters for the function.
     *
     * @return external_function_parameters
     */
    public static function add_item_parameters() {
        return new external_function_parameters([
                'quotetext' => new external_value(PARAM_RAW, 'Item text describing what is to be done'),
        ]);
    }

    /**
     * Adds a new quote item.
     * @param $quotetext
     * @return \stdClass
     * @throws \coding_exception
     * @throws \core\invalid_persistent_exception
     * @throws \invalid_parameter_exception
     * @throws \required_capability_exception
     * @throws \restricted_context_exception
     */
    public static function add_item($quotetext) {
        /**
         * Fetch the globally used variables
         * @see
         */
        global $USER, $PAGE;

        /**
         * The context is validated
         * @todo provide more information
         */
        $context = context_user::instance($USER->id);
        self::validate_context($context);

        /**
         * Is the current user allowed to do this action
         */
        require_capability('block/quote:add', $context);

        /**
         * Remove HTML and PHP Tags
         */
        $quotetext = strip_tags($quotetext);

        /**
         * The validate_parameters function checks if the provided data has the right format
         */
        $params = self::validate_parameters(self::add_item_parameters(), compact('quotetext'));

        /**
         * Create a new quote item
         */
        $item = new item(null, (object) $params);
        $item->create();

        /**
         * Log that the user has created an new entry
         */
        self::log($item,$context);

        /**
         * Brings the item in the correct format to return to the browser
         */
        $itemexporter = new item_exporter($item, ['context' => $context]);
        return $itemexporter->export($PAGE->get_renderer('core'));
    }

    public static function log($item,$context) {
        $event = item_added::create(array(
                'objectid' => $item->get('id'),
                'context' => $context
        ));
        $event->trigger();
    }

    /**
     * Returns description of add_item result values.
     * Describes the webservice to the api consumer
     *
     * @return \external_single_structure
     */
    public static function add_item_returns() {
        return item_exporter::get_read_structure();
    }
}