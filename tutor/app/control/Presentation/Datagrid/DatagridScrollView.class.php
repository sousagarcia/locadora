<?php
/**
 * DatagridScrollView
 *
 * @version    1.0
 * @package    samples
 * @subpackage tutor
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class DatagridScrollView extends TPage
{
    private $datagrid;
    
    public function __construct()
    {
        parent::__construct();
        
        // creates one datagrid
        $this->datagrid = new TDataGrid;
        
        // make scrollable and define height
        $this->datagrid->setHeight(300);
        $this->datagrid->makeScrollable();
        
        // create the datagrid columns
        $code       = new TDataGridColumn('code',    'Code',    'right',   70);
        $name       = new TDataGridColumn('name',    'Name',    'left',   180);
        $address    = new TDataGridColumn('address', 'Address', 'left',   180);
        $telephone  = new TDataGridColumn('fone',    'Phone',   'left',   160);
        
        // add the columns to the datagrid
        $this->datagrid->addColumn($code);
        $this->datagrid->addColumn($name);
        $this->datagrid->addColumn($address);
        $this->datagrid->addColumn($telephone);
        
        // creates the datagrid model
        $this->datagrid->createModel();
        
        // wrap the page content using vertical box
        $vbox = new TVBox;
        $vbox->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $vbox->add($this->datagrid);

        parent::add($vbox);
    }
    
    /**
     * Load the data into the datagrid
     */
    function onReload()
    {
        $this->datagrid->clear();
        
        for ($n=1; $n<=40; $n++)
        {
            // add an regular object to the datagrid
            $item = new StdClass;
            $item->code     = $n;
            $item->name     = 'Person name';
            $item->address  = 'Person address';
            $item->fone     = '1111-1111';
            $this->datagrid->addItem($item);
        }
    }
    
    /**
     * shows the page
     */
    function show()
    {
        $this->onReload();
        parent::show();
    }
}
