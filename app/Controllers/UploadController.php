<?php

namespace App\Controllers;

class UploadController
{
    protected $loader;
    protected $twig;
    private $imageName;
    private $file;
    private $fileExtention;

    public function __construct()
    {
        $this->loader = $loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../../views');
        $this->twig = $twig = new \Twig\Environment($loader, [
            'cache' => false,
        ]);
    }

    public function index()
    {
        $directory = $_SERVER['DOCUMENT_ROOT'] . "/storage/";
        $folder = scandir($directory);
        $images = array_diff($folder, ['..', '.']);
        echo $this->twig->render('fileUpload/index.html.twig', ['images' => $images, 'directory' => $directory]);
        exit;
    }

    public function create()
    {
        echo $this->twig->render('fileUpload/create.html.twig');
        exit;
    }

    public function upload()
    {
        $this->imageName = $_POST['image-name'];
        $this->file = $_FILES['image'];
        $this->fileExtention = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);

        $this->isPostEmpty();
        $this->isFileEmpty();
        $this->checkFileType();
        $this->checkFileSize();
        $this->checkFileWidth();
        $this->checkFileHeigth();
        $this->fileNameExist();
        $this->save();
    }

    protected function isPostEmpty()
    {
        if (!isset($this->imageName) || empty($this->imageName)) {
            echo $this->twig->render('fileUpload/create.html.twig', ['error' => "Le nom de l'image est obligatoire !", 'oldName' => $this->imageName]);
            exit;
        }
    }

    protected function isFileEmpty()
    {
        if (!isset($this->file['name']) || empty($this->file['name']) || !$this->file['error'] == 0) {
            echo $this->twig->render('fileUpload/create.html.twig', ['error' => "Une image est obligatoire !", 'oldName' => $this->imageName]);
            exit;

        }
    }

    protected function checkFileType()
    {
        if (pathinfo($this->file['type'])['dirname'] != 'image') {
            echo $this->twig->render('fileUpload/create.html.twig', ['error' => "Le type de fichier n'est pas conforme !", 'oldName' => $this->imageName]);
            exit;
        }
    }

    protected function checkFileSize()
    {
        if($this->file['size'] > 600000) {
            echo $this->twig->render('fileUpload/create.html.twig', ['error' => "Votre image est trop lourde !", 'oldName' => $this->imageName]);
            exit;
        }
    }

    protected function checkFileWidth()
    {
        if(getimagesize($this->file['tmp_name'])[0] > 1200) {
            $_SESSION['alerts']['imageRequired'] = "Votre image est trop large !";
            echo $this->twig->render('fileUpload/create.html.twig', ['error' => "Votre image est trop large !", 'oldName' => $this->imageName]);
            exit;
        }
    }

    protected function checkFileHeigth()
    {
        if(getimagesize($this->file['tmp_name'])[1] > 600) {
            echo $this->twig->render('fileUpload/create.html.twig', ['error' => "Votre image est trop haute !", 'oldName' => $this->imageName]);
            exit;
        }
    }

    protected function fileNameExist()
    {
        if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/storage/" . $this->imageName .'.'. $this->fileExtention)) {
            echo $this->twig->render('fileUpload/create.html.twig', ['error' => "Cette image existe deja merci de la renommer !", 'oldName' => $this->imageName]);
            exit;
        }
    }

    protected function save()
    {
        move_uploaded_file($this->file['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . "/storage/" . basename($this->imageName .'.'. $this->fileExtention));
        echo $this->twig->render('fileUpload/index.html.twig', ['success' => "Votre image a été inseré avec succès!"]);
        exit;
    }
}
