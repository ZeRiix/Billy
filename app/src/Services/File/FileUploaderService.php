<?php

namespace App\Services\File;

use App\Entity\Organization;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploaderService
{
	private string $targetDirectory;

	public function __construct(string $targetDirectory)
	{
		$this->targetDirectory = $targetDirectory;
	}

	public function uploadImage(UploadedFile $file, Organization $organization): void
	{
		$fileName = $organization->getId() . ".jpeg";

		try {
			$file->move($this->targetDirectory, $fileName);
		} catch (FileException $e) {
			throw new FileException("Une erreur est survenue lors de l'upload du fichier.");
		}
	}
}
