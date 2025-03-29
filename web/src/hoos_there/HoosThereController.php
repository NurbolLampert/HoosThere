<?php

class HoosThereController {

    public function __construct($input, $include_path) {
        $this->input = $input;
        $this->include_path = $include_path;
        session_start();
    }

    public function run() {
        if (isset($this->input["command"])) {
            $command = $this->input["command"];
        } else {
            $command = null;
        }

        switch($command) {
            case "home":
            default:
            $this->showTemplate("/templates/home.php");
                break;
        }
    }

    private function showTemplate($template) {
        include($this->include_path . $template);
    }

}