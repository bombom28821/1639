<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Order;
use App\Entity\OrderDetail;
use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserController extends AbstractController
{
    /**
     * @IsGranted("ROLE_STAFF")
     */
    #[Route('staff/showorder', name: 'staff_showorder')]
    public function index()
    {
        $orders = $this->getDoctrine()->getRepository(Order::class)->findBy(['status' => ['To Pay', 'To Receive']]);
        return $this->render('order/index.html.twig', [
            'orders' => $orders,
        ]);
    }
    /**
     * @IsGranted("ROLE_STAFF")
     */
    #[Route('staff/verifyorder/{id}', name: 'staff_verifyorder')]
    public function verifyorderAction($id)
    {
        $order = $this->getDoctrine()->getRepository(Order::class)->find($id);
        $order->setStatus('To Receive');

        $manager = $this->getDoctrine()->getManager();
        $manager->persist($order);
        $manager->flush();
        return $this->redirectToRoute('staff_showorder');
    }
    /**
     * @IsGranted("ROLE_STAFF")
     */
    #[Route('staff/cancelorder/{id}', name: 'staff_cancelorder')]
    public function cancelorderStaffAction($id)
    {
        $order = $this->getDoctrine()->getRepository(Order::class)->find($id);
        $orderDetails = $this->getDoctrine()->getRepository(OrderDetail::class)->findBy(array('Order' => $id));

        $manager = $this->getDoctrine()->getManager();
        foreach ($orderDetails as $orderDetail) {
            $book = $orderDetail->getBook();
            $orderQuantity = $book->getOrderQuantity();

            $book->removeOrderQuantity($orderQuantity);
            $manager->persist($book);
        }
        $manager->flush();

        $order->setStatus('Cancel order');
        $manager->persist($order);
        $manager->flush();

        $this->addFlash('Warn', 'Cancel order successfully!!');
        return $this->redirectToRoute('staff_showorder');
    }
    /**
     * @IsGranted("ROLE_USER")
     */
    #[Route('user/received/{id}', name: 'received_order_detail')]
    public function receivedAction($id)
    {
        $order = $this->getDoctrine()->getRepository(Order::class)->find($id);
        $order->setStatus('Received');

        $manager = $this->getDoctrine()->getManager();
        $manager->persist($order);
        $manager->flush();
        return $this->redirectToRoute('show_order');
    }
    /**
     * @IsGranted("ROLE_USER")
     */
    #[Route('user/cancelorder/{id}', name: 'user_cancelorder')]
    public function cancelorderUserAction($id)
    {
        $order = $this->getDoctrine()->getRepository(Order::class)->find($id);
        $orderDetails = $this->getDoctrine()->getRepository(OrderDetail::class)->findBy(array('Order' => $id));

        $manager = $this->getDoctrine()->getManager();
        foreach ($orderDetails as $orderDetail) {
            $book = $orderDetail->getBook();
            $orderQuantity = $book->getOrderQuantity();

            $book->removeOrderQuantity($orderQuantity);
            $manager->persist($book);
        }
        $manager->flush();

        $order->setStatus('Cancel order');
        $manager->persist($order);
        $manager->flush();

        $this->addFlash('Warn', 'Cancel order successfully!!');
        return $this->redirectToRoute('show_order');
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     */
    #[Route('admin/viewstaff', name:"view_staff")]
    public function viewStaffAction(UserRepository $userRepository) {
        $staffs = $userRepository->findUsersByRole("ROLE_STAFF");
        return $this->render('user/index.html.twig',[
            'staffs' => $staffs,
        ]);
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     */
    #[Route('admin/deletestaff/{id}', name:"delete_staff")]
    public function deleteStaffAction($id) {
        $staff = $this->getDoctrine()->getRepository(User::class)->find($id);
        if (!$staff) {
            $this->addFlash('Error', 'Staff not found!');
        } else {
            $manager = $this->getDoctrine()->getManager();
            $manager->remove($staff);
            $manager->flush();
            $this->addFlash('Warn', 'Deleted staff successfully!!');
        }
        return $this->redirectToRoute('view_staff');
    }
    
    /**
     * @IsGranted("ROLE_ADMIN")
     */
    #[Route('/admin/addStaff', name: 'add_staff')]
    public function addAuthorAction(Request $request, UserPasswordHasherInterface $userPasswordHasherInterface)
    {
        $staff = new User();
        $form = $this->createForm(UserType::class, $staff);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $staff->setPassword(
                $userPasswordHasherInterface->hashPassword(
                    $staff,
                    $form->get('plainPassword')->getData()
                )
            );
            $staff->setRoles(['ROLE_STAFF']);
            $image = $staff->getAvatar();
            //B2: t???o t??n m???i cho ???nh => t??n file ???nh l?? duy nh???t
            $imgName = uniqid(); //unique ID
            //B3: l???y ra ??u??i (extension) c???a ???nh
            $imgExtension = $image->guessExtension();
            //B4: g???p t??n m???i + ??u??i t???o th??nh t??n file ???nh ho??n thi???n
            $imageName = $imgName . "." . $imgExtension;
            //B5: di chuy???n file ???nh upload v??o th?? m???c ch??? ?????nh
            try {
                $image->move(
                    $this->getParameter('user_avatar'),
                    $imageName
                    //L??u ??: c???n khai b??o tham s??? ???????ng d???n c???a th?? m???c cho "user_cover" ??? file config/services.yaml
                );
            } catch (FileException $e) {
                // throwException($e);
            }
            //B6: l??u t??n v??o database
            $staff->setAvatar($imageName);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($staff);
            $entityManager->flush();
            // do anything else you need here, like send an email
            $this->addFlash('Success', 'Add staff successfully!');
            return $this->redirectToRoute('view_staff');
        }
        return $this->render(
            'user/add-edit-Staff.html.twig',
            [
                'form' => $form->createView(),
                'edit' => false
            ]
        );
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     */
    #[Route('/admin/editStaff/{id}', name: 'edit_staff')]
    public function editAuthorAction(Request $request, UserPasswordHasherInterface $userPasswordHasherInterface, $id)
    {
        $staff = $this->getDoctrine()->getRepository(User::class)->find($id);
        $form = $this->createForm(UserType::class, $staff);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $staff->setPassword(
                $userPasswordHasherInterface->hashPassword(
                    $staff,
                    $form->get('plainPassword')->getData()
                )
            );
            $staff->setRoles(['ROLE_STAFF']);
            $file = $form['avatar']->getData();
            if($file) {
                $image = $staff->getAvatar();
                //B2: t???o t??n m???i cho ???nh => t??n file ???nh l?? duy nh???t
                $imgName = uniqid(); //unique ID
                //B3: l???y ra ??u??i (extension) c???a ???nh
                $imgExtension = $image->guessExtension();
                //B4: g???p t??n m???i + ??u??i t???o th??nh t??n file ???nh ho??n thi???n
                $imageName = $imgName . "." . $imgExtension;
                //B5: di chuy???n file ???nh upload v??o th?? m???c ch??? ?????nh
                try {
                    $image->move(
                        $this->getParameter('user_avatar'),
                        $imageName
                        //L??u ??: c???n khai b??o tham s??? ???????ng d???n c???a th?? m???c cho "user_cover" ??? file config/services.yaml
                    );
                } catch (FileException $e) {
                    // throwException($e);
                }
                //B6: l??u t??n v??o database
                $staff->setAvatar($imageName);
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($staff);
            $entityManager->flush();
            // do anything else you need here, like send an email
            $this->addFlash('Success', 'Edit staff successfully!');
            return $this->redirectToRoute('view_staff');
        }
        return $this->render(
            'user/add-edit-Staff.html.twig',
            [
                'form' => $form->createView(),
                'edit' => true
            ]
        );
    }
    #[Route('/turnover', name: 'turnover_staff')]
    public function turnOver(){
        $orderQ = $this->getDoctrine()->getRepository(Order::class)->findAll();
        $arr = [];
        $totalP = 0;
        foreach($orderQ as $q){
            if($q->getStatus() == 'Received' && $q->getUser() == $this->getUser()){
                $totalP += $q->getTotalPrice();
            }
        }
        return $this->render('user/test.html.twig', [
            'totalP' => $totalP,
        ]);
    }

}