<?php
// src/Admin/EnterpriseAdmin.php
namespace App\Admin;

use Doctrine\ORM\EntityManagerInterface;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\UserBundle\Model\UserInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class EnterpriseAdmin extends AbstractAdmin
{
    private TokenStorageInterface $ts;
    public function __construct( TokenStorageInterface $ts)
    {
        $this->ts = $ts;
    }
    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->add('name')
            ->add('address', TextareaType::class, [
                'required' => false
            ]);
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add('name')
            ->add('address')
            ->add('createdAt')
            ->add('createdBy');
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->addIdentifier('id')
            ->add('name')
            ->add('address')
            ->add('createdAt')
            ->add('createdBy');
    }

    protected function configureShowFields(ShowMapper $showMapper): void
    {
        $showMapper
            ->add('id')
            ->add('name')
            ->add('address')
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

    public function preUpdate(object $object): void
    {
      
    }
}