<?php

use RedBeanPHP\R as R;

class BaseController
{
    // Constante voor standaard profielfoto
    protected const DEFAULT_PROFILE_IMAGE = 'placeholder_image.png';

    protected $twig;

    protected function loadModels()
    {
        $models = glob(__DIR__ . '/../models/*.php');
        foreach ($models as $model) {
            require_once $model;
        }
    }

    public function __construct()
    {
        // Session starten als die nog niet is gestart
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // Modellen laden
        $this->loadModels();

        // Twig setup
        $loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../views');
        $this->twig = new \Twig\Environment($loader, [
            'cache' => false,
            'debug' => true,
        ]);

        // Add Extensions
        $this->twig->addExtension(new \Twig\Extension\DebugExtension());

        // Global variabelen voor alle views
        $this->twig->addGlobal('session', $_SESSION);
        $this->twig->addGlobal('DEFAULT_PROFILE_IMAGE', self::DEFAULT_PROFILE_IMAGE);
    }

    protected function render($template, $data = [])
    {
        echo $this->twig->render($template, $data);
    }

    protected function redirect($url)
    {
        header("Location: $url");
        exit();
    }

    protected function isLoggedIn()
    {
        return isset($_SESSION['user_id']);
    }

    protected function requireLogin()
    {
        if (!$this->isLoggedIn()) {
            $this->redirect('/login');
        }
    }
}
