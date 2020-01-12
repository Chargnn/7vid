<?php
namespace App\Services;
use App\SocialGoogleAccount;
use App\User;
use Laravel\Socialite\Contracts\User as ProviderUser;
class SocialGoogleAccountService
{
    public function createOrGetUser(ProviderUser $providerUser) : User
    {
        $account = SocialGoogleAccount::whereProvider('google')->whereProviderUserId($providerUser->getId())->first();

        if ($account) {
            return $account->user;
        }

        $account = new SocialGoogleAccount([
            'provider_user_id' => $providerUser->getId(),
            'provider' => 'google'
        ]);

        $user = User::whereEmail($providerUser->getEmail())->first();

        if (!$user) {
            try {
                $user = User::create(
                    [
                        'email' => $providerUser->getEmail(),
                        'name' => $providerUser->getName(),
                        'password' => md5(random_int(1, 10000)),
                    ]
                );
            } catch (\Exception $e) {
                var_dump($e->getMessage());
                exit(1);
            }
        }
        $account->user()->associate($user);
        $account->save();

        return $user;
    }
}
