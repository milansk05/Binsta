<?php

class Post extends RedBeanPHP\SimpleModel
{
    // Cache voor de isLikedByUser resultaten
    private $userLikeStatus = [];

    public function isLikedByUser($userId)
    {
        // Gebruik cache als die bestaat om te voorkomen dat we steeds opnieuw de database bevragen
        if (!isset($this->userLikeStatus[$userId])) {
            // Als we in de sessie hebben opgeslagen dat deze post geliked is door de huidige gebruiker
            if (isset($_SESSION['liked_posts']) && isset($_SESSION['liked_posts'][$this->id]) && $userId == $_SESSION['user_id']) {
                $this->userLikeStatus[$userId] = true;
            } else {
                // Anders controleren we in de database
                $like = R::findOne('like', ' post_id = ? AND user_id = ? ', [$this->id, $userId]);
                $this->userLikeStatus[$userId] = !empty($like);

                // Update de sessie voor de huidige gebruiker
                if ($userId == $_SESSION['user_id']) {
                    if (!isset($_SESSION['liked_posts'])) {
                        $_SESSION['liked_posts'] = [];
                    }

                    if ($this->userLikeStatus[$userId]) {
                        $_SESSION['liked_posts'][$this->id] = true;
                    } else {
                        unset($_SESSION['liked_posts'][$this->id]);
                    }
                }
            }
        }

        return $this->userLikeStatus[$userId];
    }

    // Methode om de like status direct in te stellen (voor bij sessie synchronisatie)
    public function setLikedByCurrentUser($liked)
    {
        if (isset($_SESSION['user_id'])) {
            $this->userLikeStatus[$_SESSION['user_id']] = $liked;
        }
    }

    public function getLikes()
    {
        return R::count('like', ' post_id = ? ', [$this->id]);
    }

    public function getComments()
    {
        $comments = R::findAll('comment', ' post_id = ? ORDER BY created_at ASC ', [$this->id]);

        // Maak er een lege array van als er geen comments zijn, in plaats van null
        if (!$comments) {
            return [];
        }

        return $comments;
    }

    public function getUser()
    {
        return R::load('user', $this->user_id);
    }

    // Deze methode wordt automatisch aangeroepen door RedBean bij het laden van een post
    public function open()
    {
        // Stel user property in als het nog niet bestaat
        if (!isset($this->user) || !$this->user->id) {
            $this->user = $this->getUser();
        }

        // Synchroniseer like status met sessie als die bestaat
        if (isset($_SESSION['user_id']) && isset($_SESSION['liked_posts']) && isset($_SESSION['liked_posts'][$this->id])) {
            $this->setLikedByCurrentUser(true);
        }
    }
}
