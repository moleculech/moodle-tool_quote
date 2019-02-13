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
 * Unit test for search indexing.
 *
 * @package block_html
 * @copyright 2017 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_quote;
defined('MOODLE_INTERNAL') || die();

use \block_quote\external\api;
use \block_quote\item as quoteitem;
use \block_quote\external\add_item;

/**
 * Unit test for search indexing.
 *
 * @package block_quote
 * @copyright 2019 Christoph Karlen
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_quote_crud_testcase extends \advanced_testcase {
    use add_item;

    /** @var \testing_data_generator $datagenerator */
    protected $datagenerator;

    /** @var \stdClass $user */
    protected $user;

    protected function init() {
        /**
         * Reset the database after test
         */
        $this->resetAfterTest(true);

        /**
         * Get a new data generator
         */
        $this->datagenerator = $this->getDataGenerator();

        /**
         * Create a user
         */
        $this->user();
    }

    /**
     * @test
     *
     * @throws \coding_exception
     * @throws \core\invalid_persistent_exception
     * @throws \invalid_parameter_exception
     * @throws \required_capability_exception
     * @throws \restricted_context_exception
     */
    public function test_add_item() {
        $this->init();

        /**
         * Create a quote api class and add a new quote item
         */
        $api = new api();
        $quotetext = self::word();
        $result = $api::add_item($quotetext);

        /**
         * Check if the add_item method returns the correct data
         */
        $this->assertEquals($quotetext, $result->quotetext);

        /**
         * Check if the the new quote is stored in the database
         */
        $quoteitem = quoteitem::get_my_quote_item($result->id);
        $this->assertEquals($quoteitem->get('quotetext'), $result->quotetext);
    }

    /**
     * @test
     *
     * @throws \coding_exception
     * @throws \core\invalid_persistent_exception
     * @throws \invalid_parameter_exception
     * @throws \required_capability_exception
     * @throws \restricted_context_exception
     */
    public function test_toggle_item() {
        $this->init();

        /**
         * Create a quote api class and add a new quote item
         */
        $quotetext = self::word();
        $api = new api();
        $result = $api::add_item($quotetext);

        /**
         * Check if the item is marked as not done
         */
        $quoteitem = quoteitem::get_my_quote_item($result->id);
        $this->assertEquals($quoteitem->get('done'), 0);

        /**
         * Toggle the quote item (mark it as done)
         */
        $api::toggle_item($result->id);

        /**
         * Check if the item was toggled
         */
        $quoteitem = quoteitem::get_my_quote_item($result->id);
        $this->assertEquals($quoteitem->get('done'), 1);
    }

    /**
     * @param int $roleid
     * @throws \coding_exception
     */
    protected function user($roleid = 1) {
        /**
         * Generate a random user
         *
         * @var \stdClass $user
         */
        $user = $this->datagenerator->create_user();

        /**
         * Log the user in (Set the $USER global variable)
         */
        $this->setUser($user);

        /**
         * Assign a role to the current user
         * 1 Manager / Admin user
         * 2
         * 3
         * 4 Teacher
         * 5 Student
         *
         * @see mld_role table
         */
        $this->datagenerator->role_assign($roleid, $user->id, false);
    }

    /**
     * Returns a random word
     *
     * @param int $len
     * @return bool|string
     */
    protected static function word($len = 10) {
        $word = array_merge(range('a', 'z'), range('A', 'Z'));
        shuffle($word);
        return substr(implode($word), 0, $len);
    }
}