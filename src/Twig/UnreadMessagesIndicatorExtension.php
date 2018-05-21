<?php


namespace App\Twig;

use App\Services\MessageManager;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class UnreadMessagesIndicatorExtension extends AbstractExtension
{
    private $tokenStorage;
    private $messageManager;

    public function __construct(TokenStorageInterface $tokenStorage,
                                MessageManager $messageManager)
    {
        $this->tokenStorage = $tokenStorage;
        $this->messageManager = $messageManager;
    }

    public function getFunctions()
    {
        return array(
            new TwigFunction('unreadMessagesCount', array($this, 'unreadMessagesCount')),
        );
    }

    public function unreadMessagesCount()
    {
        $user = $this->tokenStorage->getToken()->getUser();
        return $this->messageManager->getUsersUnreadMessagesCount($user);
    }
}