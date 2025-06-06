<?php

namespace App\Service;

use App\Entity\Participant;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;

class SupprFileService
{

    public function supprFile(string $imageURL) : void {

        $filesystem = new Filesystem();
        //vérifie que l'image existe
        if ($filesystem->exists($imageURL)) {
            try{
                //supprime l'image du dossier upload/img
                $filesystem->remove($imageURL);
            } catch (IOExceptionInterface $exception) {
                throw new IOException("Impossible de supprimer le fichier ".$exception->getMessage());
            }
        }
    }

}