<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class UploadImageService
{

    public function __construct(private readonly SluggerInterface      $slugger,
                                private readonly ParameterBagInterface $parameterBag)
    {

    }

    public function upload(UploadedFile $imageFile): ?string
    {

        if ($imageFile) {

            $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $this->slugger->slug($originalFilename);
            $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

            try {
                $imageFile->move(
                    $this->parameterBag->get('images_directory'),
                    $newFilename
                );
                return $newFilename;
            } catch (FileException $e) {
                throw new FileException("Une erreur est survenue lors du chargement du fichier." . $e->getMessage(), $e->getCode(), $e);
            }

        }

        return null;

    }

}