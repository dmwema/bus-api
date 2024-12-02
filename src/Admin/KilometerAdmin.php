<?php

namespace App\Admin;

use App\Entity\Competition;
use App\Entity\Notification;
use App\Entity\Region;
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


final class KilometerAdmin extends AbstractAdmin{

    public function __construct(private NotificationService $notifyer)
    {
        
    }

    protected function configureFormFields(FormMapper $form): void
    {
     
        
    }

    protected function configureDatagridFilters(DatagridMapper $datagrid): void
    {
        $datagrid->add('vehicle.name');
        $datagrid->add('kilometer');
        $datagrid->add('createdAt');
        $datagrid->add('user.username');
        
        

    
    }

    protected function configureListFields(ListMapper $list): void
    {
        
        
        $list->addIdentifier('vehicle.name');
        $list->addIdentifier('kilometer');
        $list->addIdentifier('createdAt');
        $list->addIdentifier('user.username');

        
    }

    protected function configureShowFields(ShowMapper $show): void
    {
        $show->add('vehicle.name');
        $show->add('kilometer');
        $show->add('createdAt');
        $show->add('user.username');

    }
    public function prePersist(object $user): void
    {
        
    }

    public function preUpdate(object $user): void
    {
      
    }

}