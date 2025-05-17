<?php

use RedBeanPHP\R as R;

class PostController extends BaseController
{
    public function create()
    {
        $this->requireLogin();
        $this->render('posts/create.twig');
    }

    public function store()
    {
        $this->requireLogin();

        $code = $_POST['code'] ?? '';
        $language = $_POST['language'] ?? '';
        $caption = $_POST['caption'] ?? '';

        if (empty($code) || empty($language)) {
            $this->render('posts/create.twig', [
                'error' => 'Code snippet en taal zijn verplicht',
                'code' => $code,
                'language' => $language,
                'caption' => $caption
            ]);
            return;
        }

        $post = R::dispense('post');
        $post->user_id = $_SESSION['user_id'];
        $post->code = $code;
        $post->language = $language;
        $post->caption = $caption;
        $post->created_at = date('Y-m-d H:i:s');

        R::store($post);

        $this->redirect('/');
    }

    public function show($id)
    {
        $this->requireLogin();

        $post = R::load('post', $id);

        if (!$post->id) {
            $this->redirect('/');
            return;
        }

        // Laad de gebruiker direct
        $user = R::load('user', $post->user_id);

        // Zorg ervoor dat we de post ID als integer gebruiken (heel belangrijk!)
        $postId = (int)$id;

        // Haal reacties direct op met een eenvoudige query
        $comments = R::find('comment', 'post_id = ?', [$postId]);

        // Als er commentaren zijn, laad de gebruikers voor elk commentaar
        if ($comments) {
            foreach ($comments as $comment) {
                // Converteer de user_id naar een integer
                $userId = (int)$comment->user_id;
                $comment->user = R::load('user', $userId);
            }
        } else {
            $comments = []; // Zorg voor een lege array als er geen comments zijn
        }

        $this->render('posts/show.twig', [
            'post' => $post,
            'user' => $user,
            'comments' => $comments
        ]);
    }

    public function like()
    {
        $this->requireLogin();

        $postId = $_POST['post_id'] ?? null;

        if (!$postId) {
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                header('Content-Type: application/json');
                echo json_encode(['success' => false]);
                exit;
            }

            $this->redirect('/');
            return;
        }

        // Check of de like al bestaat
        $like = R::findOne('like', ' post_id = ? AND user_id = ? ', [$postId, $_SESSION['user_id']]);

        if ($like) {
            // Unlike
            R::trash($like);
            $liked = false;
        } else {
            // Like
            $like = R::dispense('like');
            $like->post_id = $postId;
            $like->user_id = $_SESSION['user_id'];
            $like->created_at = date('Y-m-d H:i:s');

            R::store($like);
            $liked = true;
        }

        $likesCount = (int)R::count('like', ' post_id = ? ', [$postId]);

        // Sla de like status op in de sessie om deze persistent te maken tussen pagina refreshes
        if (!isset($_SESSION['liked_posts'])) {
            $_SESSION['liked_posts'] = [];
        }

        if ($liked) {
            $_SESSION['liked_posts'][$postId] = true;
        } else {
            unset($_SESSION['liked_posts'][$postId]);
        }

        // Check of het een AJAX verzoek is
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'liked' => $liked,
                'likes' => $likesCount
            ]);
            exit;
        }

        // Redirect terug naar vorige pagina
        $this->redirect($_SERVER['HTTP_REFERER'] ?? '/');
    }
}
