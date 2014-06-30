<?php

require_once EXTENSIONS . '/association_output/data-sources/datasource.associations.php';

Class extension_association_output extends Extension
{
    private static $provides = array();

    public static function registerProviders()
    {
        self::$provides = array(
            'data-sources' => array(
                'AssociationOutput' => AssociationOutput::getName()
            )
        );

        return true;
    }

    public static function providerOf($type = null)
    {
        self::registerProviders();

        if (is_null($type)) {
            return self::$provides;
        }

        if (!isset(self::$provides[$type])) {
            return array();
        }

        return self::$provides[$type];
    }

    public function getSubscribedDelegates()
    {
        return array(
            array(
                'page'      => '/frontend/',
                'delegate'  => 'DataSourcePostExecute',
                'callback'  => 'appendAssociatedEntries'
            ),
            array(
                'page' => '/backend/',
                'delegate' => 'InitaliseAdminPageHead',
                'callback' => 'appendAssociationSelector'
            ),
            array(
                'page'      => '/blueprints/datasources/',
                'delegate'  => 'DatasourcePreCreate',
                'callback'  => 'saveDataSource'
            ),
            array(
                'page'      => '/blueprints/datasources/',
                'delegate'  => 'DatasourcePreEdit',
                'callback'  => 'saveDataSource'
            )            
        );
    }

    /**
     * Append associated entries to the XML output
     */
    public function appendAssociatedEntries($context)
    {
        $datasource = $context['datasource'];
        $xml = $context['xml'];
        $parameters = $context['param_pool'];

        if (!empty($datasource->dsParamINCLUDEDASSOCIATIONS)) {
            foreach ($datasource->dsParamINCLUDEDASSOCIATIONS as $name => $settings) {
                $transcriptions = array();
                $entry_ids = array_unique($parameters['ds-' . $datasource->dsParamROOTELEMENT . '.' . $name]);

                if (!empty($entry_ids)) {
                    if (!is_numeric($entry_ids[0])) {
                        $values = "'" . implode($entry_ids, "', '") . "'";
                        $data = Symphony::Database()->fetch(
                            sprintf(
                                "SELECT `entry_id`, `handle`
                                FROM sym_entries_data_%d
                                WHERE `handle` IN (%s) or `value` IN (%s)",
                                $settings['field_id'], $values, $values
                            )
                        );

                        foreach ($data as $transcription) {
                            $transcriptions[$transcription['handle']] = $transcription['entry_id'];
                        }
                        
                        $entry_ids = array_values($transcriptions);
                    } 

                    $associated_xml = $this->fetchAssociatedEntries($settings, $entry_ids);
                    $associated_items = $this->groupAssociatedEntries($associated_xml);
                    $this->includeAssociatedEntries($xml, $associated_items, $name, $transcriptions);
                }
            }
        }
    }

    /**
     * Fetch associated entries using a custom Data Source
     *
     * @param array $settings
     *  An array of field settings
     * @param array $entry_ids
     *  An array of associated entry ids
     * @return XMLElement
     */
    private function fetchAssociatedEntries($settings, $entry_ids = array())
    {
        $datasource = DatasourceManager::create('associations');
        $datasource->dsParamSOURCE = $settings['section_id'];
        $datasource->dsParamFILTERS['system:id'] = implode($entry_ids, ', ');
        $datasource->dsParamINCLUDEDELEMENTS = $settings['elements'];

        return $datasource->execute();
    }

    /**
     * Group associated entries by id
     *
     * @param XMLELement $associated_xml
     *  The Data Source output
     * @return array
     */
    private function groupAssociatedEntries($associated_xml)
    {
        $associated_items = array();

        foreach($associated_xml->getChildren() as $entry) {
            if ($entry->getName() === 'entry') {
                $associated_items[$entry->getAttribute('id')] = $entry->getChildren();
            }
        }

        return $associated_items;
    }

    /**
     * Attach associated entries to the existing Data Source output
     *
     * @param XMLElement $xml
     *  The existing XML
     * @param array $associated_items
     *  An array linking entry ids to XML entries
     * @param string $name
     *  The associative field name
     * @param array $transcriptions
     *  An array mapping field handles to entry ids
    */
    private function includeAssociatedEntries(&$xml, $associated_items, $name, $transcriptions)
    {
        $entries = $xml->getChildren();
        if (!empty($entries)) {
            foreach ($entries as $entry) {
                $fields = $entry->getChildren();

                if ($entry->getName() === 'entry' && !empty($fields)) {
                    foreach ($fields as $field) {
                        $items = $field->getChildren();

                        if ($field->getName() === $name && !empty($items)) {
                            foreach ($items as $item) {
                                $id = $item->getAttribute('id');

                                if (empty($id)) {
                                    $handle = $item->getAttribute('handle');
                                    $id = $transcriptions[$handle];
                                }

                                $association = $associated_items[$id];
                                if(!empty($association)) {
                                    $item->replaceValue('');
                                    $item->setChildren($associated_items[$id]);
                                    $item->setAttribute('id', $id);
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Save included associations.
     *
     * @param mixed $context
     *  Delegate context including string contents of the data souce PHP file
     */
    public function saveDataSource($context)
    {
        $contents = $context['contents'];
        $elements = $_POST['fields']['includedassociations'];

        if (isset($elements)) {

            // Prepare associations
            $associations = array();
            foreach ($elements as $element) {
                $element = explode('|#|', $element);
                $associations[$element[0]]['elements'][] = $element[1];
            }

            // Prepare variable string
            $included = var_export($associations, true);
            $included = str_replace("  ", "    ", $included);
            $included = str_replace("array (", "array(", $included);
            $included = str_replace(" => \n    array", " => array", $included);
            $included = str_replace(" => \n        array", " => array", $included);
            $included = preg_replace("/\d+ => /", "", $included);
            $included = preg_replace("/,(\n)( +)?\)/", "$1$2)", $included);
            $included = str_replace("\n    ", "\n        ", $included);
            $included = preg_replace("/\)$/", "    )", $included);

            // Store included associations
            $contents = str_replace(
                "<!-- INCLUDED ELEMENTS -->",
                "<!-- INCLUDED ELEMENTS -->\n    public \$dsParamINCLUDEDASSOCIATIONS = $included;",
                $contents
            );

            $context['contents'] = $contents;
        }
    }

    /**
     * Append interface to select association output to Data Source editor.
     *
     * @param mixed $context
     *  Delegate context including page object
     */
    public function appendAssociationSelector($context)
    {
        $callback = Symphony::Engine()->getPageCallback();

        if ($callback['driver'] == 'blueprintsdatasources' && $callback['context']['page'] !== 'index') {
            Administration::instance()->Page->addScriptToHead(URL . '/extensions/association_output/assets/associationoutput.datasources.js');
        }
    }
}
