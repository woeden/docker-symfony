<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Attributes as OA;

#[Route('/user')]
class UserController extends AbstractController
{


    private SerializerInterface $serializer;
    private ManagerRegistry $doctrine;

    /**
     * @param Serializer $serializer
     * @param ManagerRegistry $managerRegistry
     */
    public function __construct(SerializerInterface $serializer, ManagerRegistry $managerRegistry)
    {
        $this->serializer = $serializer;
        $this->doctrine = $managerRegistry;
    }

    #[OA\Response(
        response: 404,
        description: 'Successful response',
    )]
    #[Route('/create', name: 'user_create', methods: ["POST"])]
    public function index(Request $request, UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];

        $this->serializer = new Serializer($normalizers, $encoders);

        /** @var User $user */
        $user = $this->serializer->deserialize($request->getContent(), User::class, 'json');

        $hashedPassword = $passwordHasher->hashPassword(
            $user,
            $user->getPassword()
        );

        $user->setPassword($hashedPassword);

        $user->setRoles(["ROLE_USER"]);

        try{
            $this->doctrine->getManager()->persist($user);
            $this->doctrine->getManager()->flush();
        }catch (\Exception $e)
        {
            return $this->json(["message"=>$e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->json($user, 200);
    }

    #[Route('/activate/{id}', name: 'user_activate', methods: ["PATCH"])]
    public function activate(Request $request, User $user): JsonResponse
    {
        $user->setActive(true);
        $this->doctrine->getManager()->persist($user);
        $this->doctrine->getManager()->flush();
        return $this->json($user);
    }
}
