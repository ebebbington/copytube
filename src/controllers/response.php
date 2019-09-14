<?php

/**
 *  The Response Controller
 * 
 * Responsible for responding to the view, whether its
 * the whole view, or from an AJAX request
 * 
 * @author Edward Bebbington
 * @copyright
 * @license
 * @method returnResponse()
 */
class ResponseController
{
    // private $result;

    // public function __construct(Array $result = [false, 'No message', null])
    // {
    //     $this->result = [
    //       'success' => $result['success'],
    //         'message' => $result['message'],
    //         'data' => $result['data']
    //     ];

    //     $this->returnResponse();
    // }

    /**
     * Respond to the View
     * 
     * This can be:
     * a: Respond with the view if the request is HTTP
     * b: Respond with the $result object if the request is AJAX
     * 
     * @param String $methodType The type of method (HTTP, AJAX)
     * @param Any $data The views path (HTTP Req) or the $result object (AJAX Req)
     */
    protected function returnResponse ($methodType, $data)
    {
        // Display the View if it's a HTTP Request
        if ($methodType === 'HTTP') {
            readfile($data);
        }
        // Respond with the $result object if AJAX
        if ($methodType === 'AJAX') {
            // Ensure we sanitise any responding data
            $data['data'] = htmlentities($data['data']);
            print json_encode($data);
        }
        exit();
    }
}