<?php

class mipuntosloyaltypointsModuleFrontController extends ModuleFrontController
{
    // public $ssl = true;

    public function initContent()
    {
        parent::initContent();

        if (!$this->context->customer->isLogged()) {
            Tools::redirect('index.php?controller=authentication');
        }

        $id_customer = (int)$this->context->customer->id;
        $sql = 'SELECT points FROM ' . _DB_PREFIX_ . 'loyalty_points WHERE id_customer = ' . $id_customer;
        $points = Db::getInstance()->getValue($sql);

        if ($points === false) {
            $points = 0;
        }

        $this->context->smarty->assign([
            'points' => $points,
        ]);

        
        $this->setTemplate('module:mipuntos/views/templates/front/loyaltypoints.tpl');
    }
}
