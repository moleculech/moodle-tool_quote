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
 * An example of a Quote Moodle Block script.
 *
 * @package   block_quote
 * @copyright 2019-02-11 Christoph Karlen <christoph.karlen@ffhs.ch>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/quiz/lib.php');

class block_quote extends block_base {

    /**
     * @throws coding_exception
     */
    public function init() {
        $this->title = get_string('defaulttitle', 'block_quote');
    }

    /**
     * @return stdObject|string
     * @throws coding_exception
     * @throws moodle_exception
     */
    public function get_content() {
        global $PAGE;

        if ($this->content !== null) {
            return $this->content;
        }

        if (empty($this->instance)) {
            $this->content = '';
            return $this->content;
        }

        $this->content = new \stdClass();

        /**
         * Load the list of persistent quote item models from the database.
         */
        $items = block_quote\item::get_my_quote_items();

        /**
         * Prepare the exporter of the quote items list.
         */
        $list = new block_quote\external\list_exporter([
                'instanceid' => $this->instance->id,
        ], [
                'items' => $items,
                'context' => $this->context,
        ]);


        $output = $PAGE->get_renderer('core');

        /**
         * Render the list using a template and exported data.
         */
        $export = $list->export($output);
        $this->content->text = $output->render_from_template('block_quote/content', $export);

        return $this->content;
    }

    /**
     * Gets Javascript required for the widget functionality.
     */
    public function get_required_javascript() {
        parent::get_required_javascript();
        $this->page->requires->js_call_amd('block_quote/control', 'init', [
                'instanceid' => $this->instance->id
        ]);
    }

    public function instance_allow_multiple() {
        return true;
    }
}
