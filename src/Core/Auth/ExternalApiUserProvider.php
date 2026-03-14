<?php

namespace HashtagCms\Core\Auth;

use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Auth\Authenticatable;
use HashtagCms\Models\User;
use HashtagCms\Core\Utils\CacheKeys;


class ExternalApiUserProvider implements UserProvider
{
    /**
     * Retrieve a user by their unique identifier.
     *
     * @param  mixed  $identifier
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveById($identifier)
    {
        // Check if we have user data in session
        if (session()->has(CacheKeys::CMS_API_USER)) {
            $userData = session(CacheKeys::CMS_API_USER);
            // Ensure the ID matches what we are looking for, usually this is called by session guard
            if ($userData['id'] == $identifier) {
                return $this->getGenericUser($userData);
            }
        }
        return null;
    }

    /**
     * Retrieve a user by their unique identifier and "remember me" token.
     *
     * @param  mixed  $identifier
     * @param  string  $token
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByToken($identifier, $token)
    {
        // Not implemented for stateless/API usually, unless we want to store remember token in session
        return null;
    }

    /**
     * Update the "remember me" token for the given user in storage.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  string  $token
     * @return void
     */
    public function updateRememberToken(Authenticatable $user, $token)
    {
        // No storage to update
    }

    /**
     * Retrieve a user by the given credentials.
     *
     * @param  array  $credentials
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByCredentials(array $credentials)
    {
        // This is called during attempt(). 
        // In our LoginController override, we handle the API call manually.
        // But if we want Auth::attempt() to work standardly, we could call API here.
        // However, LoginController is already customized.
        // We will return null here because we expect `retrieveById` to do the heavy lifting 
        // after we manually put data in session, OR we return a dummy user if LoginController passes one.

        return null;
    }

    /**
     * Validate a user against the given credentials.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  array  $credentials
     * @return bool
     */
    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        // Logic handled in LoginController via API call
        return true;
    }

    /**
     * Rehash the user's password if required and supported.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  array  $credentials
     * @param  bool  $force
     * @return void
     */
    public function rehashPasswordIfRequired(Authenticatable $user, array $credentials, bool $force = false)
    {
        // Handled by External API. No hashing required locally.
    }

    /**
     * Get a generic user instance.
     *
     * @param  array  $user
     * @return User
     */
    protected function getGenericUser($user)
    {
        // We hydrate a User model but mark it as non-existing to prevent DB saves
        $userModel = new User();
        $userModel->forceFill($user);
        $userModel->exists = true; // Pretend it exists so policies/gates might work? 
        // Actually, setting exists=false prevents save(), but standard Auth checks might want an ID.
        // Let's set the ID manually.
        $userModel->id = $user['id'] ?? null;

        return $userModel;
    }
}
