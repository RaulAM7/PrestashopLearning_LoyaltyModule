<?php

if (!defined('_PS_VERSION_')) {
    exit;
}


class mipuntos extends Module
{
    // 0.- MÉTODO CONSTRUCTOR
    public function __construct()
    {
        $this->name = 'mipuntos';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'raulAM7';
        $this->need_instance = 0; // No necesita instancia
        $this->boostrap = true;
        $this->ps_versions_compliancy = [
            'min'=> '1.7.0.0',
            'max'=> _PS_VERSION_,
        ];
        
        parent::__construct();
        
        $this->displayName = $this->trans('Puntos de Fidelidad', [],'Modules.mipuntos.Admin');
        $this->description = $this->trans('Permite asignar puntos de fidelidad a los clientes después de una compra');
        $this->confirmUNinstall = $this->trans('¿Estás seguro que deseas desinstalar el módulo?');
    }

    // 1.- MÉTODO DE INSTALACIÓN
    public function install()
    {
        if (! parent::install())
        {
            return false;
        }
        $hooks_mipuntos = ['actionValidateOrder', 'displayAdminOrder'];

        foreach ($hooks_mipuntos as $hook)
        {
            if (! $this->registerHook($hook))
            {
                return false;
            }
        }

        $defaultConfigurations = [
            'MIPUNTOS_DEFAULT_POINTS'=> 0,
        ];
        foreach ($defaultConfigurations as $key => $value)
        {
            if (!Configuration::updateValue($key, $value))
            {
                return false;
            }
        }

        // MODELO DE DATOS - Creación de la tabla para almacenar puntos de fidelidad
        $sql = "CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . "loyalty_points` (
            `id_loyalty_points` INT(11) NOT NULL AUTO_INCREMENT,
            `id_customer` INT(11) NOT NULL,
            `points` INT(11) NOT NULL DEFAULT 0,
            `last_updated` DATETIME NOT NULL,
            PRIMARY KEY (`id_loyalty_points`),
            UNIQUE KEY `id_customer` (`id_customer`)
        ) ENGINE=" . _MYSQL_ENGINE_ . " DEFAULT CHARSET=utf8;";
    
        if (!Db::getInstance()->execute($sql)) 
        {
            return false;
        }

        return true;
    }

    // 2.- MÉTODO DE DESINSTALACIÓN
    public function uninstall()
    {
        if(!parent::uninstall())
        {
            return false;
        }
        $defaultConfigurations = [
            'MIPUNTOS_DEFAULT_POINTS',
        ];
        foreach($defaultConfigurations as $configuration)
        {
            if (!Configuration::deleteByName($configuration))
            {
                return false;
            }
        }
        $sql = "DROP TABLE IF EXISTS `" . _DB_PREFIX_ . "loyalty_points`";
        if (!Db::getInstance()->execute($sql)) {
            return false;
        }
        
        return true;
    }

    // 3.- PÁGINA DE CONFIGURACIÓN INICIAL EN EL BACK OFFICE
    public function getContent()
    {
        // Mensajes de confirmación
        $output = '';
        if (Tools::isSubmit('submitMipuntosConfig')) {
            $defaultPoints = (int)Tools::getValue('MIPUNTOS_DEFAULT_POINTS');
            Configuration::updateValue('MIPUNTOS_DEFAULT_POINTS', $defaultPoints);
            $output .= $this->displayConfirmation($this->l('Configuración actualizada.'));
        }
    
        // Renderiza el formulario
        return $output . $this->renderForm();
    }
    public function renderForm()
    {
        // Definimos los campos del formulario
        $fieldsForm = [
            'form' => [
                'legend' => [
                    'title' => $this->l('Configuración del Módulo'),
                    'icon' => 'icon-cogs',
                ],
                'input' => [
                    [
                        'type' => 'text',
                        'label' => $this->l('Puntos predeterminados por pedido'),
                        'name' => 'MIPUNTOS_DEFAULT_POINTS',
                        'required' => true,
                        'desc' => $this->l('Número de puntos asignados por defecto si no se calcula dinámicamente.'),
                    ],
                ],
                'submit' => [
                    'title' => $this->l('Guardar'),
                ],
            ],
        ];
    
        // Datos por defecto
        $helper = new HelperForm();
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitMipuntosConfig';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false) . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->fields_value['MIPUNTOS_DEFAULT_POINTS'] = Configuration::get('MIPUNTOS_DEFAULT_POINTS', 10); // Valor por defecto: 10
    
        return $helper->generateForm([$fieldsForm]);
    }
    

    // 4.- DEFINICIÓN DE HOOKS
    public function hookActionValidateOrder($params)
    {
        // Lógica para capturar la orden y asignar puntos
        $order = $params['order']; // Pedido validado
        $customer = $order->id_customer; // Cliente asociado al pedido

        // Calcular puntos (ejemplo: 1 punto por cada 10€)
        $points = floor($order->total_paid / 10);

        // Aquí podemos guardar los puntos (implementaremos en la próxima etapa)
    }

    public function hookDisplayAdminOrder($params)
    {
        // Lógica para mostrar los puntos del cliente en el pedido
        $order = new Order($params['id_order']);
        $customerId = $order->id_customer;

        // Obtener puntos (implementaremos en la próxima etapa)
        $points = 0; // Este valor será dinámico en el futuro

        return "Puntos acumulados: $points";
    }



}