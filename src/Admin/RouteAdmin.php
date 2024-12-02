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


final class RouteAdmin extends AbstractAdmin{

    public function __construct(private NotificationService $notifyer)
    {
        
    }

    /*protected function configureFormFields(FormMapper $form): void
    {
       
        $form->add('name', TextType::class);
        $form->add('matricule', TextType::class);
        $form->add('currentLat', DoubleType::class);
        $form->add('curretLng', DoubleType::class);
        $form->add('deviceID', TextType::class);
        
    }*/

    protected function configureDatagridFilters(DatagridMapper $datagrid): void
    {
        $datagrid->add('origine');
        $datagrid->add('destination');
        $datagrid->add('startLat');
        $datagrid->add('startLng');
        $datagrid->add('endLat');
        $datagrid->add('endLng');
        $datagrid->add('passengers');
        $datagrid->add('startingTime');
        $datagrid->add('endingTime');
    
    }

    protected function configureListFields(ListMapper $list): void
    {
        
        
        $list->addIdentifier('origine');
        $list->addIdentifier('destination');
        $list->addIdentifier('startLat');
        $list->addIdentifier('startLng');
        $list->addIdentifier('endLat');
        $list->addIdentifier('endLng');
        $list->addIdentifier('passengers');
        $list->addIdentifier('driverPassengers');
        $list->addIdentifier('startingTime');
        $list->addIdentifier('endingTime');

        
    }

    protected function configureShowFields(ShowMapper $show): void
    {
        $show->add('origine');
        $show->add('destination');
        $show->add('startLat');
        $show->add('startLng');
        $show->add('endLat');
        $show->add('endLng');
        $show->add('passengers');
        $show->add('driverPassengers');
        $show->add('startingTime');
        $show->add('endingTime');
        
    }
    public function prePersist(object $user): void
    {
        
    }

    public function preUpdate(object $user): void
    {
      
    }

}