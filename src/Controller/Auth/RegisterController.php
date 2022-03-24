<?php

declare(strict_types=1);

namespace App\Controller\Auth;

use App\Model\User\UseCase;
use App\Model\User\UseCase\SignUp;
use App\ReadModel\User\UserFetcher;
use App\Security\LoginFormAuthenticator;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class RegisterController extends AbstractController
{
    /**
     * @param LoggerInterface $logger
     * @param UserFetcher $users
     */
    public function __construct(
        private LoggerInterface $logger,
        private UserFetcher $users,
    ) {}

    /**
     * @param Request $request
     * @param SignUp\Request\Handler $handler
     * @return Response
     */
    #[Route('/signup', name: 'auth.signup')]
    public function request(Request $request, SignUp\Request\Handler $handler): Response
    {
        $command = new SignUp\Request\Command();

        $form = $this->createForm(SignUp\Request\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'Check your email.');
                return $this->redirectToRoute('home');
            } catch (\DomainException $e) {
                $this->logger->warning($e->getMessage(), ['exception' => $e]);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('app/auth/signup.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @param string $token
     * @param Request $request
     * @param \App\Model\User\UseCase\SignUp\Confirm\Token\Handler $handler
     * @param UserAuthenticatorInterface $authenticator
     * @param UserProviderInterface $userProvider
     * @param LoginFormAuthenticator $formAuthenticator
     * @return Response
     * @throws \Doctrine\DBAL\Exception
     */
    #[Route('/signup/{token}', name: 'auth.signup.confirm')]
    public function confirm(
        string $token,
        Request $request,
        SignUp\Confirm\Token\Handler $handler,
        UserAuthenticatorInterface $authenticator,
        UserProviderInterface $userProvider,
        LoginFormAuthenticator $formAuthenticator,
    ): Response
    {
        if (!$user = $this->users->findByRegisterConfirmToken($token)) {
            $this->addFlash('error', 'Incorrect or already confirmed token.');
            return $this->redirectToRoute('auth.signup');
        }

        $command = new SignUp\Confirm\Token\Command($token);

        try {
            $handler->handle($command);
            return $authenticator->authenticateUser(
                $userProvider->loadUserByIdentifier($user['email']),
                $formAuthenticator,
                $request
            );
        } catch (\DomainException $e) {
            $this->logger->warning($e->getMessage(), ['exception' => $e]);
            $this->addFlash('error', $e->getMessage());
            return $this->redirectToRoute('auth.signup');
        }
    }
}