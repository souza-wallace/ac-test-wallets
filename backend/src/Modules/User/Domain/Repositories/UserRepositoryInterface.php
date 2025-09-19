<?php

namespace Modules\User\Domain\Repositories;

use Modules\User\Domain\Entities\User;

interface UserRepositoryInterface
{
    public function findById(int $id): ?User;
    public function findByIdWithWallet(int $id): ?User;
    public function findByEmailWithWallet(string $email): ?User;
    public function findByEmail(string $email): ?User;
    public function save(User $user): User;
}