<?php
/**
 * CustomerDataGridView
 *
 * @version    1.0
 * @package    samples
 * @subpackage tutor
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class CustomerDataGridView extends TPage
{
    private $form;      // search form
    private $datagrid;  // listing
    private $pageNavigation;
    private $loaded;
    
    /**
     * Class constructor
     * Creates the page, the search form and the listing
     */
    public function __construct()
    {
        parent::__construct();
        
        // creates the form
        $this->form = new TQuickForm('form_search_customer');
        $this->form->setFormTitle('Customers');
        $this->form->class = 'tform';
        
        // create the form fields
        $name   = new TEntry('name');
        $city_name = new TEntry('city_name');
        
        $this->form->addQuickField( 'Name', $name, '50%' );
        $this->form->addQuickField( 'City', $city_name, '50%' );
        
        $name->setValue(TSession::getValue('customer_name'));
        $city_name->setValue(TSession::getValue('customer_city_name'));
        
        $this->form->addQuickAction( 'Find', new TAction(array($this, 'onSearch')), 'fa:search blue' );
        $this->form->addQuickAction( 'CSV',  new TAction(array($this, 'onExportCSV')), 'fa:table' );
        $this->form->addQuickAction( 'New',  new TAction(array('CustomerFormView', 'onEdit')), 'fa:plus green' );
        
        // creates a DataGrid
        $this->datagrid = new TQuickGrid;
        $this->datagrid->enablePopover('Popover', 'Hi <b>{name}</b>, <br> that lives at <b>{city->name} - {city->state->name}</b>');
        
        // creates the datagrid columns
        $this->datagrid->addQuickColumn('Id', 'id', 'center', '10%', new TAction(array($this, 'onReload')), array('order', 'id'));
        $this->datagrid->addQuickColumn('Name', 'name', 'left', '30%', new TAction(array($this, 'onReload')), array('order', 'name'));
        $this->datagrid->addQuickColumn('Address', 'address', 'left', '30%');
        $this->datagrid->addQuickColumn('City', '{city->name} ({city->state->name})', 'left', '30%', new TAction(array($this, 'onReload')), array('order', 'city->name'));
        
        // creates two datagrid actions
        $this->datagrid->addQuickAction('Edit', new TDataGridAction(array('CustomerFormView', 'onEdit')), 'id', 'fa:edit blue');
        $this->datagrid->addQuickAction('Delete', new TDataGridAction(array($this, 'onDelete')), 'id', 'fa:trash red');
        
        // create the datagrid model
        $this->datagrid->createModel();
        
        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction(array($this, 'onReload')));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());
        
        // creates the page structure using a vertical box
        $vbox = new TVBox;
        $vbox->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $vbox->add($this->form);
        $vbox->add($this->datagrid);
        $vbox->add($this->pageNavigation);
        
        // add the box inside the page
        parent::add($vbox);
    }
    
    /**
     * method onSearch()
     * Register the filter in the session when the user performs a search
     */
    function onSearch()
    {
        // get the search form data
        $data = $this->form->getData();
        
        // check if the user has filled the form
        if (isset($data->name) AND ($data->name))
        {
            // creates a filter using what the user has typed
            $filter = new TFilter('name', 'like', "{$data->name}%");
            
            // stores the filter in the session
            TSession::setValue('customer_filter1', $filter);
            TSession::setValue('customer_name',   $data->name);
            
        }
        else
        {
            TSession::setValue('customer_filter1', NULL);
            TSession::setValue('customer_name',   '');
        }
        
        
        // check if the user has filled the form
        if ($data->city_name)
        {
            // creates a filter using what the user has typed
            $filter = new TFilter('(SELECT name from city WHERE id=customer.city_id)', 'like', "{$data->city_name}%");
            
            // stores the filter in the session
            TSession::setValue('customer_filter2', $filter);
            TSession::setValue('customer_city_name', $data->city_name);
        }
        else
        {
            TSession::setValue('customer_filter2', NULL);
            TSession::setValue('customer_city_name', '');
        }
        
        // fill the form with data again
        $this->form->setData($data);
        
        $param=array();
        $param['offset']    =0;
        $param['first_page']=1;
        $this->onReload($param);
    }
    
    /**
     * method onReload()
     * Load the datagrid with the database objects
     */
    function onReload($param = NULL)
    {
        try
        {
            // open a transaction with database 'samples'
            TTransaction::open('samples');
            
            // creates a repository for Customer
            $repository = new TRepository('Customer');
            $limit = 10;
            
            // creates a criteria
            $criteria = new TCriteria;
            
            $newparam = $param; // define new parameters
            if (isset($newparam['order']) AND $newparam['order'] == 'city->name')
            {
                $newparam['order'] = '(select name from city where city_id = id)';
            }
            
            // default order
            if (empty($newparam['order']))
            {
                $newparam['order'] = 'id';
                $newparam['direction'] = 'asc';
            }
            
            $criteria->setProperties($newparam); // order, offset
            $criteria->setProperty('limit', $limit);
            
            if (TSession::getValue('customer_filter1'))
            {
                // add the filter stored in the session to the criteria
                $criteria->add(TSession::getValue('customer_filter1'));
            }
            
            if (TSession::getValue('customer_filter2'))
            {
                // add the filter stored in the session to the criteria
                $criteria->add(TSession::getValue('customer_filter2'));
            }
            
            // load the objects according to criteria
            $customers = $repository->load($criteria, FALSE);
            $this->datagrid->clear();
            if ($customers)
            {
                foreach ($customers as $customer)
                {
                    // add the object inside the datagrid
                    $this->datagrid->addItem($customer);
                }
            }
            
            // reset the criteria for record count
            $criteria->resetProperties();
            $count= $repository->count($criteria);
            
            $this->pageNavigation->setCount($count); // count of records
            $this->pageNavigation->setProperties($param); // order, page
            $this->pageNavigation->setLimit($limit); // limit
            
            // close the transaction
            TTransaction::close();
            $this->loaded = true;
        }
        catch (Exception $e) // in case of exception
        {
            // shows the exception error message
            new TMessage('error', $e->getMessage());
            
            // undo all pending operations
            TTransaction::rollback();
        }
    }
    
    /**
     * Export to CSV
     */
    function onExportCSV()
    {
        $this->onSearch();

        try
        {
            // open a transaction with database 'samples'
            TTransaction::open('samples');
            
            // creates a repository for Customer
            $repository = new TRepository('Customer');
            
            // creates a criteria
            $criteria = new TCriteria;
            
            if (TSession::getValue('customer_filter1'))
            {
                // add the filter stored in the session to the criteria
                $criteria->add(TSession::getValue('customer_filter1'));
            }
            
            if (TSession::getValue('customer_filter2'))
            {
                // add the filter stored in the session to the criteria
                $criteria->add(TSession::getValue('customer_filter2'));
            }
            
            $csv = '';
            // load the objects according to criteria
            $customers = $repository->load($criteria);
            if ($customers)
            {
                foreach ($customers as $customer)
                {
                    $csv .= $customer->id.';'.
                            $customer->name.';'.
                            $customer->city_name."\n";
                }
                file_put_contents('app/output/customers.csv', $csv);
                TPage::openFile('app/output/customers.csv');
            }
            // close the transaction
            TTransaction::close();
        }
        catch (Exception $e) // in case of exception
        {
            // shows the exception error message
            new TMessage('error', $e->getMessage());
            
            // undo all pending operations
            TTransaction::rollback();
        }

    }
    
    /**
     * method onDelete()
     * executed whenever the user clicks at the delete button
     * Ask if the user really wants to delete the record
     */
    function onDelete($param)
    {
        // define the next action
        $action1 = new TAction(array($this, 'Delete'));
        $action1->setParameters($param); // pass 'key' parameter ahead
        
        // shows a dialog to the user
        new TQuestion('Do you really want to delete ?', $action1);
    }
    
    /**
     * method Delete()
     * Delete a record
     */
    function Delete($param)
    {
        try
        {
            // get the parameter $key
            $key = $param['id'];
            
            // open a transaction with database 'samples'
            TTransaction::open('samples');
            
            // instantiates object Customer
            $customer = new Customer($key);
            
            // deletes the object from the database
            $customer->delete();
            
            // close the transaction
            TTransaction::close();
            
            // reload the listing
            $this->onReload($param);
            
            // shows the success message
            new TMessage('info', "Record Deleted");
        }
        catch (Exception $e) // in case of exception
        {
            // shows the exception error message
            new TMessage('error', $e->getMessage());
            
            // undo all pending operations
            TTransaction::rollback();
        }
    }

    /**
     * method show()
     * Shows the page
     */
    function show()
    {
        // check if the datagrid is already loaded
        if (!$this->loaded)
        {
            $this->onReload( func_get_arg(0) );
        }
        parent::show();
    }
}
