<?php

namespace App\Controller\Admin;

use App\Consts\Role;
use App\Entity\Brand;
use App\Entity\Category;
use App\Entity\Comments;
use App\Entity\Good;
use App\Entity\GoodPropKey;
use App\Entity\Orders;
use App\Entity\Product;
use App\Entity\ProductPropKey;
use App\Entity\PropKey;
use App\Entity\Shop;
use App\Entity\UserInfo;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

#[IsGranted(Role::ADMIN)]
class DashboardController extends AbstractDashboardController
{

    private string $frontend;
    private JWTTokenManagerInterface $tokenManager;

    public function __construct(string $frontend, JWTTokenManagerInterface $tokenManager)
    {
        $this->frontend = $frontend;
        $this->tokenManager = $tokenManager;
    }

    #[Route('/admin', name: 'admin_dashboard')]
    public function index(): Response
    {
        $user = $this->getUser();
        return $this->render(
            view: "admin/custom-dashboard.html.twig",
            parameters: [
                'my_own_data'=>[],
                'front_path'=>$this->frontend,
                'token'=>$this->tokenManager->createFromPayload($user, ['exp'=> time() + 300])
            ]
        );
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Php Mall Admin');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linktoDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::section("商品服务");
        yield MenuItem::linkToCrud('商品管理', 'fab fa-product-hunt', Product::class);
        yield MenuItem::linkToCrud('货品管理', 'fab fa-goodreads', Good::class);
        yield MenuItem::linkToCrud('商品属性管理', 'fa fa-tags', PropKey::class);
        yield MenuItem::linkToCrud('商品标签删除', 'fa fa-tags', ProductPropKey::class);
        yield MenuItem::linkToCrud('货品标签删除', 'fa fa-tags', GoodPropKey::class);
        yield MenuItem::section("其他服务");
        yield MenuItem::linkToCrud('订单管理', 'fas fa-money-check-alt', Orders::class);
        yield MenuItem::linkToCrud('分类管理', 'fa fa-tags', Category::class);
        yield MenuItem::linkToCrud('品牌管理', 'fab fa-bimobject', Brand::class);
        yield MenuItem::linkToCrud('评论管理', 'fa fa-comment', Comments::class);
        yield MenuItem::section("用户服务");
        yield MenuItem::linkToCrud('用户管理', 'fa fa-user', UserInfo::class);
        yield MenuItem::linkToCrud('商店管理', 'fas fa-shopping-bag', Shop::class);

    }
}
