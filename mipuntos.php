<?php

if (!defined('_PS_VERSION_')) {
    exit;
}


class mipuntos extends Module
{
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

    // Métodos de instalación
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
            'NEW_MODULE_CONFIG'=> "VALUE",
        ];
        foreach ($defaultConfigurations as $key => $value)
        {
            if (!Configuration::updateValue($key, $value))
            {
                return false;
            }
        }
    }

    // Método de desinstalación
    public function uninstall()
    {
        if(!parent::uninstall())
        {
            return false;
        }
        foreach($defaultConfigurations as $configuration)
        {
            if (!Configuration::deleteByName($configuration))
            {
                return false;
            }
        }
    }



    // Definición de Hooks

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