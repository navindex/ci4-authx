<?php

namespace Navindex\Auth\Models;

use Navindex\Auth\Entities\UserToken;
use Navindex\Auth\Models\Base\BaseModel;
use Navindex\Auth\Models\TokenModelInterface;

class UserTokenModel extends BaseModel implements TokenModelInterface
{
    protected $table = 'user_token';
    protected $primaryKey = 'id';
    protected $uniqueKeys = [];
    protected $returnType = UserToken::class;

    protected $allowedFields = [
        'user_id',
        'selector',
        'validator_hash',
        'expires_at',
        'deleted',
        'creator_id',
    ];
    protected $validationRules = [
        'user_id'        => 'is_not_unique[style.id]',
        'selector'       => 'required',
        'validator_hash' => 'required',
        'expires_at'     => 'required|valid_date',
    ];

    //--------------------------------------------------------------------

    /**
     * Stores a remember-me token for the user.
     *
     * @param int    $userID    User ID
     * @param string $selector  Device selector
     * @param string $validator Validator hash
     * @param string $expires   Expiry date and time (UTC)
     *
     * @return false|int Token ID or false
     */
    public function rememberUser(int $userID, string $selector, string $validator, string $expires)
    {
        return $this->insert([
            'user_id'        => $userID,
            'selector'       => $selector,
            'validator_hash' => $validator,
            'expires'        => (new \DateTime($expires, new \DateTimeZone('UTC')))->format('Y-m-d H:i:s'),
        ]);
    }

    //--------------------------------------------------------------------

    /**
     * Returns the remember-me token info for a given selector.
     *
     * @param string $selector Device selector
     *
     * @return null|\App\Entities\UserToken Token entity
     */
    public function getToken(string $selector): ?UserToken
    {
        return $this->where('selector', $selector)->get()->getRow();
    }

    //--------------------------------------------------------------------

    /**
     * Updates the validator for a given selector.
     *
     * @param string $selector  Device selector
     * @param string $validator Validator hash
     *
     * @return bool True if the operation was successful, false otherwise
     */
    public function updateValidator(string $selector, string $validator): bool
    {
        return $this->where('selector', $selector)->update(['validator_hash' => hash('sha256', $validator)]);
    }

    //--------------------------------------------------------------------

    /**
     * Removes all persistent login tokens (remember-me) for a single user
     * across all devices they may have logged in with.
     *
     * @param int $userID User ID
     *
     * @return bool True if the operation was successful, false otherwise
     */
    public function purgeUserTokens(int $userId): bool
    {
        return $this->where(['user_id' => $userId])->delete();
    }

    //--------------------------------------------------------------------

    /**
     * Purges any records that are past
     * their expiration date already.
     *
     * @return bool True if the operation was successful, false otherwise
     */
    public function purgeExpiredTokens(): bool
    {
        if ((config('Auth'))->allowRemembering) {
			return $this->where('expires_at <=', gmdate('Y-m-d H:i:s'))->delete();
        }

        return true;
    }
}
