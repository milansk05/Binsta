<?php

use RedBeanPHP\R as R;

class ProfileController extends BaseController
{
    public function show()
    {
        $this->requireLogin();

        $userId = $_GET['id'] ?? $_SESSION['user_id'];

        $user = R::load('user', $userId);
        if (!$user->id) {
            $this->redirect('/');
            return;
        }        // Posts van de gebruiker - direct als array exporteren
        $posts = R::findAll('post', ' user_id = ? ORDER BY created_at DESC', [$userId]);

        $this->render('profile/show.twig', [
            'profile' => $user,
            'posts' => $posts,
            'postCount' => count($posts)
        ]);
    }

    public function edit()
    {
        $this->requireLogin();

        $user = R::load('user', $_SESSION['user_id']);

        $this->render('profile/edit.twig', [
            'user' => $user
        ]);
    }

    public function update()
    {
        $this->requireLogin();

        $username = $_POST['username'] ?? '';
        $bio = $_POST['bio'] ?? '';

        if (empty($username)) {
            $this->render('profile/edit.twig', [
                'error' => 'Gebruikersnaam is verplicht',
                'user' => R::load('user', $_SESSION['user_id'])
            ]);
            return;
        }

        $user = R::load('user', $_SESSION['user_id']);
        $user->username = $username;
        $user->bio = $bio;

        // Profielfoto verwerken als die is geüpload
        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
            $tempFile = $_FILES['profile_image']['tmp_name'];
            $extension = pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION);
            $filename = uniqid() . '.' . $extension;
            $targetPath = __DIR__ . '/../../public/uploads/' . $filename;

            if (move_uploaded_file($tempFile, $targetPath)) {
                // Oude profielfoto verwijderen als het geen default of placeholder is
                if ($user->profile_image !== 'default.jpg' && $user->profile_image !== self::DEFAULT_PROFILE_IMAGE) {
                    $oldFilePath = __DIR__ . '/../../public/uploads/' . $user->profile_image;
                    if (file_exists($oldFilePath)) {
                        @unlink($oldFilePath);
                    }
                }

                $user->profile_image = $filename;
            }
        }

        R::store($user);
        $_SESSION['username'] = $username;

        $this->redirect('/profile');
    }

    public function updatePassword()
    {
        $this->requireLogin();

        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
            $this->render('profile/edit.twig', [
                'error' => 'Alle velden zijn verplicht',
                'user' => R::load('user', $_SESSION['user_id'])
            ]);
            return;
        }

        if ($newPassword !== $confirmPassword) {
            $this->render('profile/edit.twig', [
                'error' => 'Nieuwe wachtwoorden komen niet overeen',
                'user' => R::load('user', $_SESSION['user_id'])
            ]);
            return;
        }

        $user = R::load('user', $_SESSION['user_id']);

        if (!password_verify($currentPassword, $user->password)) {
            $this->render('profile/edit.twig', [
                'error' => 'Huidig wachtwoord is onjuist',
                'user' => $user
            ]);
            return;
        }

        $user->password = password_hash($newPassword, PASSWORD_DEFAULT);
        R::store($user);

        $this->redirect('/profile');
    }
}
