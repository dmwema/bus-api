<?php

namespace App\Admin;

use App\Entity\Competition;
use App\Entity\Notification;
use App\Service\NotificationService;
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
use Symfony\Component\Form\Extension\Core\Type\TextType;


final class UserDataAdmin extends AbstractAdmin{

    public function __construct(private NotificationService $notifyer)
    {
        
    }

    protected function configureFormFields(FormMapper $form): void
    {
       
        $form->add('name', TextType::class);
        $form->add('phone', TextType::class);
        $form->add('email', EmailType::class);
        $form->add('userType', TextType::class);
        $form->add('registeredBy', TextType::class);
        $form->add('isActive',CheckboxType::class);
        $form->add('status',ChoiceType::class,[
           
                'choices'  => [
                    'IN' => 'IN',
                    'RED'     => 'RED',
                    'ORANGE'    => 'ORAGE',
                    'GREEN'    => 'GREEN',
                ],
        
        ]);
        $form->add('province', TextType::class);
        $form->add('address', TextType::class);
        $form->add('churchName', TextType::class);
        $form->add('pastorName', TextType::class);
        $form->add('churchFile', TextType::class);
        $form->add('churchName', TextType::class);
    }

    protected function configureDatagridFilters(DatagridMapper $datagrid): void
    {
        $datagrid->add('name');
        $datagrid->add('phone');
        $datagrid->add('email');
        $datagrid->add('userType');
    
    }

    protected function configureListFields(ListMapper $list): void
    {
        
        $list->addIdentifier('name');
        $list->addIdentifier('phone');
        $list->addIdentifier('email');
        $list->addIdentifier('userType');
        $list->addIdentifier('registeredBy');
        $list->addIdentifier('birthDate');
        $list->addIdentifier('isActive');
        $list->addIdentifier('status');

        
    }

    protected function configureShowFields(ShowMapper $show): void
    {
        $show->add('name');
        $show->add('phone');
        $show->add('email');
        $show->add('userType');
        $show->add('registeredBy');
        $show->add('birthDate');
        $show->add('isActive');
        $show->add('status');
        $show->add('province');
        $show->add('address');
        $show->add('churchName');
        $show->add('pastorName');
        $show->add('churchFile');
        $show->add('churchAddress');
        
    }
    public function prePersist(object $user): void
    {
        
    }

    public function preUpdate(object $user): void
    {
        if($user->getStatus() == "RED"){
            $body = "Vous avez ete elemine de la competition";

        }
        if($user->getStatus() == "ORANGE"){
            $body = "Vous avez passe a l'epreuve suivante";

        }
        if($user->getStatus() == "GREEN"){
            $body = "Vous avez ete selectione pour la phase finale";

        }
        
        $type = "SELECTION_TYPE";
        $title = "Maajabu Rafiki";

        $notif = new Notification();
        $notif->setTitle($title);
        $notif->setBody($body);
        $notif->setType($type);
        $notif->setIsSent(false);
        
        $notif->setUsers([$user->getId()]);
        $devices = array();
        array_push($devices,$user->getDeviceToken());
        $this->notifyer->notify($devices,$notif);
    }

}