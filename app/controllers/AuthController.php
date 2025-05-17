<?php

use RedBeanPHP\R as R;

class AuthController extends BaseController
{
    // Constante voor standaard profielfoto - moet protected of public zijn, niet private
    protected const DEFAULT_PROFILE_IMAGE = 'placeholder_image.png';

    public function loginForm()
    {
        if ($this->isLoggedIn()) {
            $this->redirect('/');
        }

        $this->render('auth/login.twig');
    }

    public function login()
    {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        $user = R::findOne('user', ' email = ? ', [$email]);

        if ($user && password_verify($password, $user->password)) {
            $_SESSION['user_id'] = $user->id;
            $_SESSION['username'] = $user->username;

            $this->redirect('/');
        } else {
            $this->render('auth/login.twig', [
                'error' => 'Ongeldige inloggegevens'
            ]);
        }
    }

    public function registerForm()
    {
        if ($this->isLoggedIn()) {
            $this->redirect('/');
        }

        $this->render('auth/register.twig');
    }

    public function register()
    {
        $username = $_POST['username'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        // Validatie
        if (empty($username) || empty($email) || empty($password)) {
            $this->render('auth/register.twig', [
                'error' => 'Alle velden zijn verplicht'
            ]);
            return;
        }

        // Check of email al bestaat
        $exists = R::findOne('user', ' email = ? ', [$email]);
        if ($exists) {
            $this->render('auth/register.twig', [
                'error' => 'Email is al in gebruik'
            ]);
            return;
        }

        // Gebruiker aanmaken
        $user = R::dispense('user');
        $user->username = $username;
        $user->email = $email;
        $user->password = password_hash($password, PASSWORD_DEFAULT);
        $user->bio = '';
        $user->profile_image = self::DEFAULT_PROFILE_IMAGE;
        $user->created_at = date('Y-m-d H:i:s');

        $id = R::store($user);

        // Inloggen
        $_SESSION['user_id'] = $id;
        $_SESSION['username'] = $username;

        $this->redirect('/');
    }

    public function logout()
    {
        session_destroy();
        $this->redirect('/login');
    }
}
