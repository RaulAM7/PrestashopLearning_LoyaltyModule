<?php
require_once dirname(__FILE__) . '/classes/LoyaltyPoints.php';

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
        
        $this->displayName = $this->trans('Puntos de Fidelidad', [], 'Modules.mipuntos.Admin');
        $this->description = $this->trans('Gestiona los puntos de fidelidad de tus clientes', [], 'Modules.mipuntos.Admin');        
        $this->confirmUNinstall = $this->trans('¿Estás seguro que deseas desinstalar el módulo?');
    }

    // 1.- MÉTODO DE INSTALACIÓN
    public function install()
    {
        if (! parent::install())
        {
            return false;
        }
        $hooks_mipuntos = ['actionValidateOrder', 'displayAdminOrder', 'displayCustomerAccount', 'displayHeader', 'displayLoyaltyOffer'];

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

        if (!$this->installAdminController())
        {
            return false;
        }
        
        return true;
    }

    // BACKOFFICE ADMIN CONTROLLER -> Especificamos donde va a aparecer en el Back Office
    public function installAdminController()
    {
        $tab = new Tab();
        $tab->class_name = 'AdminLoyaltyPoints';
    
        // Asignar la pestaña a "Clientes"
        $tab->id_parent = (int)Tab::getIdFromClassName('AdminParentCustomer');
        
        // Fallback en caso de que no encuentre la pestaña
        if (!$tab->id_parent) {
            $tab->id_parent = 0;
        }
    
        $tab->module = $this->name;
        $tab->active = 1;
        $tab->position = 99; // Posición en el menú
    
        $tab->name = [];
        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = 'Gestionar Puntos';
        }
    
        return $tab->add();
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
        
        // BACKOFFICE ADMIN CONTROLLER
        $id_tab = (int)Tab::getIdFromClassName('AdminLoyaltyPoints');
        if ($id_tab) {
            $tab = new Tab($id_tab);
            $tab->delete();
        }

        return true;
    }

    // 3.- PÁGINA DE CONFIGURACIÓN INICIAL EN EL BACK OFFICE
    public function getContent()
    {
        $output = '';
        if (Tools::isSubmit('submitMipuntosConfig')) {
            $defaultPoints = (int)Tools::getValue('MIPUNTOS_DEFAULT_POINTS');
            Configuration::updateValue('MIPUNTOS_DEFAULT_POINTS', $defaultPoints);
            $output .= $this->displayConfirmation($this->l('Configuración actualizada.'));
        }
    
        return $output . $this->renderForm();
    }
    public function renderForm()
    {
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
        $order = $params['order']; 
        $customerId = $order->id_customer; 

        if (!$customerId)
        {
            return;
        }

        $points = floor($order->total_paid / 10);

        $loyaltyPoints = new LoyaltyPoints();

        $existingPoints = Db::getInstance()->getValue(
        'SELECT points FROM ' . _DB_PREFIX_ . 'loyalty_points WHERE id_customer = ' . (int)$customerId
        );

        if ($existingPoints !== false) 
        {
            $totalPoints = (int)$existingPoints + $points;
            Db::getInstance()->execute(
                'UPDATE ' . _DB_PREFIX_ . 'loyalty_points 
                 SET points = ' . (int)$totalPoints . ', last_updated = "' . date('Y-m-d H:i:s') . '" 
                 WHERE id_customer = ' . (int)$customerId
            );
        } 
        else 
        {
            $loyaltyPoints->id_customer = $customerId;
            $loyaltyPoints->points = $points;
            $loyaltyPoints->last_updated = date('Y-m-d H:i:s');
            $loyaltyPoints->add();
        }
    }

    public function hookDisplayAdminOrder($params)
    {
        $order = new Order($params['id_order']);
        $customerId = $order->id_customer;

        $points = 0; 

        return "Puntos acumulados: $points";
    }

    public function hookDisplayCustomerAccount()
    {
        return '
        <a class="col-lg-4 col-md-6 col-sm-6 col-xs-12" id="loyalty-points-link" href="' . $this->context->link->getModuleLink('mipuntos', 'loyaltypoints') . '">
            <span class="link-item">
                <i class="material-icons">&#xe8e8;</i> <!-- Icono de Material Icons -->
                ' . $this->l('Mis Puntos de Fidelidad') . '
            </span>
        </a>';
    }

    public function hookDisplayHeader()
    {
        $this->context->controller->registerStylesheet(
            'mipuntos-loyaltypoints-css',
            'modules/' . $this->name . '/views/css/loyaltypoints.css',
            ['media' => 'all', 'priority' => 150]
        );
    }

    public function hookDisplayLoyaltyOffer()
    {
        $this->context->smarty->assign([
            'loyalty_points_offer' => [
                'title' => '¡Gana puntos de fidelidad!',
                'description' => 'Por cada 10€ gastados, ganarás 1 punto de fidelidad. ¡Acumula puntos y obtén descuentos en tus próximas compras!',
                'benefits' => [
                    'Descuentos exclusivos para clientes frecuentes.',
                    'Sistema sencillo de acumulación y canje.',
                    'Mayor ahorro en tus compras futuras.',
                ],
                'cta' => 'Empieza a ganar puntos hoy mismo.'
            ]
        ]);

        return $this->display(__FILE__, 'views/templates/front/loyalty_offer.tpl');
    }


}