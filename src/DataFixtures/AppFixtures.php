<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

// load my entities
use App\Entity\User;
use App\Entity\Article;
use App\Entity\Section;

// load other dependencies
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Faker\Factory AS Faker;
use Cocur\Slugify\Slugify;
use DateTime;

class AppFixtures extends Fixture
{
    private $faker;


    public function __construct(UserPasswordHasherInterface $userPasswordHasher){
        $this->hasher = $userPasswordHasher;
        $this->faker = Faker::create('en_IE');
    }

    private function createTitle($min = 1, $max = 5)
    {
        $nbWords = mt_rand($min,$max);
        $title = $this->faker->words($nbWords);
        return implode(" ", $title);

    }

    private function createText($min = 5, $max = 10)
    {
        $nbPara = mt_rand($min, $max);
        $texts = $this->faker->paragraphs($nbPara);
        return implode("\n", $texts);
    }

    private function createPubDate($maxDate): DateTime
    {
        return $this->faker->dateTimeBetween($maxDate,(clone $maxDate)->modify('+3 months'));
    }

    public function load(ObjectManager $manager): void
    {
        $slugify = new Slugify();
        // declaring these here rather than inside the loops as they will be needed for the relations
        $articles = [];
        $sections = [];
        $admins = [];

        // Start by creating the different user types
        // create me...
        $super = new User();
        $super->setUsername('leerlandais');
        $super->setRoles(['ROLE_SUPER', 'ROLE_ADMIN', 'ROLE_REDAC', 'ROLE_USER']);
        $super->setEmail('lee@leerlandais.com');
        $super->setFullName("Lee Brennan");
        $super->setActivate(true);
        $super->setUniqid(uniqid('user_', true));
        $pwdHash = $this->hasher->hashPassword($super, '270675');
        $super->setPassword($pwdHash);
        $this->admins[1] = $super;
        // ... and save me
        $manager->persist($super);

        // create the admin
        $admin = new User();

        $admin->setUsername('admin');
        $admin->setRoles(['ROLE_ADMIN', 'ROLE_REDAC', 'ROLE_USER']);
        $admin->setEmail('admin@admin.com');
        $admin->setFullName("Admin");
        $admin->setActivate(true);
        $admin->setUniqid(uniqid('user_', true));
        $pwdHash = $this->hasher->hashPassword($admin, 'admin');
        $admin->setPassword($pwdHash);

        $this->admins[2] = $admin;

        $manager->persist($admin);

        for($i = 1; $i < 6; $i++){
            // create the 5 Editors
            $redac = new User();
            $redac->setUsername('redac'.$i);
            $redac->setRoles(['ROLE_REDAC', 'ROLE_USER']);
            $redac->setEmail('redac'.$i.'@redac.com');
            $redac->setFullName('Redac '.$i);
            $redac->setActivate(true);
            $redac->setUniqid(uniqid('user_', true));
            $pwdHash = $this->hasher->hashPassword($redac, 'redac'.$i);
            $redac->setPassword($pwdHash);

            $this->admins[($i + 2)] = $redac;
            $manager->persist($redac);
        }


        for($i = 1; $i < 25; $i++){
            // create the users
            $user = new User();
            $user->setUsername('user'.$i);
            $user->setRoles(['ROLE_USER']);
            $user->setEmail($this->faker->email);
            $user->setFullName($this->faker->name());
            $user->setActivate(mt_rand(0,3)); // tidier way to get 1 false in 4, no need for an extra variable
            $user->setUniqid(uniqid('user_', true));
            $pwdHash = $this->hasher->hashPassword($user, 'user'.$i);
            $user->setPassword($pwdHash);

            $users[] = $user;
            $manager->persist($user);
        }
        // Then create all the articles
        for ($i = 1; $i < 161; $i++) {
            $article = new Article();
            $randomUserId = array_rand($this->admins);
            $article->setUser($this->admins[$randomUserId]);
            $title = $this->createTitle();
            $article->setTitle($title);
            $article->setTitleSlug($slugify->slugify($title));
            $text = $this->createText();
            $article->setText($text);
            $dateCreate = $this->faker->dateTimeThisDecade();
            $article->setArticleDateCreated($dateCreate);
            $isPub = mt_rand(0, 4);
            $article->setPublished($isPub);
            if ($isPub) {
                $datePub = $this->createPubDate($dateCreate);
                $article->setArticleDatePosted($datePub);
            }
            $articles[] = $article;
            $manager->persist($article);
        }

        // Now create the 6 sections
        for ($i = 1; $i < 7; $i++){
            $section = new Section();
            $title = $this->createTitle(1,3);
            $section->setSectionTitle($title);
            $section->setSectionSlug($slugify->slugify($title));
            $detail = $this->createText(1,1);
            $section->setSectionDetail($detail);
            // and link them to the articles randomly
            $nbArt = mt_rand(20, 40);
            shuffle($articles);
            // There are many methods to do this but I like this cos it makes sure the random articles are unique
            // (meaning the if(!$this) in addArticle may be redundant)
            $randArts = array_slice($articles, 0, $nbArt);

            foreach ($randArts as $art) {
                $section->addArticle($art);
            }

            $manager->persist($section);
        }

        $manager->flush();
    }

}

