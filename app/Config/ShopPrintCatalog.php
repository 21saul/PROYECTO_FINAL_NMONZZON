<?php

declare(strict_types=1);

namespace Config;

/**
 * Títulos de láminas y totebags según nombre de fichero (tienda / tarjetas / seeder).
 */
class ShopPrintCatalog
{
    /**
     * @var list<string>
     */
    private const ORDERED_PRINT_TITLES = [
        'Cala línea',
        'Strelitzia línea',
        'Búfalo',
        'Mike Wazowski',
        'Pulpo vertical',
        'Paco',
        'Chica línea',
        'Aretha Franklin',
        'Rana en bici y flores',
        'Cabeza animales',
        'Cabeza ola',
        'Cabeza casa',
        'Cabeza Galicia',
        'El juego del calamar',
        'Gallo teriomorfo',
        'Máquina de coser',
        'Amari',
        'Gata teriomorfa',
        'Cebra',
        'Libélula',
        'Mariposa',
        'Máquina de coser (variante)',
        'Tres mariposas',
        'Lince',
        'Willow Smith',
        'Kamali',
        'Quino y Mafalda',
        'Pájaro teriomorfo',
        'Ángela Molina',
        'Gato teriomorfo',
        'Joker',
        'Nala',
        'Teléfono antiguo',
        'Dani Rovira',
        'Mariquita',
        'Erizo',
        'Lagartija',
        'Rana en bicicleta',
        'Samurái',
        'Cangrejos',
        'Freddie Mercury',
        'Rana en bici',
        'Café y molinillo',
        'Sansa y Arya',
        'Zorro en la cama',
        'Padre e hijo',
        'Tony Stark',
        'Chica con libro',
        'Camaleón en silla',
        'Chica y planta',
        'Batman',
        'The Last of Us',
        'Thoros y Dondarrion',
        'Mantis religiosa',
        'Tortuga',
        'Tigre',
        'Caballo en línea',
        'Murciélagos',
        'Tortuga II',
        'Ojos de águila',
        'Escorpión',
        'Pulpo',
    ];

    /**
     * @var list<string>
     */
    private const PRINT_STEMS_ORDERED = [
        'cala-linea',
        'sterlizia-linea',
        'bufalo',
        'mike-wazowski',
        'pulpo-vertical',
        'paco',
        'chica-linea',
        'aretha-franklin',
        'rana-bici-flores',
        'cabeza-animales',
        'cabeza-ola',
        'cabeza-casa',
        'cabeza-galicia',
        'el-juego-del-calamar',
        'gallo-teriomorfo',
        'maquina-de-coser',
        'amari',
        'gata-teriomorfa',
        'cebra',
        'libelula',
        'mariposa',
        'maquina-coser',
        '3-mariposas',
        'lince',
        'willow-smith',
        'kamali',
        'quino-mafalda',
        'pajaro-teriomorfo',
        'angela-molina',
        'gato-teriomorfo',
        'joker',
        'nala',
        'telefono-antiguo',
        'dani-rovira',
        'mariquitilla',
        'erizo',
        'lagartija',
        'rana-biciclo',
        'samurai',
        'cangrejos',
        'freddie',
        'rana-bici',
        'cafe-molinillo',
        'sansa-y-arya',
        'zorro-cama',
        'padre-e-hijo',
        'tony-stark',
        'chica-libro',
        'camaleon-silla',
        'chica-planta',
        'batman',
        'the-last-of-us',
        'thoros-y-dondarrion',
        'mantis-religiosa',
        'tortuga',
        'tigre',
        'caballo-en-linea',
        'murcielagos',
        'tortuga-2',
        'ojos-aguila',
        'escorpion',
        'pulpo',
    ];

    /** @var array<string, string>|null */
    private static ?array $stemToPrintTitle = null;

    public static function printTitleForFilename(string $file): string
    {
        if (count(self::ORDERED_PRINT_TITLES) !== count(self::PRINT_STEMS_ORDERED)) {
            throw new \RuntimeException('ShopPrintCatalog: listas de láminas con longitud distinta.');
        }

        $stem = pathinfo($file, PATHINFO_FILENAME);
        if (self::$stemToPrintTitle === null) {
            self::$stemToPrintTitle = [];
            foreach (self::PRINT_STEMS_ORDERED as $i => $s) {
                self::$stemToPrintTitle[$s] = self::ORDERED_PRINT_TITLES[$i];
            }
        }

        if (isset(self::$stemToPrintTitle[$stem])) {
            return self::$stemToPrintTitle[$stem];
        }

        if (preg_match('/^\d+$/', $stem) === 1) {
            $i = (int) $stem;
            if ($i >= 1 && $i <= count(self::ORDERED_PRINT_TITLES)) {
                return self::ORDERED_PRINT_TITLES[$i - 1];
            }
        }

        return self::humanizeStem($stem, 'Lámina nmonzzon');
    }

    public static function totebagTitleForFilename(string $file): string
    {
        $stem = pathinfo($file, PATHINFO_FILENAME);

        return 'Totebag — ' . self::humanizeStem($stem, 'nmonzzon');
    }

    private static function humanizeStem(string $stem, string $emptyFallback): string
    {
        $t = str_replace(['_', '-'], ' ', $stem);
        $t = preg_replace('/\s+/', ' ', trim($t)) ?? $t;

        return $t !== '' ? mb_convert_case($t, MB_CASE_TITLE, 'UTF-8') : $emptyFallback;
    }
}
