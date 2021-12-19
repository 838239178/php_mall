<?php


namespace App\Tests\Comments;


use App\Entity\Comments;
use App\Entity\UserInfo;
use App\Repository\CommentsRepository;
use Monolog\Test\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CommentsTests extends KernelTestCase
{
    private $entityManager;
    private UserPasswordHasherInterface $passwordHasher;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->entityManager = $kernel->getContainer()
        ->get('doctrine')
        ->getManager();
    }

    public function testGetById()
    {
        /** @var Comments $res */
        $res = $this->entityManager
            ->getRepository(Comments::class)
            ->find(1);
        print($res->getCommentsId());
        print("\n");
        print($res->getContent());
        self::assertSame(2, $res->getReplies()->count(), "该评论的回复数量不为2");
        $reply1 = $res->getReplies()->get(0);
        print("\nreply 1 ".$reply1->getContent());
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        // doing this is recommended to avoid memory leaks
        $this->entityManager->close();
        $this->entityManager = null;
    }
}