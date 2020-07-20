<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Firebase\JWT\JWT;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\Token\PostAuthenticationGuardToken;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class SecurityController extends AbstractController
{

    /**
     * @Route("/register", name="register", methods={"GET","POST"})
     */
    public function register(Request $request, UserPasswordEncoderInterface $encoder, ValidatorInterface $validator) : Response
    {
        $message = [];
        $method = $request->getMethod();

        if ($method == 'POST') {
            $username = $request->get('username');
            $password = $request->get('password');

            $user = new User();
            $user->setUsername($username);
            $user->setPassword($password);

            $errors = $validator->validate($user);

            if (count($errors) > 0) {
                foreach ($errors as $violation) {
                    $message[] = $violation->getMessage();
                }
            } else {
                $user->setPassword($encoder->encodePassword($user, $password));
                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();
            }
        }

        return $this->render('security/auth_register.html.twig', ['errors' => $message]);
    }

    /**
     * @Route("/login", name="login", methods={"POST"})
     */
    public function login(Request $request, UserRepository $userRepository, UserPasswordEncoderInterface $encoder)
    {
        $user = $userRepository->findOneBy([
            'username'=>$request->get('username'),
        ]);
        if (!$user || !$encoder->isPasswordValid($user, $request->get('password'))) {
            return $this->json([
                'message' => 'Username or password is wrong.',
            ]);
        }
        $payload = [
            "user" => $user->getUsername(),
            "exp"  => (new \DateTime())->modify("+100 minutes")->getTimestamp(),
        ];

        $jwt = JWT::encode($payload, $this->getParameter('jwt_secret'), 'HS256');

        return $this->json([
            'message' => 'success!',
            'token' => sprintf('Bearer %s', $jwt),
        ]);
    }

    /**
     * @Route("/", name="auth_login", methods={"GET"})
     */
    public function authLogin()
    {
        return $this->render('security/auth_login.html.twig');
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logout(): Response
    {
        // controller can be blank: it will never be executed!
    }


}
