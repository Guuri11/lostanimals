<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use App\Repository\PostRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PostRepository::class)]
#[ApiResource(
    itemOperations: [
        'get',
        'put' => ["security" => "object.owner == user", "security_message" => "You are not able to modify this post",],
        'delete' => ["security" => "object.owner == user", "security_message" => "You are not able to delete this post",],
    ],
    collectionOperations: ['post', 'get'],
    normalizationContext: ['groups' => ['read']],
    denormalizationContext: ['groups' => ['write']],
)]
#[ApiFilter(DateFilter::class, properties: ['created_at'])]
#[ApiFilter(SearchFilter::class, properties: ['owner.username' => 'partial', 'type' => 'exact', 'state' => 'exact'])]
class Post
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(["read", "write"])]
    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Length([min: 0, max: 255])]
    private $description;

    #[ORM\Column(type: 'string', length: 10)]
    #[Groups(["read", "write"])]
    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Choice(['LOST', 'FOUND'])]
    private $type;

    #[ORM\Column(type: 'boolean')]
    #[Groups(["read", "write"])]
    #[Assert\NotNull]
    private $state;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(["read", "write"])]
    #[Assert\NotNull]
    #[Assert\NotBlank]
    private $location;

    #[ORM\ManyToOne(targetEntity: MediaObject::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[ApiProperty(iri: 'http://schema.org/image')]
    #[ApiSubresource]
    #[Groups(["read", "write"])]
    public ?MediaObject $image = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'posts')]
    #[ORM\JoinColumn(nullable: false)]
    #[ApiSubresource]
    #[Groups(["read", "write"])]
    #[Assert\NotNull]
    private $owner;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Groups(["read"])]
    private $created_at;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Groups(["read"])]
    private $updated_at;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getState(): ?bool
    {
        return $this->state;
    }

    public function setState(bool $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(string $location): self
    {
        $this->location = $location;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTimeImmutable $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }
}
