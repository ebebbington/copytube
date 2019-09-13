<?php
/**
 * Created by PhpStorm.
 * User: Edward
 * Date: 25/02/2019
 * Time: 11:25
 */

require_once $_SERVER[ 'DOCUMENT_ROOT' ] . '/models/comments.php';
require_once $_SERVER[ 'DOCUMENT_ROOT' ] . '/models/validate.php';
require_once $_SERVER[ 'DOCUMENT_ROOT' ] . '/controllers/response.php';

//
// Set data
//
$data            = $_POST ?? $_GET;
$action          = $data[ 'action' ];
$Comments        = new Comments();

//
// Handle the different actions
//
switch ($action) {
    case 'getComments':
        $result = $Comments->getComments($data[ 'videoTitle' ]);
        new Response($result);
        break;
    case 'addComment':
        $Validate = new Validate();
        $checkComment  = $Validate->validateComment($data);
        if ( ! $checkComment) {
            $requestResponse->returnResponse(
              false,
              'Comment could not be validated',
              null
            );
        }
        if ($checkComment) {
            $addComment = $Comments->addComment($data);
            if (!$addComment) {
                $requestResponse->returnResponse(
                  false,
                  'Comment could not be added to the database',
                  null
                );
            }
            if ($addComment) {
                $requestResponse->returnResponse(
                  true,
                  'Comment was successfully added to the database',
                  $addComment
                );
            }
        }
        $Comments->addComment($data);
        print_r(json_encode($comment));
        break;

    default:
        print json_encode([
          'success' => false,
          'message' => 'Action requested does not exist',
          'data' => null
        ]);
        break;
}