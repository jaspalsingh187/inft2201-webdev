<?php
namespace Application;

class Page {
    private function send($code, $payload) {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode($payload);
    }

    public function list($items) {
        $this->send(200, $items);
    }

    public function item($item, $code = 200) {
        $this->send($code, $item);
    }

    public function notFound() {
        $this->send(404, ["error" => "Not found"]);
    }

    public function badRequest() {
        $this->send(400, ["error" => "Bad request"]);
    }
}
