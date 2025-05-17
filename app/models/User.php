<?php

class User extends RedBeanPHP\SimpleModel
{
    // Default profielfoto constante
    public const DEFAULT_PROFILE_IMAGE = 'placeholder_image.png';

    public function isFollowedByUser($userId)
    {
        return R::count('follow', ' follower_id = ? AND following_id = ? ', [$userId, $this->id]) > 0;
    }

    public function ownPostCount()
    {
        return R::count('post', ' user_id = ? ', [$this->id]);
    }

    public function ownFollowerCount()
    {
        return R::count('follow', ' following_id = ? ', [$this->id]);
    }

    public function ownFollowingCount()
    {
        return R::count('follow', ' follower_id = ? ', [$this->id]);
    }

    // Deze methode wordt automatisch aangeroepen door RedBean bij het laden van een user
    public function open()
    {
        // Zorg ervoor dat er altijd een profielfoto is ingesteld
        if (empty($this->profile_image)) {
            $this->profile_image = self::DEFAULT_PROFILE_IMAGE;
        }
    }
}
