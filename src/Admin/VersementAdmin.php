<?php

namespace App\Admin;

use App\Entity\Competition;
use App\Entity\Notification;
use App\Entity\Region;
use App\Entity\User;
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


final class VersementAdmin extends AbstractAdmin{

    private $ts;

    public function __construct($ts,private NotificationService $notifyer)
    {
        $this->ts = $ts;
    }

    protected function configureFormFields(FormMapper $form): void
    {
        $form->add('vehicle', EntityType::class,[
            'class' => Vehicle::class,
            'choice_label' => 'name',
            'multiple' => false,
            'expanded' => false,
        ]);
       
        $form->add('amount', NumberType::class);
        $form->add('driver', EntityType::class,[
            'class' => User::class,
            'choice_label' => 'username',
            'multiple' => false,
            'expanded' => false,
        ]);
        
    }

    protected function configureDatagridFilters(DatagridMapper $datagrid): void
    {
        $datagrid->add('vehicle.name');
        $datagrid->add('amount');
        $datagrid->add('createdAt');
        $datagrid->add('driver.username');

    }

    protected function configureListFields(ListMapper $list): void
    {
        
        
        $list->addIdentifier('vehicle.name');
        $list->addIdentifier('amount');
        $list->addIdentifier('createdAt');
        $list->addIdentifier('driver.username');

        
    }

    protected function configureShowFields(ShowMapper $show): void
    {
        $show->add('vehicle.name');
        $show->add('amount');
        $show->add('createdAt');
        $show->add('driver.username');

    }
    public function prePersist(object $vers): void
    {
        $me = $this->ts->getToken()->getUser();
        $vers->setCreatedBy($me->getUsername());
    }

    public function preUpdate(object $user): void
    {
      
    }

}