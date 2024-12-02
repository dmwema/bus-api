<?php

namespace App\Admin;

use App\Entity\Competition;
use App\Entity\Notification;
use App\Entity\User;
use App\Helper\StringGenerator;
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
use Symfony\Component\Security\Csrf\TokenStorage\TokenStorageInterface;


final class RechargeUserAdmin extends AbstractAdmin{
    private $ts;
    public function __construct( $ts, private NotificationService $notifyer, private EntityManagerInterface $em)
    {
        $this->ts = $ts;
    }

    protected function configureFormFields(FormMapper $form): void
    {
        if(!$this->isCurrentRoute('create')){
            return;
            
        }
        $form->add('user', EntityType::class,[
            'class' => User::class,
            'choice_label' => 'username',
            'multiple' => false,
            'expanded' => false,
        ]);
        $form->add('amount', NumberType::class);
 
    }

    protected function configureDatagridFilters(DatagridMapper $datagrid): void
    {
        $datagrid->add('user.username');
        $datagrid->add('amount');
        $datagrid->add('createdAt');
        $datagrid->add('createdBy');

        
        
    
    }

    protected function configureListFields(ListMapper $list): void
    {
        
        
        $list->addIdentifier('user.username');
        $list->addIdentifier('amount');
        $list->addIdentifier('oldBalance');
        $list->addIdentifier('newBalance');
        $list->addIdentifier('reference');
        $list->addIdentifier('createdAt');
        $list->addIdentifier('createdBy');

        
    }

    protected function configureShowFields(ShowMapper $show): void
    {

        $show->add('user.username');
        $show->add('amount');
        $show->add('oldBalance');
        $show->add('newBalance');
        $show->add('reference');
        $show->add('createdAt');
        $show->add('createdBy');

    }
    public function prePersist(object $recharge): void
    {
        //$user = $this->em->getRepository(User::class)->findBy(["username"])
        $generator = new StringGenerator();
        $user = $recharge->getUser();
        $me = $this->ts->getToken()->getUser();
        $recharge->setCreatedBy($me->getUsername());
        if($user->getBalance() == null){
            $user->setBalance($recharge->getAmount());
            $recharge->setOldBalance(0);
            $recharge->setNewBalance($recharge->getAmount());
        }else{
            $recharge->setOldBalance($user->getBalance());

            $user->setBalance($user->getBalance() + $recharge->getAmount());
            
            $recharge->setNewBalance($user->getBalance());
        }
         $recharge->setReference($generator->generate(10));
            
            $user->setUpdatedAt(new \DateTime('now',new \DateTimeZone('Africa/Kinshasa')));
            $this->em->flush();

    }

    public function preUpdate(object $recharge): void
    {
        //$ticket->setUpdatedAt(new \DateTime('now',new \DateTimeZone('Africa/Kinshasa')));
        return ;


      
    }

}