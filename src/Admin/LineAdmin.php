<?php
// src/Admin/LineAdmin.php
namespace App\Admin;

use App\Entity\Enterprise;
use App\Entity\Region;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\UserBundle\Model\UserInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class LineAdmin extends AbstractAdmin
{
    private TokenStorageInterface $ts;
    public function __construct( TokenStorageInterface $ts)
    {
        $this->ts = $ts;
    }

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
        ->add('region', EntityType::class,[
            'class' => Region::class,
            'choice_label' => 'name',
            'multiple' => false,
            'expanded' => false,
        ])
        ->add('enterprise', EntityType::class,[
            'class' => Enterprise::class,
            'required'=>false,
            'choice_label' => 'name',
            'multiple' => false,
            'expanded' => false,
        ])
            ->add('name')
            ->add('description', TextareaType::class, [
                'required' => false,
            ])
            ->add('paymentType', ChoiceType::class,[
                'required'=> true,
                'choices'=>[  'DEDUCTED'=>"DEDUCTED", 'SUBSCRIPTION'=>"SUBSCRIPTION"  ]
            ])
            ->add('ticketPrice', NumberType::class, [
                'required' => false,
            ]);
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add('name')
            ->add('description')
            ->add('paymentType')
            ->add('region.name')
            ->add('enterprise.name')
            ->add('ticketPrice')
            ->add('createdAt')
            ->add('createdBy');
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->addIdentifier('id')
            ->add('name')
            ->add('description')
            ->add('paymentType')
            ->add('region',null,
            [
                'associated_property' => 'name', // Use the title property of the Post entity
                'label' => 'Region',
                'render' => function($line) {
        return $line->getRegion()->getName(); // Custom rendering of the post title
    }         // Label in the admin list view
            ])
            ->add('enterprise.name')
            ->add('ticketPrice')
            ->add('createdAt')
            ->add('createdBy');
    }

    protected function configureShowFields(ShowMapper $showMapper): void
    {
        $showMapper
            ->add('id')
            ->add('name')
            ->add('description')
            ->add('paymentType')
            ->add('region.name')
            ->add('enterprise.name')
            ->add('ticketPrice')
            ->add('createdAt')
            ->add('createdBy');
    }
    public function prePersist(object $object): void
    {
        $token = $this->ts->getToken();
        if ($token && $token->getUser() instanceof UserInterface) {
            $user = $token->getUser();
            // Set the createdBy field
            $object->setCreatedBy($user->getUserIdentifier());
        }
        
    }
}