<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use AppBundle\Entity\Stock;
use AppBundle\Entity\Produit;

class DefaultController extends Controller
{
    /**
        * @Route("/", name="homepage")
        */
        public function homepageAction()
        {
          return $this ->render('home/homepage.html.twig');
        }

        /**
           * @Route("/product/add_product", name="add_product")
           */
          public function addProductAction(Request $request)
          {
              $e = new Produit();
              $repository = $this->getDoctrine()->getRepository(Produit::class);
              $products = $repository->findAll();


              $form = $this->createFormBuilder($e)
                ->add('nom', TextType::class)
                ->add('categorie', TextType::class)
                ->add('prixHT', NumberType::class)
                ->add('tVA', NumberType::class)
                ->add('submit', SubmitType::class, array('label' => 'Ajouter le produit'))
                ->getForm();

                $form->handleRequest($request);
                if($form->isValid()){
                  //entité manager
                  $em = $this->getDoctrine()->getManager();
                  $em->persist($e);
                  $em ->flush();
                  return $this-> redirect($this->generateUrl("add_product"));
                }

                return $this-> render('product/add_product.html.twig', [
                    'f' => $form->createView(),
                    'products' => $products,
                ]);
          }

          /**
 * @Route("/product/delete_product/{id}", name="product_delete")
 */
      public function deleteAction($id)
      {
          $produit = $this->getDoctrine()
                  ->getRepository('AppBundle:Produit')
                  ->find($id);

          if (empty($produit)) {
              $this->addFlash('error', 'Produit not found');

              return $this->redirectToRoute('add_product');
          }

          $em = $this->getDoctrine()->getManager();
          $em->remove($produit);
          $em->flush();
          $this->addFlash('notice', 'Produit supprimé');

          return $this->redirectToRoute('add_product');
      }


          /**
             * @Route("/product/add_stock/{id}", name="add_stock")
             */
             public function addStockAction(Request $request)

              {
                $s = new Stock();
                $repository = $this->getDoctrine()->getRepository(Stock::class);
                $stocks = $repository->findAll();


                              $form = $this->createFormBuilder($s)
                                ->add('quantity', NumberType::class)
                                ->add('idProduit', NumberType::class)
                                ->add('submit', SubmitType::class, array('label' => 'Ajouter dans le stock'))
                                ->getForm();

                                $form->handleRequest($request);
                                if($form->isValid()){
                                  //entité manager
                                  $em = $this->getDoctrine()->getManager();
                                  $em->persist($s);
                                  $em ->flush();
                                  return $this-> redirect($this->generateUrl("add_product"));
                                }

                                return $this-> render('product/add_stock.html.twig', [
                                    'l' => $form->createView(),
                                    'stocks' => $stocks,

                  ]);
              }



              /**
                 * @Route("/product/edit_product/{id}", name="edit_product")
                 */
                 public function editProductAction(Produit $produit ,Request $request)
                 {


                    if (empty($produit)) {
                        $this->addFlash('error', 'Produit not found');

                        return $this->redirectToRoute('add_product');
                    }

                    $atrributes = array('class' => 'form-control' , 'style' => 'margin-bottom:15px');
                    $choices = array('Low' => 'Low', 'Normal' => 'Normal', 'High' => 'High');
                    $form = $this->createFormBuilder($produit)
                            ->add('nom', TextType::class, array('attr' => $atrributes))
                            ->add('categorie', TextType::class, array('attr' => $atrributes))
                            ->add('prixHT', NumberType::class, array('attr' => $atrributes))
                            ->add('tVA', NumberType::class, array('attr' => $atrributes))
                            ->add('save', SubmitType::class, array('label' => 'Modifier Produit', 'attr' => array('class' => 'btn btn-primary')))
                            ->getForm();

                    $form->handleRequest($request);

                    if($form->isSubmitted() && $form->isValid()) {
                        $produit->setNom($form['nom']->getData());
                        $produit->setCategorie($form['categorie']->getData());
                        $produit->setPrixHT($form['prixHT']->getData());
                        $produit->setTVA($form['tVA']->getData());


                        $em = $this->getDoctrine()->getManager();
                        $em->persist($produit);
                        $em->flush();

                        $this->addFlash('notice', 'Produit modifié');

                        return $this->redirectToRoute('add_product');
                    }

                    return $this->render('product/edit_product.html.twig', array(
                        'f' => $form->createView(),
                        'products' => $produit
                    ));
                }

  /////////////////////////// ///////////////////////////////////////////////////////////////////
                /**
                   * @Route("/product/edit_stock/{id}", name="edit_stock")
                   */
                   public function editStocktAction(Stock $stock,Request $request)
                   {


                      if (empty($stock)) {
                          $this->addFlash('error', 'stock not found');

                          return $this->redirectToRoute('add_stock');
                      }

                      $atrributes = array('class' => 'form-control' , 'style' => 'margin-bottom:15px');
                      $choices = array('Low' => 'Low', 'Normal' => 'Normal', 'High' => 'High');
                      $form = $this->createFormBuilder($stock)
                              ->add('idProduit', NumberType::class, array('attr' => $atrributes))
                              ->add('quantity', NumberType::class, array('attr' => $atrributes))
                              ->add('save', SubmitType::class, array('label' => 'Modifier stock', 'attr' => array('class' => 'btn btn-primary')))
                              ->getForm();

                      $form->handleRequest($request);

                      if($form->isSubmitted() && $form->isValid()) {
                          $stock->setIdProduit($form['idProduit']->getData());
                          $stock->setQuantity($form['quantity']->getData());


                          $em = $this->getDoctrine()->getManager();
                          $em->persist($stock);
                          $em->flush();

                          $this->addFlash('notice', 'stock modifié');

                          return $this->redirectToRoute('add_stock');
                      }

                      return $this->render('product/edit_stock.html.twig', array(
                          'l' => $form->createView(),
                          'stocks' => $stock
                      ));


                  }

                  /**
                  * @Route("/product/delete_stock/{id}", name="stock_delete")
                  */
                  public function deleteStockAction($id)
                  {
                  $stock = $this->getDoctrine()
                          ->getRepository('AppBundle:Stock')
                          ->find($id);

                  if (empty($stock)) {
                      $this->addFlash('error', 'Stock not found');

                      return $this->redirectToRoute('add_stock');
                  }

                  $em = $this->getDoctrine()->getManager();
                  $em->remove($stock);
                  $em->flush();
                  $this->addFlash('notice', 'stock supprimé');

                  return $this->redirectToRoute('add_stock');
                  }

                  /**
                       * @Route("/product/login", name="login")
                       */
                      public function loginAction(Request $request)
                      {
                          $authenticationUtils = $this->get('security.authentication_utils');

                          $error = $authenticationUtils->getLastAuthenticationError();
                          $lastUsername = $authenticationUtils->getLastUsername();


                          return $this->render('product/login.html.twig', [
                              'last_username' => $lastUsername,
                              'error' => $error,
                          ]);
                      }



}
