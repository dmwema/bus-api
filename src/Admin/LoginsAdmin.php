<?php

namespace App\Admin;

use App\Entity\Competition;
use App\Entity\Notification;
use App\Entity\User;
use App\Service\NotificationService;
use Doctrine\ORM\EntityManagerInterface;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;


final class LoginsAdmin extends AbstractAdmin{

    public function __construct(private NotificationService $notifyer, private EntityManagerInterface $em)
    {
        
    }

   /* protected function configureFormFields(FormMapper $form): void
    {
       
        $form->add('user', EntityType::class,[
            'class' => User::class,
            'choice_label' => 'username',
            'multiple' => false,
            'expanded' => false,
        ]);
        $form->add('amount', NumberType::class);
 
    }*/

    protected function configureDatagridFilters(DatagridMapper $datagrid): void
    {
        $datagrid->add('user.username');
        $datagrid->add('createdAt');
        $datagrid->add('endTime');
        $datagrid->add('user.vehicle.name');

        
        
    
    }

    protected function configureListFields(ListMapper $list): void
    {
        
        
        
        $list->add('user.username');
        $list->add('createdAt');
        $list->add('endTime');
        $list->add('user.vehicle.name');


        
    }

    protected function configureShowFields(ShowMapper $show): void
    {

        $show->add('user.username');
        $show->add('createdAt');
        $show->add('endTime');
        $show->add('user.vehicle.name');


    }
    public function prePersist(object $recharge): void
    {
        //$user = $this->em->getRepository(User::class)->findBy(["username"])
       

    }

    public function preUpdate(object $recharge): void
    {
        //$ticket->setUpdatedAt(new \DateTime('now',new \DateTimeZone('Africa/Kinshasa')));
        return ;


      
    }

}