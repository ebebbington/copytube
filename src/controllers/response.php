<?php

class Response
{
    private $result;

    public function __construct(Array $result = [false, 'No message', null])
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
    private function returnResponse ()
    {
        print json_encode($this->result);
        exit();
    }
}