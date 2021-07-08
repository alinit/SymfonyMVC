<?php


namespace App\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Repository\ProductRepository;
use function PHPUnit\Framework\throwException;

class BasketController extends AbstractController
{
    /**
     * @Route("/basket")
     */
    public function basket(Request $request, SessionInterface $session): Response
    {
        $basket = $session->get('basket', []);
        if ($request->isMethod('POST')) {
            unset($basket[$request->request->get('id')]);
            $session->set('basket', $basket);
        }
        $total = array_sum(array_map(function($product) {
            return $product->getPrice();
        }, $basket));
        $total_products = count($basket);
        return $this->render('basket.html.twig', ['basket' => $basket, 'total'=> $total, 'total_products'=> $total_products]);
    }
}
?>