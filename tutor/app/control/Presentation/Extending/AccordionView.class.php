<?php
/**
 * ContainerNotebookView
 *
 * @version    1.0
 * @package    samples
 * @subpackage tutor
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class AccordionView extends TPage
{
    /**
     * Class constructor
     * Creates the page
     */
    function __construct()
    {
        parent::__construct();
        
        // creates the accordion
        $accordion = new TAccordion;
        $accordion->style = "width:550px";
        
        // creates the containers for each accordion page
        $page1 = new TTable;
        $page2 = new TPanel(370,180);
        $page3 = new TTable;
        
        // adds two pages in the accordion
        $accordion->appendPage('Basic data', $page1);
        $accordion->appendPage('Other data', $page2);
        $accordion->appendPage('Other note', $page3);
        
        // create the form fields
        $field1 = new TEntry('field1');
        $field2 = new TEntry('field2');
        $field3 = new TEntry('field3');
        $field4 = new TEntry('field4');
        $field5 = new TEntry('field5');
        
        $field6 = new TEntry('field6');
        $field7 = new TEntry('field7');
        $field8 = new TEntry('field8');
        $field9 = new TEntry('field9');
        $field10= new TEntry('field10');
        
        // change the size for some fields
        $field1->setSize(100);
        $field2->setSize(80);
        $field3->setSize(150);
        
        $field6->setSize(80);
        $field7->setSize(80);
        $field8->setSize(80);
        $field9->setSize(80);
        $field10->setSize(80);
        
        ## fields for the page 1 ##
        
        // add a row for a label
        $row=$page1->addRow();
        $cell=$row->addCell(new TLabel('<b>Table Layout</b>'));
        $cell->valign = 'top';
        $cell->colspan=2;
        
        // adds a row for a field
        $row=$page1->addRow();
        $row->addCell(new TLabel('Field1:'));
        $row->addCell($field1);
        
        // adds a row for a field
        $row=$page1->addRow();
        $row->addCell(new TLabel('Field2:'));
        $cell = $row->addCell($field2);
        $cell->colspan=3;
        
        // adds a row for a field
        $row=$page1->addRow();
        $row->addCell(new TLabel('Field3:'));
        $cell = $row->addCell($field3);
        $cell->colspan=3;
        
        // adds a row for a field
        $row=$page1->addRow();
        $row->addCell(new TLabel('Field4:'));
        $row->addCell($field4);
        
        // adds a row for a field
        $row=$page1->addRow();
        $row->addCell(new TLabel('Field5:'));
        $row->addCell($field5);
        
        
        ## fields for the page 2 ##
        
        $page2->put(new TLabel('<b>Panel Layout</b>'), 4, 4);
        $page2->put(new TLabel('Field6'),  20,  30);
        $page2->put(new TLabel('Field7'),  50,  60);
        $page2->put(new TLabel('Field8'),  80,  90);
        $page2->put(new TLabel('Field9'), 110, 120);
        $page2->put(new TLabel('Field10'),140, 150);
        
        $page2->put($field6, 120,  30);
        $page2->put($field7, 150,  60);
        $page2->put($field8, 180,  90);
        $page2->put($field9, 210, 120);
        $page2->put($field10,240, 150);
        
        
        ## fields for the page 3 ##
        
        // creates the notebook
        $subnotebook = new TNotebook(250, 160);
        $subnotebook->appendPage('new page1', new TLabel('test1'));
        $subnotebook->appendPage('new page2', new TText('test2'));
        
        $row = $page3->addRow();
        $row->addCell($subnotebook);
        
        // wrap the page content using vertical box
        $vbox = new TVBox;
        $vbox->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $vbox->add($accordion);

        parent::add($vbox);
    }
}
?>