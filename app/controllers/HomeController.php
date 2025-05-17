<?php

use RedBeanPHP\R as R;

class HomeController extends BaseController
{
    public function index()
    {
        $this->requireLogin();

        // Posts ophalen voor de feed, met eager loading van user data
        $posts = R::findAll('post', ' ORDER BY created_at DESC LIMIT 20');

        // Voor elke post, laad de bijbehorende user in
        foreach ($posts as $post) {
            // Zorg ervoor dat de user property is ingesteld
            $post->user = R::load('user', $post->user_id);

            // Als er een sessie liked_posts bestaat, synchroniseer deze met de cache in het post-object
            if (isset($_SESSION['liked_posts']) && isset($_SESSION['liked_posts'][$post->id])) {
                $post->setLikedByCurrentUser(true);
            }
        }

        // Render de homepage met posts
        $this->render('home/index.twig', [
            'posts' => $posts
        ]);
    }
}
