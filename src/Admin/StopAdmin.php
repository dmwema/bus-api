<?php
// src/Admin/StopAdmin.php
namespace App\Admin;

use App\Entity\Line;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\UserBundle\Model\UserInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class StopAdmin extends AbstractAdmin
{
    private TokenStorageInterface $ts;
    public function __construct( TokenStorageInterface $ts)
    {
        $this->ts = $ts;
    }
    protected function configureFormFields(FormMapper $form): void
    {
        $form->add('Line', EntityType::class,[
            'class' => Line::class,
            'choice_label' => 'name',
            'multiple' => false,
            'expanded' => false,
        ])
            ->add('name')
            ->add('lat')
            ->add('lng');
            
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add('name')
            ->add('lat')
            ->add('lng')
            ->add('line.name')
            ->add('createdBy')
            ->add('createdAt');
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->addIdentifier('id')
            ->add('name')
            ->add('lat')
            ->add('lng')
            ->add('line.name')
            ->add('createdBy')
            ->add('createdAt');
    }

    protected function configureShowFields(ShowMapper $showMapper): void
    {
        $showMapper
            ->add('id')
            ->add('name')
            ->add('lat')
            ->add('lng')
            ->add('line.name')
            ->add('createdBy')
            ->add('createdAt');
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

    public function preUpdate(object $user): void
    {
      
    }
}
