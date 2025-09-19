<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\TelescopeServiceProvider::class,
    Modules\Auth\Providers\AuthServiceProvider::class,
    Modules\User\Providers\UserServiceProvider::class,
    Modules\Wallet\Providers\WalletServiceProvider::class,
];
