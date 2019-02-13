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
 * COMPONENT External functions unit tests
 *
 * @package    block_quote
 * @category   external
 * @copyright  2019 Christoph Karlen
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_quote;
defined('MOODLE_INTERNAL') || die();

global $CFG;

use \block_quote\external\api;
use \block_quote\external\add_item;

require_once($CFG->dirroot . '/webservice/tests/helpers.php');

class block_quote_external_testcase extends \externallib_advanced_testcase {
    use add_item;

    /** @var \testing_data_generator $datagenerator */
    protected $datagenerator;

    private function init() {
        /**
         * Reset the database after test
         */
        $this->resetAfterTest(true);

        /**
         * Get a new data generator
         */
        $this->datagenerator = $this->getDataGenerator();
    }

    /**
     * @test
     * block_quote_add_item
     * @throws \coding_exception
     * @throws \core\invalid_persistent_exception
     * @throws \invalid_parameter_exception
     * @throws \invalid_response_exception
     * @throws \required_capability_exception
     * @throws \restricted_context_exception
     */
    public function test_add_item() {

        $this->init();

        /**
         * Generate a quote block with the testing_data_generator
         */
        $block = $this->datagenerator->create_block('quote');

        /**
         * Set the required capabilities by the external function
         */
        $contextid = \context_block::instance($block->id)->id;
        /** @todo this is not working on the current user */
        $roleid = $this->assignUserCapability('moodle/block/quote:add', $contextid);

        /**
         * Create User
         */
        $this->user();

        /**
         *
         */
        $quotetext = self::word();
        $returnvalue = api::add_item($quotetext);

        /**
         * We need to execute the return-values-cleaning-process
         * to simulate the web service server
         *
         * The add_item_returns describes the format of the api return values
         */
        $returnvalue = api::clean_returnvalue(api::add_item_returns(), $returnvalue);

        // Some PHPUnit assert
        $this->assertEquals($quotetext, $returnvalue['quotetext']);

        /**
         * Call without required capability should return an 'required_capability_exception' exception
         */
        $this->unassignUserCapability('moodle/CAPABILITYNAME', $contextid, $roleid);
        $this->user(2); // @todo the removal of the UserCapability should be enough, remove this line and make it work
        $this->expectException(\required_capability_exception::class);
        $returnvalue = api::add_item($quotetext);
    }

    /**
     * @throws \dml_exception
     * @throws \moodle_exception
     */
    public function test_add_item_to_course_page() {
        global $DB;
        $this->init();
        $newblock = 'quote';

        /**
         * Create new course
         */
        $course = $this->datagenerator->create_course();
        $course->format = course_get_format($course)->get_format();

        /**
         * Create new user with student role
         * Enrol the newly created user
         */
        $user = $this->datagenerator->create_user();
        $studentrole = $DB->get_record('role', ['shortname' => 'student']);
        $this->datagenerator->enrol_user($user->id, $course->id, $studentrole->id);
        $this->setUser($user);

        /**
         * Create a new moodle page
         */
        $page = new \moodle_page();
        $page->set_context(\context_course::instance($course->id));
        $page->set_pagelayout('course');
        $page->set_pagetype('course-view-' . $course->format);

        $page->blocks->load_blocks();
        $page->blocks->add_block_at_end_of_default_region($newblock);

        /**
         * Check if the new block was created
         */
        $result = \core_block_external::get_course_blocks($course->id);

        // We need to execute the return values cleaning process to simulate the web service server.
        $result = \external_api::clean_returnvalue(\core_block_external::get_course_blocks_returns(), $result);

        $this->assertCount(1, $result['blocks']);
        $this->assertEquals($newblock, $result['blocks'][0]['name']);
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