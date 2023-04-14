<?php

namespace App\Entity\Image;

use ApiPlatform\Metadata\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Put;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\BaseEntity\BaseEntity;
use App\Entity\User\User;
use App\Controller\Image\ImageUploadController;
use App\Controller\Image\ImageGetController;
use App\Controller\Image\ImageDeleteController;

#[ORM\Entity]
#[ApiResource(operations: [
    new GetCollection(
        controller: ImageGetController::class,
        security: 'is_granted("ROLE_USER")',
        normalizationContext: ['groups' => 'read'],
        openapiContext: [
            'parameters' => [
                [
                    'name' => 'page',
                    'in' => 'query',
                    'description' => 'The collection page number (INT, the fractional part will be discarded!)',
                    'type' => 'number',
                    'example' => 1
                ],
                [
                    'name' => 'itemsPerPage',
                    'in' => 'query',
                    'description' => 'The number of items per page (INT, the fractional part will be discarded!)',
                    'type' => 'number',
                    'example' => 30
                ],
                [
                    'name' => 'id',
                    'in' => 'query',
                    'description' => 'Filter by id',
                    'type' => 'number'
                ],
                [
                    'name' => 'createdAtBefore',
                    'in' => 'query',
                    'type' => 'string'
                ],
                [
                    'name' => 'createdAtStrictlyBefore',
                    'in' => 'query',
                    'type' => 'string'
                ],
                [
                    'name' => 'createdAtAfter',
                    'in' => 'query',
                    'type' => 'string'
                ],
                [
                    'name' => 'createdAtStrictlyAfter',
                    'in' => 'query',
                    'type' => 'string'
                ],
                [
                    'name' => 'orderById',
                    'in' => 'query',
                    'description' => 'Available values : asc, desc',
                    'schema' => [
                        'type' => 'string',
                        'enum' => ['asc', 'desc']
                    ]
                ],
                [
                    'name' => 'orderByCreatedAt',
                    'in' => 'query',
                    'description' => 'Available values : asc, desc',
                    'schema' => [
                        'type' => 'string',
                        'enum' => ['asc', 'desc']
                    ]
                ]
            ]
        ]
    ),
    new Delete(
        controller: ImageDeleteController::class,
        security: 'is_granted("ROLE_USER")'
    ),
    new Put(
        security: 'is_granted("ROLE_USER")',
        normalizationContext: ['groups' => 'read'],
        denormalizationContext: ['groups' => 'write']
    ),
    new Post(
        controller: ImageUploadController::class,
        deserialize: false,
        security: 'is_granted("ROLE_USER")',
        normalizationContext: ['groups' => 'read'],
        openapiContext: [ // нет denormalizationContext, потому что поля для загрузки картинки итак кастомные
            'requestBody' => [
                'description' => 'Upload image',
                'required' => true,
                'content' => [
                    'multipart/form-data' => [
                        'schema' => [
                            'type' => 'object',
                            'properties' => [
                                'file' => [
                                    'type' => 'string',
                                    'format' => 'binary',
                                    'description' => 'Upload the required image file'
                                ],
                                'description' => [
                                    'type' => 'string',
                                    'format' => 'string',
                                    'description' => 'Some description'
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ]
    )
])]
class Image extends BaseEntity {
    /** Path to image file */
    #[ORM\Column(type: "string", length: 255)]
    private string $path;

    /** Url to image file */
    #[ORM\Column(type: "string", length: 255), Groups(['read'])]
    private string $url;

    /** Some description */
    #[ORM\Column(type: "string", length: 255), Groups(['read', 'write'])]
    private string $description;

    /** The owner of the image */
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'images')]
    #[ORM\JoinColumn(nullable: false)]
    private User $user;

    // геттеры

    /** @return string */
    public function getPath(): string {
        return $this->path;
    }

    /** @return string */
    public function getUrl(): string {
        return $this->url;
    }

    /** @return string */
    public function getDescription(): string {
        return $this->description;
    }

    /** @return User */
    public function getUser(): User {
        return $this->user;
    }

    // сеттеры

    /** @param string */
    public function setPath(string $path): void {
        $this->path = $path;
    }

    /** @param string */
    public function setUrl(string $url): void {
        $this->url = $url;
    }

    /** @param string */
    public function setDescription(string $description): void {
        $this->description = $description;
    }

    /** @param User */
    public function setUser(User $user): void {
        $this->user = $user;
    }
}
