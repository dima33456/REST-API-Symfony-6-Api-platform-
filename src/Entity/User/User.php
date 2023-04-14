<?php

namespace App\Entity\User;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Post;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;

use App\Entity\BaseEntity\BaseEntity;
use App\Entity\Image\Image;
use App\Controller\User\RegistrationController;
use App\Controller\User\UserAccessController;
use App\Repository\UserRepository;

#[ApiResource(operations: [
    new GetCollection(
        normalizationContext: ['groups' => 'read']
    ),
    new Put(
        security: 'is_granted("ROLE_USER")',
        controller: UserAccessController::class,
        denormalizationContext: ['groups' => 'write'],
        normalizationContext: ['groups' => 'read']
    ),
    new Delete(
        security: 'is_granted("ROLE_USER")',
        controller: UserAccessController::class
    ),
    new Post(
        uriTemplate: 'user/register',
        controller: RegistrationController::class,
        denormalizationContext: ['groups' => 'createUser'],
        normalizationContext: ['groups' => 'read']
    )
])]
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ApiFilter(SearchFilter::class, properties: ['id' => 'exact'])]
#[ApiFilter(DateFilter::class, properties: ['createdAt'])]
#[ApiFilter(
    OrderFilter::class,
    properties: ['id', 'createdAt'],
    arguments: ['orderParameterName' => 'order']
)]
class User extends BaseEntity implements UserInterface, PasswordAuthenticatedUserInterface {
    public function __construct() {
        $this->images = new ArrayCollection();
    }

    #[ORM\Column(type: "string", length: 255)]
    #[Groups(['createUser', 'read', 'write'])]
    private string $username;

    #[ORM\Column(type: "string", length: 180)]
    #[Groups(['createUser', 'read', 'write'])]
    private string $email;

    #[ORM\Column(type: "string", length: 255)]
    #[Groups(['createUser'])]
    private string $password;

    #[ORM\Column(type: "json")]
    private $roles = [];

    #[ORM\OneToMany(targetEntity: Image::class, mappedBy: 'user', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['read'])]
    private iterable $images;

    // геттеры

    /** @return string */
    public function getUsername(): string {
        return $this->username;
    }

    /** @return string */
    public function getEmail(): string {
        return $this->email;
    }

    /** @return string */
    public function getPassword(): string {
        return $this->password;
    }

    /** @return Image[] */
    public function getImages(): iterable {
        return $this->images;
    }

    /** @return string */
    public function getUserIdentifier(): string {
        return $this->username;
    }

    /** @return array */
    public function getRoles(): array {
        return $this->roles;
    }

    // сеттеры

    /** @param string */
    public function setUsername(string $username): void {
        $this->username = $username;
    }

    /** @param string */
    public function setEmail(string $email): void {
        $this->email = $email;
    }

    /** @param string */
    public function setPassword(string $password): void {
        $this->password = $password;
    }

    /** @param array */
    public function setRoles(array $roles): self {
        $this->roles = $roles;
        return $this;
    }

    public function eraseCredentials() {
        
    }
}
