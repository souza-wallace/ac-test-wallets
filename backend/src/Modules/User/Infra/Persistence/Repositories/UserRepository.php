<?php

namespace Modules\User\Infra\Persistence\Repositories;

use Modules\User\Domain\Entities\User;
use Modules\User\Domain\Repositories\UserRepositoryInterface;
use Modules\User\Infra\Persistence\Models\User as UserModel;
use Modules\Wallet\Domain\Entities\Wallet;

class UserRepository implements UserRepositoryInterface
{
    public function findById(int $id): ?User
    {
        $userModel = UserModel::find($id);
        
        return $userModel ? $this->toDomainEntity($userModel) : null;
    }

    public function findByIdWithWallet(int $id): ?User
    {
        $userModel = UserModel::with('wallet')->find($id);
        
        return $userModel ? $this->toDomainEntity($userModel) : null;
    }

    public function findByEmail(string $email): ?User
    {
        $userModel = UserModel::where('email', $email)->first();
        
        return $userModel ? $this->toDomainEntity($userModel) : null;
    }

    public function save(User $user): User
    {
       
        $userModel = new UserModel([
            'name' => $user->getName(),
            'email' => $user->getEmail(),
            'password' => $user->getPassword()
        ]);

        $userModel->save();

        return $this->toDomainEntity($userModel);
    }

    public function updateBalance(int $userId, float $balance): void
    {
        UserModel::where('id', $userId)->update(['balance' => $balance]);
    }

    private function toDomainEntity(UserModel $userModel): User
    {
        $wallet = null;
    
        if ($userModel->relationLoaded('wallet') && $userModel->wallet) {
            $wallet = new Wallet(
                $userModel->wallet->id,
                $userModel->wallet->user_id,
                $userModel->wallet->balance
            );
        }
    
        return new User(
            $userModel->id,
            $userModel->name,
            $userModel->email,
            $userModel->password,
            $wallet
        );
    }
    
}