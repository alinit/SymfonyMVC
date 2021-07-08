<?php


namespace App\Controller;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Repository\ProductRepository;
use function PHPUnit\Framework\throwException;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use App\Entity\Order;

class CheckoutController extends AbstractController
{
    /**
     * @Route("/checkout")
     */
    public function details(ProductRepository $repo, Request $request, SessionInterface $session): Response
    {
        $basket = $session->get('basket', []);
        $total = array_sum(array_map(function($product) {
            return $product->getPrice();
        }, $basket));

        $order  = new Order();
        $form = $this->createFormBuilder($order)
            ->add('name', TextType::class, ['label'=>'Name'])
            ->add('email', TextType::class, ['label'=>'Email'])
            ->add('address', TextareaType::class, ['label'=>'Address'])
            ->add('save', SubmitType::class, ['label'=>'Confirm order'])
            ->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $order = $form->getData();
            foreach($basket as $product) {
                $order->getProducts()->add($repo->find($product->getId()));
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($order);
            $entityManager->flush();
            $session->set('basket', []);
            return $this->render('confirmation.html.twig');
        }
        return $this->render('checkout.html.twig', ['total' => $total, 'form'=>$form->createView()]);
    }

    private function sendEmailConfirmation(Order $order, MailerInterface $mailer)
    {
        $email = (new TemplatedEmail())
            ->form('dumitru.mihailalin@gmail.com')
            ->to(new Address($order->getEmail(), $order->getName()))
            ->subject('Order confirmation')
            ->htmlTemplate('emails/order.html.twig')
            ->context(['order' => $order]);
        $mailer->send($email);
    }
}