<?php

namespace Yavuz\ExchangeRates\ExchangeRate\Cache;

use Exception;

class Cache
{
    /**
     * @var string
     */
    protected string $path;

    /**
     * @var string
     */
    protected string $cacheFile;

    /**
     * Constructor
     *
     * @throws Exception
     */
    public function __construct()
    {
        $this->path = WRITEABLE_PATH;

        if (!file_exists($this->path)) {
            @mkdir($this->path . '/cache', 0640, true);
        }

        $this->path = rtrim($this->path . '/cache', '/') . '/';

        if (!is_writable($this->path)) {
            throw new Exception('Cache directory is not writable!');
        }
    }

    /**
     * @param string $cacheFile
     * @return $this
     */
    public function setCacheFile(string $cacheFile): self
    {
        $this->cacheFile = $cacheFile;
        return $this;
    }

    /**
     * @param bool $path
     * @return string
     */
    public function getCacheFile(bool $path = true): string
    {
        return $path === true ? $this->path . $this->cacheFile : $this->cacheFile;
    }

    /**
     * @param string $data
     * @return void
     */
    public function writeCache(string $data): void
    {
        try {
            $fileResource = fopen($this->path . $this->cacheFile, 'w');
            fwrite($fileResource, $data);
        } catch (Exception $e) {
            dump($e->getMessage());
        } finally {
            fclose($fileResource);
        }
    }

    /**
     * @return string|null
     */
    public function readCache(): ?string
    {
        if ($data = file_get_contents($this->path . $this->cacheFile)) {
            return $data;
        }
        return null;
    }

    /**
     * @return void
     */
    public function clearCache(): void
    {
        file_put_contents($this->path . $this->cacheFile, '');
    }
}
