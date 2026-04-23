<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Recaptcha extends BaseConfig
{
    public string $siteKey = 'YOUR_SITE_KEY_HERE';
    public string $secretKey = 'YOUR_SECRET_KEY_HERE';
    public bool $enabled = true;
}