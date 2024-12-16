<?php

class LoyaltyPoints extends ObjectModel
{
    public $id_customer;
    public $points;
    public $last_updated;

    public static $definition = [
        'table' => 'loyalty_points',
        'primary' => 'id_loyalty_points',
        'fields' => [
            'id_customer' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true],
            'points' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true],
            'last_updated' => ['type' => self::TYPE_DATE, 'validate' => 'isDateFormat', 'required' => true],
        ],
    ];

    public function __construct($id = null, $id_lang = null, $id_shop = null)
    {
        parent::__construct($id, $id_lang, $id_shop);
    }
}