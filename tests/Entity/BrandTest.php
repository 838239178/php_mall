<?php

namespace App\Tests\Entity;

use App\Entity\Brand;
use App\Entity\Category;
use ContainerFzAxMcs\getCategoryRepositoryService;
use Doctrine\Persistence\ObjectManager;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class BrandTest extends KernelTestCase
{
    private ObjectManager $entityManager;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }


    public function testGetById()
    {
        /** @var Brand[] $res */
        $res = $this->entityManager
            ->getRepository(Brand::class)
            ->findAll();
        self::assertNotNull($res, "not found brand");
        self::assertGreaterThan(0, sizeof($res), "brands empty");
        $cate = $res[0]->getCategory();
        self::assertNotNull($cate, "not found category");
        print($cate->getCategoryId());
        self::assertTrue($this->entityManager->contains($cate), "not contains category");
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        // doing this is recommended to avoid memory leaks
        $this->entityManager->close();
    }
}
