<?php
declare(strict_types=1);

namespace App\Services;

use App\Contracts\ConfigServiceInterface;

class ConfigService implements ConfigServiceInterface
{
    private array $_config = [];

    public function __construct(array $config)
    {
        $this->_config = $this->_normalize(
            $config
        );
    }

    private function _normalize(array $content): array
    {
        foreach ($content as $key => $value) {
            if(!is_string($key) || !is_array($value)) {
                unset($content[$key]);
            }
        }
        return $content;
    }

    public function getConfig(): array
    {
        return $this->_config; 
    } 
}