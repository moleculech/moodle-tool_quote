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
 * An example of a HelloWorld Moodle Block script.
 *
 * @package   block_quote
 * @copyright 2019-02-11 Christoph Karlen <christoph.karlen@ffhs.ch>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Misc plugin strings
$string['pluginname'] = 'Simple HelloWorld HTML block';
$string['quote:addinstance'] = 'Add a new simple HelloWorld HTML block';
$string['quote:add'] = 'Add a new Quote';
$string['defaulttitle'] = "This is the default HelloWorld Block Title";

// Config strings
$string['blocktitle'] = "Your block title is";
$string['blockstring'] = "Your message is";
$string['blocksettings'] = "Change block config";


$string['placeholder'] = 'What do you need to do?';
$string['placeholdermore'] = 'Type something here and press the Add button';
$string['privacy:metadata:db:blockquote'] = 'Storage of users todo items.';
$string['privacy:metadata:db:blockquote:done'] = 'Has the item been marked as done.';
$string['privacy:metadata:db:blockquote:timecreated'] = 'When the item was created.';
$string['privacy:metadata:db:blockquote:timemodified'] = 'When the item was last modified.';
$string['privacy:metadata:db:blockquote:quotetext'] = 'Todo item text.';
$string['quote:myaddinstance'] = 'Add a new ToDo block to Dashboard';

// Events and logging
$string['eventitemadded'] = 'A new quote item was added';