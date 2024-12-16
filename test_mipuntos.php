<?php

require_once dirname(__FILE__) . '/config/config.inc.php';
require_once dirname(__FILE__) . '/init.php';
require_once dirname(__FILE__) . '/modules/mipuntos/classes/LoyaltyPoints.php';

// Prueba para insertar puntos
try {
    // Crear una nueva instancia de LoyaltyPoints
    $loyaltyPoints = new LoyaltyPoints();
    $loyaltyPoints->id_customer = 1; // ID de un cliente existente
    $loyaltyPoints->points = 50; // Asignar 50 puntos
    $loyaltyPoints->last_updated = date('Y-m-d H:i:s'); // Fecha actual

    if ($loyaltyPoints->add()) {
        echo "✅ Puntos añadidos correctamente.\n";
    } else {
        echo "❌ Error al añadir los puntos.\n";
    }

    // Leer los puntos del cliente
    $loadedLoyaltyPoints = new LoyaltyPoints(1); // ID del cliente existente
    echo "Puntos cargados: " . $loadedLoyaltyPoints->points . "\n";
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}