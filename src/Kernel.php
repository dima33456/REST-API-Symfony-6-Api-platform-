<?php

namespace App;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    public function boot(): void
    {
        parent::boot();
        // устанавливаем временную зону из переменной окружения
        date_default_timezone_set($this->getContainer()->getParameter('timezone'));
        // также меняем максимальный объём памяти, чтобы можно было загружать большие(относительно) картинки
        ini_set('memory_limit', '201326592');
    }
}
