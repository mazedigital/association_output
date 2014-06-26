<?php

require_once TOOLKIT . '/class.datasource.php';

class datasourceassociations extends SectionDatasource
{
    public $dsParamROOTELEMENT = 'associations';
    public $dsParamORDER = 'desc';
    public $dsParamPAGINATERESULTS = 'yes';
    public $dsParamLIMIT = '20';
    public $dsParamSTARTPAGE = '1';
    public $dsParamREDIRECTONEMPTY = 'no';
    public $dsParamASSOCIATEDENTRYCOUNTS = 'no';
    public $dsParamSORT = 'system:id';
    public $dsParamSOURCE = 0;
    public $dsParamFILTERS = array();
    public $dsParamINCLUDEDELEMENTS = array();

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