<?php

namespace App\Entity\BaseEntity;

use Symfony\Component\Serializer\Annotation\Groups;

use Doctrine\ORM\Mapping as ORM;

#[ORM\MappedSuperclass]
#[ORM\HasLifecycleCallbacks]
abstract class BaseEntity {
    #[ORM\Id, ORM\Column(type: "integer"), ORM\GeneratedValue(strategy: "AUTO"), Groups(['read'])]
    protected int $id;

    /** The date when the entity was created */
    #[ORM\Column(type: "datetime"), Groups(['read'])]
    protected ?\DateTimeInterface $createdAt;

    /** The date when the entity was last updated */
    #[ORM\Column(type: "datetime"), Groups(['read'])]
    protected ?\DateTimeInterface $updatedAt;

    // геттеры

    /** @return string */
    public function getId(): string {
        return $this->id;
    }

    /** @return ?\DateTimeInterface */
    public function getCreatedAt(): ?\DateTimeInterface {
        return $this->createdAt;
    }

    /** @return ?\DateTimeInterface */
    public function getUpdatedAt(): ?\DateTimeInterface {
        return $this->updatedAt;
    }

    // обработчики

    #[ORM\PrePersist]
    public function dateCreate(): void {
        $this->createdAt = new \DateTime();
        $this->updatedAt = $this->createdAt;
    }

    #[ORM\PreUpdate]
    public function dateUpdate(): void {
        $this->updatedAt = new \DateTime();
    }
}
