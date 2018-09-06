<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User implements UserInterface, \Serializable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $uid;

    /**
     * @ORM\Column(type="string", length=50, unique=true)
     * @Assert\NotBlank()
     * @Assert\Length(max=50)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=120, nullable=true)
     * @Assert\Length(max=120)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=64)
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=80, unique=true, nullable=true)
     * @Assert\NotBlank()
     * @Assert\Length(max=80)
     * @Assert\Email()
     */
    private $email;

    /**
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;

    /**
     * @var array
     * @ORM\Column(type="array")
     */
    private $roles;

    /**
     * @Assert\NotBlank(groups={"registration"})
     * @Assert\Length(max=4096)
     */
    private $plainPassword;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @var \DateTime
     */
    private $passwordRequestedAt;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $picture;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $token;

    public function __construct()
    {
        $this->isActive = true;
        $this->roles = ['ROLE_USER'];
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUid(): ?string
    {
        return $this->uid;
    }

    public function setUid(string $uid): self
    {
        $this->uid = $uid;

        return $this;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function setUsername($username)
    {
        $this->username = $username;
        return $this;
    }


    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    /*
     * Get email
     */
    public function getEmail()
    {
        return $this->email;
    }

    /*
     * Set email
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /*
     * Get isActive
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /*
     * Set isActive
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
        return $this;
    }

    public function getSalt()
    {
        // pas besoin de salt puisque nous allons utiliser bcrypt
        // attention si vous utilisez une méthode d'encodage différente !
        // il faudra décommenter les lignes concernant le salt, créer la propriété correspondante, et renvoyer sa valeur dans cette méthode
        return null;
    }

    // modifier la méthode getRoles
    public function getRoles()
    {
        return $this->roles;
    }

    public function setRoles(array $roles)
    {
        if (!in_array('ROLE_USER', $roles))
        {
            $roles[] = 'ROLE_USER';
        }
        foreach ($roles as $role)
        {
            if(substr($role, 0, 5) !== 'ROLE_') {
                throw new InvalidArgumentException("Chaque rôle doit commencer par 'ROLE_'");
            }
        }
        $this->roles = $roles;
        return $this;
    }

    public function eraseCredentials()
    {
    }

    /** @see \Serializable::serialize() */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->username,
            $this->password,
            $this->isActive,
            // voir remarques sur salt plus haut
            // $this->salt,
        ));
    }

    /** @see \Serializable::unserialize() */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->username,
            $this->password,
            $this->isActive,
            // voir remarques sur salt plus haut
            // $this->salt
            ) = unserialize($serialized);
    }

    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    public function setPlainPassword($password)
    {
        $this->plainPassword = $password;
    }

    /*
    * Get passwordRequestedAt
    */
    public function getPasswordRequestedAt()
    {
        return $this->passwordRequestedAt;
    }

    /*
     * Set passwordRequestedAt
     */
    public function setPasswordRequestedAt($passwordRequestedAt)
    {
        $this->passwordRequestedAt = $passwordRequestedAt;
        return $this;
    }

    /*
     * Get token
     */
    public function getToken()
    {
        return $this->token;
    }

    /*
     * Set token
     */
    public function setToken($token)
    {
        $this->token = $token;
        return $this;
    }

    public function setPicture($picture)
    {
        $this->picture = $picture;
    }

    public function getPicture()
    {
        return $this->picture;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }
}
