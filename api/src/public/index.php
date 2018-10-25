<?php
if (session_status() == PHP_SESSION_NONE) {
    session_set_cookie_params(3600);
    session_start();
}

/**
 * Require the autoload script, this will automatically load our classes
 * so we don't have to require a class everytime we use a class. Evertime
 * you create a new class, remember to runt 'composer update' in the terminal
 * otherwise your classes may not be recognized.
 */
require_once '../../vendor/autoload.php';

/**
 * Here we are creating the app that will handle all the routes. We are storing
 * our database config inside of 'settings'. This config is later used inside of
 * the container inside 'App/container.php'
 */

$container = require '../App/container.php';
$app = new \Slim\App($container);
$auth = require '../App/auth.php';
require '../App/cors.php';


/********************************
 *          ROUTES              *
 ********************************/


$app->get('/', function ($request, $response, $args) {
    /**
     * This fetches the 'index.php'-file inside the 'views'-folder
     */
    return $this->view->render($response, 'index.php');
});


/**
 * I added basic inline login functionality. This could be extracted to a
 * separate class. If the session is set is checked in 'auth.php'
 */
$app->post('/login', function ($request, $response, $args) {
    /**
     * Everything sent in 'body' when doing a POST-request can be
     * extracted with 'getParsedBody()' from the request-object
     * https://www.slimframework.com/docs/v3/objects/request.html#the-request-body
     */
    $body = $request->getParsedBody();
    $fetchUserStatement = $this->db->prepare('SELECT * FROM users WHERE username = :username');
    $fetchUserStatement->execute([
        ':username' => $body['username']
    ]);
    $user = $fetchUserStatement->fetch();
    if (password_verify($body['password'], $user['password'])) {
        $_SESSION['loggedIn'] = true;
        $_SESSION['userID'] = $user['id'];
        return $response->withJson(['data' => [ $user['id'], $user['username'] ]]);
    }
    return $response->withJson(['error' => 'wrong password']);
});

/**
 * Basic implementation, implement a better response
 */
$app->get('/logout', function ($request, $response, $args) {
    session_destroy();
    return $response->withJson('Success');
});


/**
 * The group is used to group everything connected to the API under '/api'
 * This was done so that we can check if the user is authed when calling '/api'
 * but we don't have to check for auth when calling '/signin'
 */
$app->group('/api', function () use ($app) {

    // GET http://localhost:XXXX/api/entries
    $app->get('/entries', function ($request, $response, $args) {
        /**
         * $this->get('entries') is available to us because we injected it into the container
         * in 'App/container.php'. This makes it easier for us to call the database
         * inside our routes.
         */
        $limit = $request->getQueryParam('limit', 20);
        $allEntries = $this->entries->getAll($limit);
        /**
         * Wrapping the data when returning as a safety thing
         * https://www.owasp.org/index.php/AJAX_Security_Cheat_Sheet#Server_Side
         */
        return $response->withJson(['data' => $allEntries]);
    });

    // GET http://localhost:XXXX/api/entries/5
    $app->get('/entries/{id}', function ($request, $response, $args) {
        /**
         * {id} is a placeholder for whatever you write after entries. So if we write
         * /entries/4 the {id} will be 4. This gets saved in the $args array
         * $args['id'] === 4
         * The name inside of '$args' must match the placeholder in the url
         * https://www.slimframework.com/docs/v3/objects/router.html#route-placeholders
         */
        $id = $args['id'];
        $singleEntry = $this->entries->getOne($id);
        return $response->withJson(['data' => $singleEntry]);
    });

    $app->delete('/entries/{id}', function ($request, $response, $args) {
        /**
         * {id} is a placeholder for whatever you write after entries. So if we write
         * /entries/4 the {id} will be 4. This gets saved in the $args array
         * $args['id'] === 4
         * The name inside of '$args' must match the placeholder in the url
         * https://www.slimframework.com/docs/v3/objects/router.html#route-placeholders
         */
        $id = $args['id'];
        $this->entries->remove($id);
        return $response->withJson('success');
    });

    $app->patch('/entries/{id}', function ($request, $response, $args) {
        /**
         * {id} is a placeholder for whatever you write after entries. So if we write
         * /entries/4 the {id} will be 4. This gets saved in the $args array
         * $args['id'] === 4
         * The name inside of '$args' must match the placeholder in the url
         * https://www.slimframework.com/docs/v3/objects/router.html#route-placeholders
         */
        $id = $args['id'];
        $body = $request->getParsedBody();
        $updatedEntry = $this->entries->update($id, $body);
        return $response->withJson(['data' => $updatedEntry]);
    });

    // POST http://localhost:XXXX/api/entries
    $app->post('/entries', function ($request, $response, $args) {
        /**
         * Everything sent in 'body' when doing a POST-request can be
         * extracted with 'getParsedBody()' from the request-object
         * https://www.slimframework.com/docs/v3/objects/request.html#the-request-body
         */
        $body = $request->getParsedBody();
        $newEntry = $this->entries->add($body);
        return $response->withJson(['data' => $newEntry]);
    });
});

$app->run();
