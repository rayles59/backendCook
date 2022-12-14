<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Image;
use App\Entity\Ingredient;
use App\Entity\Like;
use App\Entity\Recette;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $userPasswordHasher;
    public function __construct(UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->userPasswordHasher = $userPasswordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $admin = new User();
        $this->CreateUsers($manager, $user, $admin);

        for ($i = 0; $i < 20; $i++)
        {
            $recette = new Recette();
            $like = new Like();
            $ingredient = new Ingredient();
            $category = new Category();
            $image = new Image();


            $category->setName('VEGETARIEN');
            $like->setIsLike(false);
            $manager->persist($category);

            $image->setName('rsa.png');
            $manager->persist($image);

            $recette->setDescriptions('description here'.$i);
            $recette->setName('Name here'.$i);
            $recette->setCreationTime($i);
            $recette->setUsers($user);
            $recette->setImage($image);
            $recette->addCategory($category);
            $recette->setStep(['Etape 1 :' => 'test']);
            $like->setRecette($recette);
            $like->setUser($user);
            $recette->addLike($like);
            $ingredient->setName('tomate'.$i);
            $recette->addIngredient($ingredient);
            $ingredient->addRecette($recette);

            $manager->persist($like);
            $manager->persist($ingredient);
            $manager->persist($recette);
        }
        $manager->flush();
    }

    private function CreateUsers(ObjectManager $manager,User $user, User $admin) : void
    {
        $user->setEmail('boddaert.gauthier@gmail.com');
        $user->setName('Gauthier');
        $user->setLastname('Boddaert');
        $user->setPassword($this->userPasswordHasher->hashPassword($user, "coucou"));
        $user->setRoles(['ROLE_ADMIN']);

        $admin->setEmail('gboddaert@insitaction.com');
        $admin->setName('Gauthier');
        $admin->setLastname('Boddaert');
        $admin->setPassword($this->userPasswordHasher->hashPassword($admin, "coucou"));
        $admin->setRoles(['ROLE_ADMIN']);

        $manager->persist($user);
        $manager->persist($admin);

    }
}
