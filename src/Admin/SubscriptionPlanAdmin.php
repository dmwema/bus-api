<?php
namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class SubscriptionPlanAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->add('title')
            ->add('description', null, [
                'required' => false
            ])
            ->add('amount')
            ->add('duration',IntegerType::class,[
                'required'=> true,
                'label'=>"Number of days"
            ]);
    }

    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('title')
            ->add('amount')
            ->add('duration')
            ->add('createdBy')
            ->add('createdAt');
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->addIdentifier('id')
            ->add('title')
            ->add('amount')
            ->add('duration')
            ->add('createdBy')
            ->add('createdAt');
    }

    protected function configureShowFields(ShowMapper $show): void
    {
        $show
            ->add('id')
            ->add('title')
            ->add('description')
            ->add('amount')
            ->add('duration')
            ->add('createdBy')
            ->add('createdAt');
    }
    public function prePersist(object $object): void
    {
        
    }

    public function preUpdate(object $object): void
    {
      
    }

}