<?php

namespace App\Admin;

use App\Entity\Competition;
use App\Entity\Line;
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


final class PlaceAdmin extends AbstractAdmin{

    public function __construct(private NotificationService $notifyer)
    {
        
    }

    protected function configureFormFields(FormMapper $form): void
    {
        $form->add('Line', EntityType::class,[
            'class' => Line::class,
            'choice_label' => 'name',
            'multiple' => false,
            'expanded' => false,
        ]);
        $form->add('name', TextType::class);
        $form->add('latitude', NumberType::class);
        $form->add('longitude', NumberType::class);
        
        
    }

    protected function configureDatagridFilters(DatagridMapper $datagrid): void
    {
        $datagrid->add('name');
        $datagrid->add('latitude');
        $datagrid->add('longitude');
        $datagrid->add('line.name');
        
        

    
    }

    protected function configureListFields(ListMapper $list): void
    {
        
        
        $list->addIdentifier('name');
        $list->addIdentifier('line.name');
        $list->addIdentifier('latitude');
        $list->addIdentifier('longitude');

        
    }

    protected function configureShowFields(ShowMapper $show): void
    {
        $show->add('name');
        $show->add('latitude');
        $show->add('longitude');
        $show->add('createdAt');
        $show->add('line.id');

    }
    public function prePersist(object $user): void
    {
        
    }

    public function preUpdate(object $user): void
    {
      
    }

}