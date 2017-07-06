<?php
/**
 * FormDynamicFilterView
 *
 * @version    1.0
 * @package    samples
 * @subpackage tutor
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class FormDynamicFilterView extends TPage
{
    private $form;
    
    /**
     * Class constructor
     * Creates the page
     */
    function __construct()
    {
        parent::__construct();
        
        // create the form using TQuickForm class
        $this->form = new TQuickForm('form_dynamic_filter');
        
        // create the notebook
        $notebook = new TNotebook(530, 160);
        
        // adds the notebook page
        $notebook->appendPage('Dynamic filtering', $this->form);
        
        $check_gender = new TCheckGroup('check_gender');
        $check_gender->addItems( array('M'=>'Male', 'F'=>'Female') );
        $check_gender->setLayout('horizontal');
        
        $combo_status = new TCombo('combo_status');
        $combo_status->addItems(array('S'=>'Single', 'C'=>'Committed', 'M'=>'Married') );
        
        $combo_customers = new TCombo('customers');
        
        // add the fields inside the form
        $this->form->addQuickField('Load customers: ',  $check_gender, 200);
        $this->form->addQuickField('',  $combo_status, 200);
        $this->form->addQuickField('',  $combo_customers, 200);
        
        $check_gender->setChangeAction( new TAction( array($this, 'onGenderChange' )) );
        $combo_status->setChangeAction( new TAction( array($this, 'onGenderChange' )) );
        
        // wrap the page content using vertical box
        $vbox = new TVBox;
        $vbox->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $vbox->add($notebook);

        parent::add($vbox);
    }
    
    /**
     * Action to be executed when the user changes the gender or status
     * @param $param Action parameters
     */
    public static function onGenderChange($param)
    {
        TTransaction::open('samples');
        $repo = new TRepository('Customer');
        $criteria = new TCriteria;
        $criteria->add(new TFilter('gender', 'IN', isset($param['check_gender']) ? $param['check_gender'] : array()));
        
        if ($param['combo_status'])
        {
            $criteria->add(new TFilter('status', '=', $param['combo_status']));
        }
        
        $customers = $repo->load($criteria);
        TTransaction::close();
        
        $options = array();
        foreach ($customers as $customer)
        {
            $options[ $customer->id] = $customer->name;
        }
        
        TCombo::reload('form_dynamic_filter', 'customers', $options);
    }
}
?>