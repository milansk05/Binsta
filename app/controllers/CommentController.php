<?php

use RedBeanPHP\R as R;

class CommentController extends BaseController
{
    public function store()
    {
        $this->requireLogin();

        $postId = isset($_POST['post_id']) ? (int)$_POST['post_id'] : null;
        $content = $_POST['content'] ?? '';

        if (!$postId || empty($content)) {
            $this->redirect('/');
            return;
        }

        $comment = R::dispense('comment');
        $comment->post_id = $postId;
        $comment->user_id = $_SESSION['user_id'];
        $comment->content = $content;
        $comment->created_at = date('Y-m-d H:i:s');

        $commentId = R::store($comment);

        // Check of het een AJAX verzoek is
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            $user = R::load('user', $_SESSION['user_id']);

            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'comment_id' => $commentId,
                'username' => $user->username,
                'content' => $content,
                'created_at' => date('d-m-Y, H:i')
            ]);
            exit;
        }

        // Redirect direct naar de post pagina om de nieuwe comment te zien
        $this->redirect("/posts/{$postId}");
    }
}
