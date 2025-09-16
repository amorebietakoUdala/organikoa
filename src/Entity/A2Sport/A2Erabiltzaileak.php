<?php

namespace App\Entity\A2Sport;

use App\Repository\A2Sport\A2ErabiltzaileakRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: A2ErabiltzaileakRepository::class, readOnly: true)]
#[ORM\Table(name: "PLUSERS")]
class A2Erabiltzaileak
{
    #[ORM\Id]
    #[ORM\Column(type: "integer", name: "CODU01")]
    private int $id;

    #[ORM\Column(type: "string", length: 30, name: "NOMU01")]
    private string $nombre;

    #[ORM\Column(type: "string", length: 30, nullable: true, name: "AP1U01")]
    private ?string $apellido1 = null;

    #[ORM\Column(type: "string", length: 30, nullable: true, name: "AP2U01")]
    private ?string $apellido2 = null;

    #[ORM\Column(type: "string", length: 14, nullable: true, name: "DNIU01")]
    private ?string $dni = null;

    #[ORM\Column(type: "string", length: 1, nullable: true, name: "LETU01")]
    private ?string $letra = null;

    #[ORM\Column(type: "string", length: 20, nullable: true, name: "CODIGO")]
    private ?string $tarjeta = null;

    public function getId(): int
    {
        return $this->id;
    }

    public function getNombre(): string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): self
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getApellido1(): ?string
    {
        return $this->apellido1;
    }

    public function setApellido1(?string $apellido1): self
    {
        $this->apellido1 = $apellido1;

        return $this;
    }

    public function getApellido2(): ?string
    {
        return $this->apellido2;
    }

    public function setApellido2(?string $apellido2): self
    {
        $this->apellido2 = $apellido2;

        return $this;
    }

    public function getDni(): ?string
    {
        return $this->dni;
    }

    public function setDni(?string $dni): self
    {
        $this->dni = $dni;

        return $this;
    }

    public function getTarjeta(): ?string
    {
        return $this->tarjeta;
    }

    public function setTarjeta(?string $tarjeta): self
    {
        $this->tarjeta = $tarjeta;

        return $this;
    }

    public function getTarjetaHex(): ?string
    {
        $hexadecimal = $this->bigzToHexReversed($this->tarjeta);
        return mb_strtoupper($hexadecimal);
    }

    public static function bigzToHexReversed(string|int $number): string
    {
        $number = ltrim((string)$number, '0'); // Quitar ceros iniciales
        if (!ctype_digit($number)) {
            throw new \InvalidArgumentException("El valor debe ser un número positivo.");
        }
        $hex = dechex((int)$number);
        if (strlen($hex) % 2 !== 0) {
            $hex = '0' . $hex;
        }
        $octets = str_split($hex, 2);
        $reversedHex = implode('', array_reverse($octets));
        return $reversedHex ?: '00';
    }    

    public function getLetra(): string
    {
        return $this->letra;
    }

    public function setLetra(string $letra): self
    {
        $this->letra = $letra;

        return $this;
    }

}