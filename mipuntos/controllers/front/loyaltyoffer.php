<?php
class mipuntosLoyaltyofferModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();
    
        // Define las variables para la vista
        $this->context->smarty->assign([
            'loyalty_points_offer' => [
                'title' => '¡Gana puntos con cada compra!',
                'description' => 'Obtén puntos de fidelidad cada vez que realices una compra en nuestra tienda. Estos puntos pueden canjearse por descuentos en tus próximos pedidos.',
                'benefits' => [
                    'Gana 1 punto por cada 10€ gastados.',
                    'Canjea tus puntos por descuentos exclusivos.',
                    'Recibe puntos extra en promociones especiales.',
                    'Consulta tu saldo de puntos desde tu cuenta.'
                ],
                'cta' => 'Consulta tu cuenta'
            ]
        ]);
    
        // Renderiza la plantilla
        $this->setTemplate('module:mipuntos/views/templates/front/loyalty_offer.tpl');
    }

    public function postProcess()
    {
        if (Tools::isSubmit('customer_email')) {
            $email = Tools::getValue('customer_email');
            $name = Tools::getValue('customer_name');
            $phone = Tools::getValue('customer_phone');

            // Validación básica
            if (!Validate::isEmail($email)) {
                die(json_encode(['success' => false, 'error' => 'Email no válido.']));
            }

            // Enviar el email
            $templateVars = [
                '{name}' => $name,
                '{email}' => $email,
                '{phone}' => $phone,
            ];

            $sent = Mail::Send(
                (int)Configuration::get('PS_LANG_DEFAULT'),
                'loyalty_offer_email',
                '¡Disfruta del Doble de Puntos!',
                $templateVars,
                $email,
                $name,
                null,
                null,
                null,
                null,
                _PS_MODULE_DIR_ . 'mipuntos/mails/',
                false,
                null
            );

            if ($sent) {
                die(json_encode(['success' => true]));
            } else {
                die(json_encode(['success' => false, 'error' => 'Error al enviar el correo.']));
            }
        }
    }
}