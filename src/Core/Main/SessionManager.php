<?php

namespace HashtagCms\Core\Main;

use Illuminate\Support\Facades\Session;

/**
 * Using session for now
 */
class SessionManager extends Session
{
    private string $infoKeeperKey = 'infoKeeper';

    public function __construct()
    {

    }

    /**
     * Is exists in key
     */
    public function exists(string $key): bool
    {
        return \session()->exists($key);
    }

    /**
     * Put value in session
     */
    public function put(string $key, mixed $value): void
    {
        \session()->put($key, $value);
    }

    /**
     * Get from session
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Session\SessionManager|\Illuminate\Session\Store|mixed
     */
    public function get($key): mixed
    {
        return \session($key);
    }

    public function setInfoKeeper(string $key, mixed $value): void
    {
        if (! $this->exists($this->infoKeeperKey)) {
            $this->put($this->infoKeeperKey, []);
        }
        $infoKeeper = $this->get($this->infoKeeperKey);
        $infoKeeper[$key] = $value;
        $this->put($this->infoKeeperKey, $infoKeeper);
    }

    public function getInfoKeeper(?string $key = null): mixed
    {
        //create a blank namespace if not exists
        if (! $this->exists($this->infoKeeperKey)) {
            $this->put($this->infoKeeperKey, []);
        }

        $infoKeeper = $this->get($this->infoKeeperKey);

        if ($key == null) {
            return $infoKeeper;
        }

        return $infoKeeper[$key] ?? null;
    }
}
