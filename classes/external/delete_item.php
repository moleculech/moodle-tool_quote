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
 * Provides {@link block_quote\external\delete_item} trait.
 *
 * @package     block_quote
 * @category    external
 * @copyright   2018 David Mudrák <david@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace block_quote\external;
defined('MOODLE_INTERNAL') || die();
use block_quote\item;
use context_user;
use external_function_parameters;
use external_value;
use invalid_parameter_exception;

global $CFG;
require_once($CFG->libdir.'/externallib.php');
/**
 * Trait implementing the external function block_quote_delete_item.
 */
trait delete_item {
    /**
     * Describes the structure of parameters for the function.
     *
     * @return external_function_parameters
     */
    public static function delete_item_parameters() {
        return new external_function_parameters([
                'id' => new external_value(PARAM_INT, 'ID of the quote item'),
        ]);
    }

    /**
     * Toggle the done status of the item.
     *
     * @param $id
     * @return mixed
     * @throws \coding_exception
     * @throws \required_capability_exception
     * @throws \restricted_context_exception
     * @throws invalid_parameter_exception
     */
    public static function delete_item($id) {
        /**
         * Fetch the globally used variables
         * @see
         */
        global $USER;


        $context = context_user::instance($USER->id);
        self::validate_context($context);

        /**
         * Is the current user allowed to do this action
         */
        require_capability('block/quote:myaddinstance', $context);

        self::validate_parameters(self::delete_item_parameters(), compact('id'));

        /**
         * Find the corresponding quote item
         */
        $item = item::get_record(['usermodified' => $USER->id, 'id' => $id]);
        if (!$item) {
            throw new invalid_parameter_exception('Unable to find your quote item with that ID');
        }

        /**
         * Delete the requested db entry
         */
        $item->delete();
        return $id;
    }

    /**
     * Describes the structure of the function return value.
     *
     * @return \external_description
     */
    public static function delete_item_returns() {
        return new external_value(PARAM_INT, 'ID of the removed quote item');
    }
}