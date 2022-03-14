<?php

namespace App\Model;

interface Flusher
{
    /**
     * @return void
     */
    public function flush(): void;
}