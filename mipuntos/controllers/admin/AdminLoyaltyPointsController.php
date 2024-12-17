<?php

require_once _PS_MODULE_DIR_ . 'mipuntos/classes/LoyaltyPoints.php';

class AdminLoyaltyPointsController extends ModuleAdminController
{
    public function __construct()
    {
        $this->table = 'loyalty_points';
        $this->className = 'LoyaltyPoints';
        $this->lang = false;
        $this->bootstrap = true;

        parent::__construct();

        // Definir los campos para la lista
        $this->fields_list = [
            'id_loyalty_points' => [
                'title' => $this->l('ID'),
                'align' => 'center',
                'class' => 'fixed-width-xs',
            ],
            'id_customer' => [
                'title' => $this->l('Customer ID'),
                'align' => 'center',
            ],
            'points' => [
                'title' => $this->l('Points'),
                'align' => 'center',
            ],
            'last_updated' => [
                'title' => $this->l('Last Updated'),
                'type' => 'datetime',
            ],
        ];

        // Configurar las acciones de la lista
        $this->addRowAction('edit');
        $this->addRowAction('delete');
    }

    public function renderForm()
    {
        $this->fields_form = [
            'legend' => [
                'title' => $this->l('Edit Loyalty Points'),
            ],
            'input' => [
                [
                    'type' => 'text',
                    'label' => $this->l('Customer ID'),
                    'name' => 'id_customer',
                    'required' => true,
                ],
                [
                    'type' => 'text',
                    'label' => $this->l('Points'),
                    'name' => 'points',
                    'required' => true,
                ],
                [
                    'type' => 'datetime',
                    'label' => $this->l('Last Updated'),
                    'name' => 'last_updated',
                ],
            ],
            'submit' => [
                'title' => $this->l('Save'),
            ],
        ];

        return parent::renderForm();
    }
}
