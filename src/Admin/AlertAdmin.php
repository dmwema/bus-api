<?php

namespace App\Admin;

use App\Entity\Competition;
use App\Entity\Notification;
use App\Entity\Region;
use App\Entity\Vehicle;
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
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;


final class AlertAdmin extends AbstractAdmin{

    public function __construct(private NotificationService $notifyer)
    {
        
    }

    protected function configureFormFields(FormMapper $form): void
    {
        $form->add('vehicle', EntityType::class,[
            'class' => Vehicle::class,
            'choice_label' => 'name',
            'multiple' => false,
            'expanded' => false,
        ]);
        
        $form->add('title', TextType::class,[
            
        ]);
        $form->add('description', TextType::class,[
            
        ]);
       
        
        
    }

    protected function configureDatagridFilters(DatagridMapper $datagrid): void
    {
        $datagrid->add('vehicle.name');
        $datagrid->add('title');
        $datagrid->add('description');
        $datagrid->add('createdAt');
        
        
        

    
    }

    protected function configureListFields(ListMapper $list): void
    {
        
        
        $list->addIdentifier('vehicle.name');
        $list->addIdentifier('title');
        $list->addIdentifier('description');
        $list->addIdentifier('createdAt');
        $list->addIdentifier('isSeen');

        
    }

    protected function configureShowFields(ShowMapper $show): void
    {
        $show->add('vehicle.name');
        $show->add('title');
        $show->add('description');
        $show->add('createdAt');
        $show->add('isSeen');

    }
    public function prePersist(object $alert): void
    {
        $alert->setIsSeen(false);
    }

    public function preUpdate(object $alert): void
    {
      
    }

}