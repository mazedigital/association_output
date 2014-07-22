<?php

require_once TOOLKIT . '/class.datasource.php';

class datasourceassociations extends SectionDatasource
{
    public $dsParamROOTELEMENT = 'associations';
    public $dsParamPAGINATERESULTS = 'no';
    public $dsParamSTARTPAGE = '1';
    public $dsParamREDIRECTONEMPTY = 'no';
    public $dsParamSORT = 'system:id';
    public $dsParamASSOCIATEDENTRYCOUNTS = 'no';
    public $dsParamINCLUDEDELEMENTS = array();
    public $dsParamSOURCE = 0;
    public $dsParamHTMLENCODE = '';

    public function about()
    {
        return array(
            'name' => 'Associations'
        );
    }

    public function getSource()
    {
        return $this->dsParamSOURCE;
    }

    public function allowEditorToParse()
    {
        return false;
    }
}