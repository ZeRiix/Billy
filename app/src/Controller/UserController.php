<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
	private $manager;

	private $user;

	public function __construct(EntityManagerInterface $manager, UserRepository $user)
	{
		$this->manager = $manager;
		$this->user = $user;
	}

    #[Route('/api/user', name: 'create_user', methods: 'POST')]
    public function create(Request $request): Response
    {
		$responseStatus = false;
		$responseCode = 200;
		$responseMessage = "";

		$data = json_decode($request->getContent(), true);

		$email = $data["email"];
		$password = $data["password"];

		$emailExist = $this->user->findOneByEmail($email);

		if ($emailExist) {
			$responseCode = 409;
			$responseMessage = "Cet email existe déjà!";
		} else {
			$user = new User($email, $password);

			if ($user->getEmail() !== null) {
				$responseStatus = true;
				$responseMessage = "L'utilisateur à bien été ajouté!";

				$this->manager->persist($user);
				$this->manager->flush();
			} else {
				$responseCode = 400;
				$responseStatus = false;
				$responseMessage = "L'email n'est pas valide!";
			}
		}
		return new JsonResponse(
			[
				"code" => $responseCode,
				"status" => $responseStatus,
				"message" => $responseMessage
			]
		);
    }

	#[Route('/api/users', name: 'all_user', methods: 'GET')]
    public function getUsers(): Response
    {
		$users = $this->user->findAll();
		return $this->json($users);
	}
}
