<?php

return [
    App\Providers\AppServiceProvider::class,
    Modules\User\Providers\UserServiceProvider::class,
    Modules\Auth\Providers\AuthServiceProvider::class,
    Modules\Wallet\Providers\WalletServiceProvider::class,
];
