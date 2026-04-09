<?php

// INSTRUCCIÓN O DECLARACIÓN PHP
/*
 // LÍNEA DE DOCUMENTACIÓN EN BLOQUE
 * SERVICIO DE GENERACIÓN DE PDF CON DOMPDF.
 // LÍNEA DE DOCUMENTACIÓN EN BLOQUE
 * RENDERIZA VISTAS HTML A PDF (FACTURAS Y PRESUPUESTOS) Y LOS GUARDA EN WRITEPATH.
 // CIERRE DE BLOQUE DE DOCUMENTACIÓN
 */

// DECLARA EL ESPACIO DE NOMBRES
namespace App\Libraries;

// IMPORTA UNA CLASE O TRAIT
use Dompdf\Dompdf;
// IMPORTA UNA CLASE O TRAIT
use Dompdf\Options;

// DECLARA UNA CLASE
class PdfService
// DELIMITADOR DE BLOQUE
{
    // COMENTARIO DE LÍNEA EXISTENTE
    // INSTANCIA DOMPDF CONFIGURADA UNA VEZ EN EL CONSTRUCTOR
    // DECLARA PROPIEDAD O CONSTANTE DE CLASE
    private Dompdf $dompdf;

    // COMENTARIO DE LÍNEA EXISTENTE
    // CONFIGURA PARSER HTML5, RECURSOS REMOTOS Y FUENTE POR DEFECTO PARA RENDERIZADO
    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function __construct()
    // DELIMITADOR DE BLOQUE
    {
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $options = new Options();
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $options->set('isHtml5ParserEnabled', true);
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $options->set('isRemoteEnabled', true);
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $options->set('defaultFont', 'Helvetica');
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $this->dompdf = new Dompdf($options);
    // DELIMITADOR DE BLOQUE
    }

    // COMENTARIO DE LÍNEA EXISTENTE
    // GENERA PDF DE FACTURA DESDE LA VISTA, LO ESCRIBE EN DISCO Y DEVUELVE RUTA RELATIVA
    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function generateInvoice(array $orderData): string
    // DELIMITADOR DE BLOQUE
    {
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $html = view('pdf/invoice', $orderData);
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->dompdf->loadHtml($html);
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->dompdf->setPaper('A4', 'portrait');
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->dompdf->render();

        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $outputPath = WRITEPATH . 'invoices/';
        // CONDICIONAL SI
        if (!is_dir($outputPath)) {
            // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
            mkdir($outputPath, 0755, true);
        // DELIMITADOR DE BLOQUE
        }

        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $filename = 'invoice_' . $orderData['order_number'] . '.pdf';
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        file_put_contents($outputPath . $filename, $this->dompdf->output());

        // RETORNA UN VALOR AL LLAMADOR
        return 'invoices/' . $filename;
    // DELIMITADOR DE BLOQUE
    }

    // COMENTARIO DE LÍNEA EXISTENTE
    // GENERA PDF DE PRESUPUESTO/CITA CON MISMO FLUJO QUE FACTURA PERO CARPETA Y VISTA DISTINTAS
    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function generateQuote(array $bookingData): string
    // DELIMITADOR DE BLOQUE
    {
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $html = view('pdf/quote', $bookingData);
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->dompdf->loadHtml($html);
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->dompdf->setPaper('A4', 'portrait');
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->dompdf->render();

        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $outputPath = WRITEPATH . 'quotes/';
        // CONDICIONAL SI
        if (!is_dir($outputPath)) {
            // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
            mkdir($outputPath, 0755, true);
        // DELIMITADOR DE BLOQUE
        }

        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $filename = 'quote_' . $bookingData['booking_number'] . '.pdf';
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        file_put_contents($outputPath . $filename, $this->dompdf->output());

        // RETORNA UN VALOR AL LLAMADOR
        return 'quotes/' . $filename;
    // DELIMITADOR DE BLOQUE
    }
// DELIMITADOR DE BLOQUE
}
