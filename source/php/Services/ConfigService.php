<?php
declare(strict_types=1);

namespace App\Services;

use App\Contracts\ConfigServiceInterface;

class ConfigService implements ConfigServiceInterface
{
    private array $config = [];

    public function __construct(array $config)
    {
        $this->config = $this->normalize(
            $config
        );
    }

    private function normalize(array $content): array
    {
        foreach ($content as $key => $value) {
            // Remove invalid entries
            if(!is_string($key) || !is_array($value) || !array_is_list($value)) {
                unset($content[$key]);
            } else {
                // Ensure leading slash
                $nKey = '/' . ltrim($key, '/');
                if ($nKey !== $key) {
                    $content[$nKey] = $value;
                    unset($content[$key]);
                }
            }
        }
        return $content;
    }

    public function getFiles(): array
    {
        return $this->config;
    } 
}