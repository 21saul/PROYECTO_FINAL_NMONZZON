<?php

// COMENTARIO DE LÍNEA EXISTENTE
// PRUEBAS DE FLUJO DE ESTADOS DE PEDIDOS DE RETRATO: TRANSICIONES PERMITIDAS, BLOQUEADAS Y CAMINO FELIZ

// DECLARA EL ESPACIO DE NOMBRES
namespace Tests\Feature;

// IMPORTA UNA CLASE O TRAIT
use CodeIgniter\Test\CIUnitTestCase;

// DECLARA UNA CLASE
class PortraitOrderFlowTest extends CIUnitTestCase
// DELIMITADOR DE BLOQUE
{
    // DECLARA PROPIEDAD O CONSTANTE DE CLASE
    private array $stateTransitions = [
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'quote' => ['accepted', 'cancelled'],
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'accepted' => ['photo_received', 'cancelled'],
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'photo_received' => ['in_progress'],
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'in_progress' => ['revision'],
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'revision' => ['delivered', 'in_progress'],
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'delivered' => ['completed'],
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'completed' => [],
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'cancelled' => [],
    // INSTRUCCIÓN O DECLARACIÓN PHP
    ];

    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    private function canTransition(string $from, string $to): bool
    // DELIMITADOR DE BLOQUE
    {
        // RETORNA UN VALOR AL LLAMADOR
        return in_array($to, $this->stateTransitions[$from] ?? []);
    // DELIMITADOR DE BLOQUE
    }

    // COMENTARIO DE LÍNEA EXISTENTE
    // LAS TRANSICIONES DEFINIDAS EN EL FLUJO NORMAL SE CONSIDERAN VÁLIDAS
    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function testValidTransitionsAreAllowed(): void
    // DELIMITADOR DE BLOQUE
    {
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->assertTrue($this->canTransition('quote', 'accepted'));
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->assertTrue($this->canTransition('quote', 'cancelled'));
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->assertTrue($this->canTransition('accepted', 'photo_received'));
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->assertTrue($this->canTransition('photo_received', 'in_progress'));
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->assertTrue($this->canTransition('in_progress', 'revision'));
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->assertTrue($this->canTransition('revision', 'delivered'));
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->assertTrue($this->canTransition('revision', 'in_progress'));
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->assertTrue($this->canTransition('delivered', 'completed'));
    // DELIMITADOR DE BLOQUE
    }

    // COMENTARIO DE LÍNEA EXISTENTE
    // LAS TRANSICIONES NO DEFINIDAS EN LA MATRIZ DEBEN QUEDAR BLOQUEADAS
    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function testInvalidTransitionsAreBlocked(): void
    // DELIMITADOR DE BLOQUE
    {
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->assertFalse($this->canTransition('quote', 'completed'));
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->assertFalse($this->canTransition('quote', 'in_progress'));
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->assertFalse($this->canTransition('accepted', 'delivered'));
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->assertFalse($this->canTransition('completed', 'quote'));
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->assertFalse($this->canTransition('cancelled', 'accepted'));
    // DELIMITADOR DE BLOQUE
    }

    // COMENTARIO DE LÍNEA EXISTENTE
    // RECORRE TODO EL CAMINO DESDE PRESUPUESTO HASTA COMPLETADO SIN SALTOS ILEGALES
    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function testFullHappyPathTransitions(): void
    // DELIMITADOR DE BLOQUE
    {
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $path = ['quote', 'accepted', 'photo_received', 'in_progress', 'revision', 'delivered', 'completed'];
        // BUCLE FOR
        for ($i = 0; $i < count($path) - 1; $i++) {
            // INSTRUCCIÓN O DECLARACIÓN PHP
            $this->assertTrue(
                // INSTRUCCIÓN O DECLARACIÓN PHP
                $this->canTransition($path[$i], $path[$i + 1]),
                // INSTRUCCIÓN O DECLARACIÓN PHP
                "Failed: {$path[$i]} -> {$path[$i + 1]}"
            // INSTRUCCIÓN O DECLARACIÓN PHP
            );
        // DELIMITADOR DE BLOQUE
        }
    // DELIMITADOR DE BLOQUE
    }

    // COMENTARIO DE LÍNEA EXISTENTE
    // DESDE REVISIÓN SE PUEDE VOLVER A TRABAJO EN CURSO TRAS CAMBIOS SOLICITADOS
    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function testRevisionCanGoBackToInProgress(): void
    // DELIMITADOR DE BLOQUE
    {
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->assertTrue($this->canTransition('revision', 'in_progress'));
    // DELIMITADOR DE BLOQUE
    }

    // COMENTARIO DE LÍNEA EXISTENTE
    // LOS ESTADOS TERMINALES NO ADMITEN MÁS TRANSICIONES SALIENTES
    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function testTerminalStatesHaveNoTransitions(): void
    // DELIMITADOR DE BLOQUE
    {
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->assertEmpty($this->stateTransitions['completed']);
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->assertEmpty($this->stateTransitions['cancelled']);
    // DELIMITADOR DE BLOQUE
    }
// DELIMITADOR DE BLOQUE
}
