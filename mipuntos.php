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