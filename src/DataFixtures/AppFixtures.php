<?php

namespace App\DataFixtures;

use App\Entity\Event;
use App\Entity\User;
use App\Entity\UserEvent;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Faker\ORM\Doctrine\Populator;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('en_EN');

        $populator = new Populator($faker, $manager);

        $admin = (new User())
            ->setFirstname('Etienne')
            ->setLastname('Lebarillier')
            ->setEmail('etienne@lebarillier.fr')
            ->setIsVerified(true)
            ->setRoles(['ROLE_ADMIN'])
        ;
        $admin->setPassword($this->encoder->encodePassword($admin, '00000000'));

        $manager->persist($admin);

        $event = (new Event())
            ->setTitle('Event Fixtures')
            ->setDescription('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean laoreet, nisi et bibendum viverra, leo ex iaculis risus, in eleifend massa purus nec neque. Duis vestibulum, justo eu varius sollicitudin, dolor nunc tristique tellus, eget tincidunt lacus mi ac tellus. Aliquam erat volutpat. Nullam aliquet commodo consequat. Cras dignissim, tellus vel condimentum facilisis, elit justo hendrerit erat, id vulputate mauris mi et sapien. Nam ultrices odio non libero tempor ornare. Aliquam erat volutpat. Donec bibendum lorem non orci varius, a malesuada dolor vestibulum. Proin in neque imperdiet, pulvinar erat in, mollis turpis. ')
            ->setStartDate(new DateTime())
            ->setNbDaysFirstStep(7)
            ->setNbDaysStep(7)
            ->setNbDaysLastStep(15)
            ->setNbMaxUser(1000000)
            ->setNbMinUsersPerGroup(3)
            ->setNbMinUsersFinalGroup(5)
            ->setCreatedAt(new DateTime())
        ;

        $manager->persist($event);

        $populator->addEntity(User::class, 500, [
            'isVerified' => true,
        ], [
            function (User $user) use ($faker, $event) {
                $user->setPassword($this->encoder->encodePassword($user, $faker->password(8, 20)));
                $userEvent = (new UserEvent())
                    ->setUser($user)
                    ->setEvent($event)
                    ->setNbSources(0)
                ;
                $event->addUserEvent($userEvent);
            },
        ]);

        $populator->execute();
    }
}
