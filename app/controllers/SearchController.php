<?php

use RedBeanPHP\R as R;

class SearchController extends BaseController
{
    public function index()
    {
        $this->requireLogin();

        $query = $_GET['q'] ?? '';
        $results = [];

        if (!empty($query)) {
            $results = R::find('user', ' username LIKE ? ', ['%' . $query . '%']);
        }

        $this->render('search/index.twig', [
            'query' => $query,
            'results' => $results
        ]);
    }
}
