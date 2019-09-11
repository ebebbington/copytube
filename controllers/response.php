<?php

class Response
{
    private $result;

    //
    // Assign data and call the isSet function
    //
    public function __construct($result = [false, 'No message', null])
    {
        $this->result = [
          'success' => $result['success'],
            'message' => $result['message'],
            'data' => $result['data']
        ];

        $this->returnResponse();
    }

    //
    // Return the response
    //
    function returnResponse ()
    {
        print_r(json_encode($this->result));
        return false;
    }
}