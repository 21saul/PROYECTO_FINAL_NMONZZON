<?php

// IMPORTA UNA CLASE O TRAIT
use CodeIgniter\Test\CIUnitTestCase;
// IMPORTA UNA CLASE O TRAIT
use CodeIgniter\Test\DatabaseTestTrait;
// IMPORTA UNA CLASE O TRAIT
use Tests\Support\Database\Seeds\ExampleSeeder;
// IMPORTA UNA CLASE O TRAIT
use Tests\Support\Models\ExampleModel;

/**
 * @internal
 */
// INSTRUCCIÓN O DECLARACIÓN PHP
final class ExampleDatabaseTest extends CIUnitTestCase
// DELIMITADOR DE BLOQUE
{
    // IMPORTA UNA CLASE O TRAIT
    use DatabaseTestTrait;

    // DECLARA PROPIEDAD O CONSTANTE DE CLASE
    protected $seed = ExampleSeeder::class;

    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function testModelFindAll(): void
    // DELIMITADOR DE BLOQUE
    {
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $model = new ExampleModel();

        // COMENTARIO DE LÍNEA EXISTENTE
        // Get every row created by ExampleSeeder
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $objects = $model->findAll();

        // COMENTARIO DE LÍNEA EXISTENTE
        // Make sure the count is as expected
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->assertCount(3, $objects);
    // DELIMITADOR DE BLOQUE
    }

    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function testSoftDeleteLeavesRow(): void
    // DELIMITADOR DE BLOQUE
    {
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $model = new ExampleModel();
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->setPrivateProperty($model, 'useSoftDeletes', true);
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->setPrivateProperty($model, 'tempUseSoftDeletes', true);

        /** @var stdClass $object */
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $object = $model->first();
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $model->delete($object->id);

        // COMENTARIO DE LÍNEA EXISTENTE
        // The model should no longer find it
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->assertNull($model->find($object->id));

        // COMENTARIO DE LÍNEA EXISTENTE
        // ... but it should still be in the database
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $result = $model->builder()->where('id', $object->id)->get()->getResult();

        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->assertCount(1, $result);
    // DELIMITADOR DE BLOQUE
    }
// DELIMITADOR DE BLOQUE
}
