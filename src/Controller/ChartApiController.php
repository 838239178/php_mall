<?php


namespace App\Controller;

use App\Consts\Role;
use App\Entity\ProductMonthChart;
use App\Entity\ShopMonthChart;
use App\Util\HttpUtils;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\ResultSetMapping;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: "/api/charts", name: "api_charts")]
#[IsGranted(Role::ADMIN)]
class ChartApiController extends AbstractController
{

    private EntityManagerInterface $em;
    private HttpUtils $httpUtils;

    public function __construct(EntityManagerInterface $em, HttpUtils $httpUtils)
    {
        $this->em = $em;
        $this->httpUtils = $httpUtils;
    }

    #[Route(path: "/products/{pid}", name: "_products", methods: ["GET"])]
    public function productMonthChart(string $pid, Request $request): Response
    {
        $year = $request->get("year") ?: date_create()->format("Y");
        $rsm = (new ResultSetMapping())
                ->addEntityResult(ProductMonthChart::class, "pmc")
                ->addFieldResult("pmc","month","month")
                ->addFieldResult("pmc","sale","sale")
                ->addFieldResult("pmc","money","money");
        $nativeQuery = $this->em->createNativeQuery("
            SELECT MONTH(o.finish_time) as month, SUM(od.product_price) as money, SUM(od.product_size) as sale from orders_detail od
            JOIN good g on od.good_id = g.good_id
            JOIN orders o on o.orders_id = od.orders_id
            WHERE 
                YEAR(o.finish_time) = :year 
            AND 
                product_id = :pid
            GROUP BY MONTH(o.finish_time)
            ORDER BY MONTH(finish_time)
        ", $rsm)->setParameters([
            "year"=>$year,
            "pid"=>$pid
        ])->enableResultCache(lifetime: 60*60*24);
        return $this->httpUtils->wrapperSuccess($nativeQuery->getArrayResult());
    }

    #[Route(path: "/shop/{shopId}", name: "_shop", methods: ["GET"])]
    public function shopMMonthChart(string $shopId, Request $request): Response
    {
        $year = $request->get("year") ?: date_create()->format("Y");
        $rsm = (new ResultSetMapping())
            ->addEntityResult(ShopMonthChart::class, "smc")
            ->addFieldResult("smc","month","month")
            ->addFieldResult("smc","money","money");
        $nativeQuery = $this->em->createNativeQuery("
            SELECT MONTH(finish_time) as `month`, SUM(total_price) as money FROM orders
            WHERE 
                shop_id = :sid
            AND
                YEAR(finish_time) = :year
            GROUP BY MONTH(finish_time)
            ORDER BY MONTH(finish_time)
        ", $rsm)->setParameters([
            "year"=>$year,
            "sid"=>$shopId
        ])->enableResultCache(lifetime: 60*60*24);
        return $this->httpUtils->wrapperSuccess($nativeQuery->getArrayResult());
    }
}