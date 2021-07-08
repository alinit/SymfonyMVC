<?php


namespace App\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Repository\ProductRepository;
use function PHPUnit\Framework\throwException;

class ProductsController extends AbstractController
{
    /**
     * @Route("/", name="first_homepage")
     */
    public function homepage(ProductRepository $repo): Response
    {
        $bikes = $repo->findAll();

        return $this->render('homepage.html.twig', ['bikes' => $bikes]);
    }

    /**
     * @Route("/first/{id}")
     */
    public function details($id, ProductRepository $repo, Request $request, SessionInterface $session): Response
    {
        $bike = $repo->find($id);

        if ($bike === null) {
            return $this->render('bundles/TwigBundle/Exception/error404.html.twig');
        }

        $basket = $session->get('basket', []);
        if ($request->isMethod('POST')) {
            $basket[$bike->getId()] = $bike;
            $session->set('basket', $basket);
            $inBasket = array_key_exists($bike->getId(), $basket);
        } else {
            $inBasket = false;
        }
        return $this->render('details.html.twig', ['bike' => $bike, 'inBasket' => $inBasket]);
    }
}