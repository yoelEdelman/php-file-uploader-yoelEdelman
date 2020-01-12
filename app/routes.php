<?php

use App\Controllers\UploadController;

if (!isset($_GET['id'])) {
    $_GET['id'] = null;
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if ($_SERVER['REQUEST_URI'] =='/') {
        $uploadController = new UploadController();
        $uploadController->index();
    }
    elseif ($_SERVER['REQUEST_URI'] == '/create') {
        $uploadController = new UploadController();
        $uploadController->create();
    }
    else {
        return http_response_code(404);
    }
}
elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($_SERVER['REQUEST_URI'] =='/create') {
        $uploadController = new UploadController();
        $uploadController->upload();
    }
    else {
        return http_response_code(404);
    }
}
else {
    echo 'Method not allowed';
    return http_response_code(405);
}