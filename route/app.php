<?php

use Models\Log;
use Models\Database;
use Models\Users;
use Models\Table;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

$app->get('/', function (ServerRequestInterface $request, ResponseInterface $response, array $args) {
    return $this->view->render($response, '/index.twig');
});

$app->get('/about', function (ServerRequestInterface $request, ResponseInterface $response, array $args) {
    return $this->view->render($response, '/about.twig');
});

$app->get('/contact', function (ServerRequestInterface $request, ResponseInterface $response, array $args) {
    return $this->view->render($response, '/contact.twig');
});

$app->post('/contact', function (ServerRequestInterface $request, ResponseInterface $response, array $args) {
    /*if (!empty($_POST['email']) && !empty($_POST['name']) && !empty($_POST['comment'])) {
        $mailFrom = $_POST['email'];
        $name = $_POST['name'];
        $subject = $_POST['subject'];
        $message = $_POST['comment'];
        $phone = $_POST['number'];

        $mailTo = 'post@haukerodskolekorps.no';
        $headers = 'From: ' .$mailFrom;
        $txt = 'You have received an e-mail from '.$name.".\n\n".$message;

        mail($mailTo, $subject, $txt, $headers);
    }
    return $response->withRedirect('/contact');
    */
});


$app->get('/info', function (ServerRequestInterface $request, ResponseInterface $response, array $args) {
    return $this->view->render($response, '/info.twig');
});

$app->get('/calendar', function (ServerRequestInterface $request, ResponseInterface $response, array $args) {
    if (!User::isAuthenticated()) {
        return $response->withRedirect(signInUrl());
    }
    return $this->view->render($response, '/calender.twig');
});

$app->get('/sign-in', function(ServerRequestInterface $request, ResponseInterface $response, array $args) {
    return $this->view->render($response, 'auth/sign-in.twig');
});

$app->post('/sign-in', function(ServerRequestInterface $request, ResponseInterface $response, array $args) {
        if (!empty($_POST['email']) && !empty($_POST['password'])) {
            Users::logIn($_POST['email'], $_POST['password']);
        }

    return $response->withRedirect('/user-info');
});

$app->get('/user-info', function(ServerRequestInterface $request, ResponseInterface $response, array $args) {
    if (Users::isAuthenticated()) {
        $info = Table::selectAll('users', ['*'], ['id' => $_SESSION['user']->id]);
        $stmt = Database::get()->prepare('SELECT band_members.*, band.type FROM band_members LEFT JOIN band ON band_members.band_id=band.id');
        $stmt->execute();
        $band = $stmt->fetchAll();
        return $this->view->render($response, 'auth/user-info.twig', ['info' => $info, 'band' => $band]);
    } else {
        return $response->withRedirect('/sign-in');
    }

});

$app->get('/user-info/update', function(ServerRequestInterface $request, ResponseInterface $response, array $args) {
    $data = Table::selectAll('users', ['*'], ['id' => $_SESSION['user']->id]);
    return $this->view->render($response, 'auth/user-info_add.twig', ['data' => $data]);
});

$app->post('/user-info/update', function(ServerRequestInterface $request, ResponseInterface $response, array $args) {
    Table::update('users', ['birth_date' => $_POST['birth_date'], 'address' => $_POST['address'], 'number' => $_POST['number'] ], ['id' => $_SESSION['user']->id]);
    return $response->withRedirect('/user-info');
});

$app->get('/sign-up', function(ServerRequestInterface $request, ResponseInterface $response, array $args) {
    return $this->view->render($response, 'auth/sign-up.twig');
});

$app->post('/sign-up', function(ServerRequestInterface $request, ResponseInterface $response, array $args) {
    if (!empty($_POST['email']) && !empty($_POST['password']) && !empty($_POST['password2'])) {
        Users::register($_POST['email'], $_POST['password'], $_POST['password2'], $_POST['name']);
    }
    return $response->withRedirect('/sign-in');
});

$app->get('/sign-out', function (ServerRequestInterface $request, ResponseInterface $response, array $args) {

    Users::logOut();

    return $response->withRedirect('/sign-in');
});

$app->group('/musician', function () use($app) {
    $app->get('/forum', function (ServerRequestInterface $request, ResponseInterface $response, array $args) {
        $posts = Table::selectAll('posts', ['*']);
        return $this->view->render($response, '/auth/forum.twig', ['post' => $posts]);
    });

});

$app->get('/bands/show', function(ServerRequestInterface $request, ResponseInterface $response, array $args) {
    $stmt = Database::get()->prepare('SELECT * FROM band ORDER BY id DESC LIMIT 3');
    $stmt->execute();
    $data = $stmt->fetchAll();
    return $this->view->render($response, 'auth/show_band.twig', ['bands' => $data]);
});

$app->get('/join/band/{id}', function(ServerRequestInterface $request, ResponseInterface $response, array $args) {
    Table::insert('band_members',  ['band_id' => $args['id'], 'user_id' => $_SESSION['user']->id]);
    return $response->withRedirect('/user-info');
});

$app->group('/admin', function () use($app) {

    $app->get('/band/create', function (ServerRequestInterface $request, ResponseInterface $response, array $args) {
       return $this->view->render($response, 'auth/create_band.twig');
    });

    $app->post('/band/create', function (ServerRequestInterface $request, ResponseInterface $response, array $args) {
        if (isset($_POST['band']) && isset($_POST['start']) && isset($_POST['end'])) {
            Table::insert('band', ['type' => $_POST['band'], 'start' => $_POST['start'], 'end' => $_POST['end']]);
        } else {
            return $this->view->render($response, 'auth/create_band.twig');
        }
        return $response->withRedirect('/band/show');
    });


    $app->get('/lists', function(ServerRequestInterface $request, ResponseInterface $response, array $args) {

        $hoved = Table::selectAll('lists', ['*'], ['band' => 0]);
        $junior = Table::selectAll('lists', ['*'], ['band' => 1]);
        $aspirant = Table::selectAll('lists', ['*'], ['band' => 2]);
        foreach ($hoved as &$row) {
            \Carbon\Carbon::createFromTimestamp(strtotime($row->birth_date));
        }
        return $this->view->render($response, 'auth/lists.twig', ['hoved' => $hoved, 'aspirant' => $aspirant, 'junior' => $junior]);
    });




});



