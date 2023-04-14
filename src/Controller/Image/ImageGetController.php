<?php
namespace App\Controller\Image;
 
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Image\Image;
 
#[AsController]
final class ImageGetController extends AbstractController {
    public function __invoke(Request $request, EntityManagerInterface $entityManager): iterable {
        $user = $this->getUser();

        if (!$user) {
            throw new AccessDeniedHttpException("Объект пользователя отсутствует, куда вы его дели");
        }

        // стандартная пагинация с кастомным контроллером не заработала, поэтому написал свою

        $page = (int)$request->query->get('page');
        $itemsPerPage = (int)$request->query->get('itemsPerPage');

        if (!$page) {
            throw new BadRequestHttpException('Серьёзно? Как можно вывести ответ, если ты не указал страницу 😩');
        }

        if (!$itemsPerPage) {
            throw new BadRequestHttpException('Серьёзно? Как можно вывести ответ, если ты не указал количество элементов на страницу 😩');
        }

        // стандартная сортировка тоже

        // фильтрация по id

        $id = $request->query->get('id');
        if ($id) {
            return  $entityManager->getRepository(Image::class)->findBy(['user' => $user, 'id' => $id], null, $itemsPerPage, ($page - 1) * $itemsPerPage);
        }

        // фильтрация по дате

        $createdAtBefore = $request->query->get('createdAtBefore');
        if ($createdAtBefore) {
            $imgs = $entityManager->getRepository(Image::class)->findBy(['user' => $user]);

            $filteredImgs = array_filter($imgs, function($img) use ($createdAtBefore) {
                return $img->getCreatedAt() <= new \DateTime($createdAtBefore);
            });

            return array_slice($filteredImgs, ($page - 1) * $itemsPerPage, $itemsPerPage);
        }

        $createdAtStrictlyBefore = $request->query->get('createdAtStrictlyBefore');
        if ($createdAtStrictlyBefore) {
            $imgs = $entityManager->getRepository(Image::class)->findBy(['user' => $user]);
            
            $filteredImgs = array_filter($imgs, function($img) use ($createdAtStrictlyBefore) {
                return $img->getCreatedAt() < new \DateTime($createdAtStrictlyBefore);
            });

            return array_slice($filteredImgs, ($page - 1) * $itemsPerPage, $itemsPerPage);
        }

        $createdAtAfter = $request->query->get('createdAtAfter');
        if ($createdAtAfter) {
            $imgs = $entityManager->getRepository(Image::class)->findBy(['user' => $user]);
            
            $filteredImgs = array_filter($imgs, function($img) use ($createdAtAfter) {
                return $img->getCreatedAt() >= new \DateTime($createdAtAfter);
            });

            return array_slice($filteredImgs, ($page - 1) * $itemsPerPage, $itemsPerPage);
        }

        $createdAtStrictlyAfter = $request->query->get('createdAtStrictlyAfter');
        if ($createdAtStrictlyAfter) {
            $imgs = $entityManager->getRepository(Image::class)->findBy(['user' => $user]);

            $filteredImgs = array_filter($imgs, function($img) use ($createdAtStrictlyAfter) {
                return $img->getCreatedAt() > new \DateTime($createdAtStrictlyAfter);
            });
            
            return array_slice($filteredImgs, ($page - 1) * $itemsPerPage, $itemsPerPage);
        }

        // сортировка по id

        $orderById = $request->query->get('orderById');
        if ($orderById) {
            return  $entityManager->getRepository(Image::class)->findBy(['user' => $user], ['id' => $orderById], $itemsPerPage, ($page - 1) * $itemsPerPage);
        }

        // сортировка по дате

        $orderByCreatedAt = $request->query->get('orderByCreatedAt');
        if ($orderByCreatedAt) {
            return  $entityManager->getRepository(Image::class)->findBy(['user' => $user], ['createdAt' => $orderByCreatedAt], $itemsPerPage, ($page - 1) * $itemsPerPage);
        }

        return  $entityManager->getRepository(Image::class)->findBy(['user' => $user], null, $itemsPerPage, ($page - 1) * $itemsPerPage);
    }
}
