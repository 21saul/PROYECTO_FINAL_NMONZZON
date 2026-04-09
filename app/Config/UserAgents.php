<?php

// DECLARA EL ESPACIO DE NOMBRES
namespace Config;

// IMPORTA UNA CLASE O TRAIT
use CodeIgniter\Config\BaseConfig;

/**
 * -------------------------------------------------------------------
 * User Agents
 * -------------------------------------------------------------------
 *
 * This file contains four arrays of user agent data. It is used by the
 * User Agent Class to help identify browser, platform, robot, and
 * mobile device data. The array keys are used to identify the device
 * and the array values are used to set the actual name of the item.
 */
// DECLARA UNA CLASE
class UserAgents extends BaseConfig
// DELIMITADOR DE BLOQUE
{
    /**
     * -------------------------------------------------------------------
     * OS Platforms
     * -------------------------------------------------------------------
     *
     * @var array<string, string>
     */
    // DECLARA PROPIEDAD O CONSTANTE DE CLASE
    public array $platforms = [
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'windows nt 10.0' => 'Windows 10',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'windows nt 6.3'  => 'Windows 8.1',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'windows nt 6.2'  => 'Windows 8',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'windows nt 6.1'  => 'Windows 7',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'windows nt 6.0'  => 'Windows Vista',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'windows nt 5.2'  => 'Windows 2003',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'windows nt 5.1'  => 'Windows XP',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'windows nt 5.0'  => 'Windows 2000',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'windows nt 4.0'  => 'Windows NT 4.0',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'winnt4.0'        => 'Windows NT 4.0',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'winnt 4.0'       => 'Windows NT',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'winnt'           => 'Windows NT',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'windows 98'      => 'Windows 98',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'win98'           => 'Windows 98',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'windows 95'      => 'Windows 95',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'win95'           => 'Windows 95',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'windows phone'   => 'Windows Phone',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'windows'         => 'Unknown Windows OS',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'android'         => 'Android',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'blackberry'      => 'BlackBerry',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'iphone'          => 'iOS',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'ipad'            => 'iOS',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'ipod'            => 'iOS',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'os x'            => 'Mac OS X',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'ppc mac'         => 'Power PC Mac',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'freebsd'         => 'FreeBSD',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'ppc'             => 'Macintosh',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'linux'           => 'Linux',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'debian'          => 'Debian',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'sunos'           => 'Sun Solaris',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'beos'            => 'BeOS',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'apachebench'     => 'ApacheBench',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'aix'             => 'AIX',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'irix'            => 'Irix',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'osf'             => 'DEC OSF',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'hp-ux'           => 'HP-UX',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'netbsd'          => 'NetBSD',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'bsdi'            => 'BSDi',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'openbsd'         => 'OpenBSD',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'gnu'             => 'GNU/Linux',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'unix'            => 'Unknown Unix OS',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'symbian'         => 'Symbian OS',
    // INSTRUCCIÓN O DECLARACIÓN PHP
    ];

    /**
     * -------------------------------------------------------------------
     * Browsers
     * -------------------------------------------------------------------
     *
     * The order of this array should NOT be changed. Many browsers return
     * multiple browser types so we want to identify the subtype first.
     *
     * @var array<string, string>
     */
    // DECLARA PROPIEDAD O CONSTANTE DE CLASE
    public array $browsers = [
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'OPR'    => 'Opera',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'Flock'  => 'Flock',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'Edge'   => 'Spartan',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'Edg'    => 'Edge',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'Chrome' => 'Chrome',
        // COMENTARIO DE LÍNEA EXISTENTE
        // Opera 10+ always reports Opera/9.80 and appends Version/<real version> to the user agent string
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'Opera.*?Version'   => 'Opera',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'Opera'             => 'Opera',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'MSIE'              => 'Internet Explorer',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'Internet Explorer' => 'Internet Explorer',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'Trident.* rv'      => 'Internet Explorer',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'Shiira'            => 'Shiira',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'Firefox'           => 'Firefox',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'Chimera'           => 'Chimera',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'Phoenix'           => 'Phoenix',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'Firebird'          => 'Firebird',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'Camino'            => 'Camino',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'Netscape'          => 'Netscape',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'OmniWeb'           => 'OmniWeb',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'Safari'            => 'Safari',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'Mozilla'           => 'Mozilla',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'Konqueror'         => 'Konqueror',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'icab'              => 'iCab',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'Lynx'              => 'Lynx',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'Links'             => 'Links',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'hotjava'           => 'HotJava',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'amaya'             => 'Amaya',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'IBrowse'           => 'IBrowse',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'Maxthon'           => 'Maxthon',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'Ubuntu'            => 'Ubuntu Web Browser',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'Vivaldi'           => 'Vivaldi',
    // INSTRUCCIÓN O DECLARACIÓN PHP
    ];

    /**
     * -------------------------------------------------------------------
     * Mobiles
     * -------------------------------------------------------------------
     *
     * @var array<string, string>
     */
    // DECLARA PROPIEDAD O CONSTANTE DE CLASE
    public array $mobiles = [
        // COMENTARIO DE LÍNEA EXISTENTE
        // legacy array, old values commented out
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'mobileexplorer' => 'Mobile Explorer',
        // COMENTARIO DE LÍNEA EXISTENTE
        // 'openwave'             => 'Open Wave',
        // COMENTARIO DE LÍNEA EXISTENTE
        // 'opera mini'           => 'Opera Mini',
        // COMENTARIO DE LÍNEA EXISTENTE
        // 'operamini'            => 'Opera Mini',
        // COMENTARIO DE LÍNEA EXISTENTE
        // 'elaine'               => 'Palm',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'palmsource' => 'Palm',
        // COMENTARIO DE LÍNEA EXISTENTE
        // 'digital paths'        => 'Palm',
        // COMENTARIO DE LÍNEA EXISTENTE
        // 'avantgo'              => 'Avantgo',
        // COMENTARIO DE LÍNEA EXISTENTE
        // 'xiino'                => 'Xiino',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'palmscape' => 'Palmscape',
        // COMENTARIO DE LÍNEA EXISTENTE
        // 'nokia'                => 'Nokia',
        // COMENTARIO DE LÍNEA EXISTENTE
        // 'ericsson'             => 'Ericsson',
        // COMENTARIO DE LÍNEA EXISTENTE
        // 'blackberry'           => 'BlackBerry',
        // COMENTARIO DE LÍNEA EXISTENTE
        // 'motorola'             => 'Motorola'

        // COMENTARIO DE LÍNEA EXISTENTE
        // Phones and Manufacturers
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'motorola'             => 'Motorola',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'nokia'                => 'Nokia',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'palm'                 => 'Palm',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'iphone'               => 'Apple iPhone',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'ipad'                 => 'iPad',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'ipod'                 => 'Apple iPod Touch',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'sony'                 => 'Sony Ericsson',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'ericsson'             => 'Sony Ericsson',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'blackberry'           => 'BlackBerry',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'cocoon'               => 'O2 Cocoon',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'blazer'               => 'Treo',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'lg'                   => 'LG',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'amoi'                 => 'Amoi',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'xda'                  => 'XDA',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'mda'                  => 'MDA',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'vario'                => 'Vario',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'htc'                  => 'HTC',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'samsung'              => 'Samsung',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'sharp'                => 'Sharp',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'sie-'                 => 'Siemens',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'alcatel'              => 'Alcatel',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'benq'                 => 'BenQ',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'ipaq'                 => 'HP iPaq',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'mot-'                 => 'Motorola',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'playstation portable' => 'PlayStation Portable',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'playstation 3'        => 'PlayStation 3',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'playstation vita'     => 'PlayStation Vita',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'hiptop'               => 'Danger Hiptop',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'nec-'                 => 'NEC',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'panasonic'            => 'Panasonic',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'philips'              => 'Philips',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'sagem'                => 'Sagem',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'sanyo'                => 'Sanyo',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'spv'                  => 'SPV',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'zte'                  => 'ZTE',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'sendo'                => 'Sendo',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'nintendo dsi'         => 'Nintendo DSi',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'nintendo ds'          => 'Nintendo DS',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'nintendo 3ds'         => 'Nintendo 3DS',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'wii'                  => 'Nintendo Wii',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'open web'             => 'Open Web',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'openweb'              => 'OpenWeb',

        // COMENTARIO DE LÍNEA EXISTENTE
        // Operating Systems
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'android'    => 'Android',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'symbian'    => 'Symbian',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'SymbianOS'  => 'SymbianOS',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'elaine'     => 'Palm',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'series60'   => 'Symbian S60',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'windows ce' => 'Windows CE',

        // COMENTARIO DE LÍNEA EXISTENTE
        // Browsers
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'obigo'         => 'Obigo',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'netfront'      => 'Netfront Browser',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'openwave'      => 'Openwave Browser',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'mobilexplorer' => 'Mobile Explorer',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'operamini'     => 'Opera Mini',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'opera mini'    => 'Opera Mini',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'opera mobi'    => 'Opera Mobile',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'fennec'        => 'Firefox Mobile',

        // COMENTARIO DE LÍNEA EXISTENTE
        // Other
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'digital paths' => 'Digital Paths',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'avantgo'       => 'AvantGo',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'xiino'         => 'Xiino',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'novarra'       => 'Novarra Transcoder',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'vodafone'      => 'Vodafone',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'docomo'        => 'NTT DoCoMo',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'o2'            => 'O2',

        // COMENTARIO DE LÍNEA EXISTENTE
        // Fallback
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'mobile'     => 'Generic Mobile',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'wireless'   => 'Generic Mobile',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'j2me'       => 'Generic Mobile',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'midp'       => 'Generic Mobile',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'cldc'       => 'Generic Mobile',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'up.link'    => 'Generic Mobile',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'up.browser' => 'Generic Mobile',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'smartphone' => 'Generic Mobile',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'cellphone'  => 'Generic Mobile',
    // INSTRUCCIÓN O DECLARACIÓN PHP
    ];

    /**
     * -------------------------------------------------------------------
     * Robots
     * -------------------------------------------------------------------
     *
     * There are hundred of bots but these are the most common.
     *
     * @var array<string, string>
     */
    // DECLARA PROPIEDAD O CONSTANTE DE CLASE
    public array $robots = [
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'googlebot'            => 'Googlebot',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'google-pagerenderer'  => 'Google Page Renderer',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'google-read-aloud'    => 'Google Read Aloud',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'google-safety'        => 'Google Safety Bot',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'msnbot'               => 'MSNBot',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'baiduspider'          => 'Baiduspider',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'bingbot'              => 'Bing',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'bingpreview'          => 'BingPreview',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'slurp'                => 'Inktomi Slurp',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'yahoo'                => 'Yahoo',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'ask jeeves'           => 'Ask Jeeves',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'fastcrawler'          => 'FastCrawler',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'infoseek'             => 'InfoSeek Robot 1.0',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'lycos'                => 'Lycos',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'yandex'               => 'YandexBot',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'mediapartners-google' => 'MediaPartners Google',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'CRAZYWEBCRAWLER'      => 'Crazy Webcrawler',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'adsbot-google'        => 'AdsBot Google',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'feedfetcher-google'   => 'Feedfetcher Google',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'curious george'       => 'Curious George',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'ia_archiver'          => 'Alexa Crawler',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'MJ12bot'              => 'Majestic-12',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'Uptimebot'            => 'Uptimebot',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'duckduckbot'          => 'DuckDuckBot',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'sogou'                => 'Sogou Spider',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'exabot'               => 'Exabot',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'bot'                  => 'Generic Bot',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'crawler'              => 'Generic Crawler',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'spider'               => 'Generic Spider',
    // INSTRUCCIÓN O DECLARACIÓN PHP
    ];
// DELIMITADOR DE BLOQUE
}
