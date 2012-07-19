<?php
/**
 * CLI Controller Module
 *
 * PHP version 5
 *
 * Copyright (C) Villanova University 2010.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License version 2,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @category VuFind2
 * @package  Controller
 * @author   Chris Hallberg <challber@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/building_a_recommendations_module Wiki
 */
namespace VuFind\CLI\Controller;
use VuFind\XSLT\Importer;

/**
 * This controller handles various command-line tools
 *
 * @category VuFind2
 * @package  Controller
 * @author   Chris Hallberg <challber@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/building_a_recommendations_module Wiki
 */
class ImportController extends AbstractBase
{
    /**
     * XSLT Import Tool
     *
     * @return void
     */
    public function importXslAction()
    {
        // Parse switches:
        $this->consoleOpts->addRules(
            array('test-only' => 'Use test mode', 'index-s' => 'Solr index to use')
        );
        $testMode = $this->consoleOpts->getOption('test-only') ? true : false;
        $index = $this->consoleOpts->getOption('index');
        if (empty($index)) {
            $index = 'Solr';
        }

        // Display help message if parameters missing:
        $argv = $this->consoleOpts->getRemainingArgs();
        if (!isset($argv[1])) {
            echo "Usage: import-xsl.php [--test-only] [--index <type>] XML_file "
                . "properties_file\n"
                . "\tXML_file - source file to index\n"
                . "\tproperties_file - import configuration file\n"
                . "If the optional --test-only flag is set, transformed XML will "
                . "be displayed\non screen for debugging purposes, but it will "
                . "not be indexed into VuFind.\n\n"
                . "If the optional --index parameter is set, it must be followed by "
                . "the name of\na class for accessing Solr; it defaults to the "
                . "standard Solr class, but could\nbe overridden with, for example, "
                . "SolrAuth to load authority records.\n\n"
                . "Note: See vudl.properties and ojs.properties for configuration "
                . "examples.\n";
            return $this->getFailureResponse();
        }

        // Try to import the document if successful:
        try {
            Importer::save($argv[0], $argv[1], $index, $testMode);
        } catch (\Exception $e) {
            echo "Fatal error: " . $e->getMessage() . "\n";
            return $this->getFailureResponse();
        }
        if (!$testMode) {
            echo "Successfully imported {$argv[0]}...\n";
        }
        return $this->getSuccessResponse();
    }
}
