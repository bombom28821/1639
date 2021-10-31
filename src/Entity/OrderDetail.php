<?php

namespace App\Entity;

use App\Repository\OrderDetailRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=OrderDetailRepository::class)
 */
class OrderDetail
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $quantity;

    /**
     * @ORM\ManyToOne(targetEntity=Order::class)
     */
    private $Order;

    /**
     * @ORM\ManyToOne(targetEntity=Book::class)
     */
    private $Book;
    public function __construct()
    {
        $this->Book = new ArrayCollection();
    }
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getOrder(): ?Order
    {
        return $this->Order;
    }

    public function setOrder(?Order $Order): self
    {
        $this->Order = $Order;

        return $this;
    }

    public function getBook()
    {
        return $this->Book;
    }

    public function setBook(Book $Book): self
    {
        $this->Book = $Book;

        return $this;
    }
}