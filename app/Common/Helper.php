<?php

namespace App\Common;

class Helper {
    function checkExistDirectory($directory){
        if (!is_dir($directory)){
            mkdir($directory, 0777, true);
        }
    }
}
