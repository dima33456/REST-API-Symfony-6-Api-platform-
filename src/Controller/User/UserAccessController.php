<?php
namespace App\Controller\User;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

use App\Entity\User\User;
 
#[AsController]
final class UserAccessController extends AbstractController {
    public function __invoke(User $user): User {
        $authedUser = $this->getUser();

        if (!$authedUser) {
            throw new AccessDeniedHttpException("Объект пользователя отсутствует, куда вы его дели");
        }

        if ($user->getId() != $authedUser->getId()) {
            throw new AccessDeniedHttpException("Вы не можете отредактировать данные другого пользователя");
        }

        return $user;
    }
}
