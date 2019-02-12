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
 * block_quote data generator.
 *
 * @package    block_quote
 * @category   test
 * @copyright  2019 Christoph Karlen
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * block_quote data generator class.
 *
 * @package    block_quote
 * @category   test
 * @copyright  2019 Christoph Karlen
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_quote_generator extends testing_block_generator {

    /**
     * @var int keep track of how many quoteitems have been created.
     */
    protected $quotecount = 0;

    /**
     * To be called from data reset code only,
     * do not use in tests.
     * @return void
     */
    public function reset() {
        $this->quotecount = 0;
        parent::reset();
    }

    public function create_instance($record = null, $options = array()) {
        $record = (object)(array)$record;

        if (!isset($record->usermodified)) {
            $record->usermodified = 2;
        }
        if (!isset($record->timecreated)) {
            $record->timecreated = time();
        }
        if (!isset($record->timemodified)) {
            $record->timemodified = time();
        }
        if (!isset($record->done)) {
            $record->done = 0;
        }

        return parent::create_instance($record, (array)$options);
    }

}
