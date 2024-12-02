<?php

namespace App\Admin;

use App\Entity\Competition;
use App\Entity\Notification;
use App\Service\NotificationService;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
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


final class TransactionAdmin extends AbstractAdmin{

    public function __construct(private NotificationService $notifyer)
    {
        
    }

    protected function configureFormFields(FormMapper $form): void
    {
       
       
        
    }

    protected function configureDatagridFilters(DatagridMapper $datagrid): void
    {

        $datagrid->add('amount');
        $datagrid->add('card.uid');
        $datagrid->add('createdAt');
        $datagrid->add('route.vehicle.name');
        $datagrid->add('route.vehicle.matricule');
        $datagrid->add('oldBalance');
        $datagrid->add('newBalance');

    
    }

    protected function configureListFields(ListMapper $list): void
    {
        
        
        $list->addIdentifier('card.uid');
        $list->addIdentifier('amount');
        $list->addIdentifier('createdAt');
        $list->addIdentifier('oldBalance');
        $list->addIdentifier('newBalance');
        $list->addIdentifier('fromDate');
        $list->addIdentifier('toDate');
        $list->addIdentifier('oldFromDate');
        $list->addIdentifier('oldToDate');
        $list->addIdentifier('route.vehicle.name');

        
    }

    protected function configureShowFields(ShowMapper $show): void
    {
        $show->add('card.uid');
        $show->add('card.cardHolder');
        $show->add('amount');
        $show->add('route.vehicle.name');
        $show->add('createdAt');
        $show->add('oldBalance');
        $show->add('newBalance');
        $show->add('fromDate');
        $show->add('toDate');
        $show->add('oldFromDate');
        $show->add('oldToDate');

    }
    public function prePersist(object $user): void
    {
        
    }

    public function preUpdate(object $user): void
    {
      
    }

}