<?php

declare(strict_types=1);

namespace App\Domain\Blog\Entity;

use App\Core\Entity\Behavior\IdentifiableInterface;
use App\Core\Entity\Behavior\IdentifiableTrait;
use App\Core\Entity\Behavior\SluggableInterface;
use App\Core\Entity\Behavior\SluggableTrait;
use App\Core\Entity\Behavior\TimestampableInterface;
use App\Core\Entity\Behavior\TimestampableTrait;
use App\Domain\Blog\Repository\TagRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: 'tag')]
#[ORM\Entity(repositoryClass: TagRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[ORM\UniqueConstraint(columns: ['name'])]
#[UniqueEntity(fields: 'name')]
class Tag implements IdentifiableInterface, SluggableInterface, TimestampableInterface, \Stringable
{
    use IdentifiableTrait;
    use SluggableTrait;
    use TimestampableTrait;

    #[ORM\Column(type: Types::STRING, length: 30)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 30)]
    private ?string $name = null;

    /**
     * @var Collection<int, Post>
     */
    #[ORM\ManyToMany(targetEntity: Post::class, mappedBy: 'tags')]
    #[ORM\OrderBy(['publishedAt' => 'DESC'])]
    private Collection $posts;

    public function __construct()
    {
        $this->posts = new ArrayCollection();
    }

    #[\Override]
    public function __toString(): string
    {
        return $this->name ?? '';
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, Post>
     */
    public function getPosts(): Collection
    {
        return $this->posts;
    }

    #[\Override]
    public function getSluggableFields(): array
    {
        return ['name'];
    }
}
