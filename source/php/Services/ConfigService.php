<?php
declare(strict_types=1);

namespace App\Services;

use App\Contracts\ConfigServiceInterface;
use App\Contracts\LoggerServiceInterface;
class ConfigService implements ConfigServiceInterface
{
    private array $_config = [];

    public function __construct(array $config, private LoggerServiceInterface $log)
    {
        $this->_config = $this->_normalize(
            $config
        );
    }

    private function _normalize(array $content): array
    {
        $this->log->write("Normalizing config...");

        foreach ($content as $key => $value) {
            if(!is_array($value)) {
                $this->log->write("Setting default for key: $key");
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