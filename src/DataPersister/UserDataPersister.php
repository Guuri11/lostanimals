<?php 

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\DataPersisterInterface;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class UserDataPersister implements DataPersisterInterface
{

    private $entityManager;
    private $passwordHasher;

    public function __construct( EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher )
    {
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
    }

    
    public function supports($data): bool
    {
        return $data instanceof User;
    }

    /**
     * @param User $data
     */
    public function persist($data)
    {

        if ($data->getPlainPassword()) {
             // hash the password (based on the security.yaml config for the $user class)
            $hashedPassword = $this->passwordHasher->hashPassword(
                $data,
                $data->getPlainPassword()
            );
            $data->setPassword($hashedPassword);
        }        

        $data->eraseCredentials();

        $this->entityManager->persist($data);
        $this->entityManager->flush();
    }

    /**
    * @param User $data
    */
    public function remove($data)
    {
        $this->entityManager->remove($data);
        $this->entityManager->flush();
    }
}