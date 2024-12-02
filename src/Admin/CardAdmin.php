<?php

namespace App\Admin;

use App\Entity\Competition;
use App\Entity\Line;
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
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use App\Helper\StringGenerator;
use Sonata\AdminBundle\Form\Type\ModelType;

final class CardAdmin extends AbstractAdmin{

    public function __construct(private NotificationService $notifyer)
    {
        
    }

    protected function configureFormFields(FormMapper $form): void
    {
       
        $form->tab('NFC Card')
        ->add('uid', TextType::class)
        ->add('cardHolder', TextType::class)
        ->add('phoneNumber', NumberType::class)
        ->add('isActive', CheckboxType::class)
        ->end()->end();
        $form->tab('Lines')
        ->add('liness', ModelType::class, [
            'multiple'              => true,
            'expanded'              => true,     // or false
            'class'                 => Line::class,
            'property'              => 'name',   // or any field in your media entity
            'label'                 => 'Line',
            'btn_add'               => true,
            'btn_list'              => false,
            'btn_delete'            => true,
            'btn_catalogue'         => 'admin'
        
        ])
        ->end()->end();
    }

    protected function configureDatagridFilters(DatagridMapper $datagrid): void
    {
        $datagrid->add('id');
        $datagrid->add('uid');
        $datagrid->add('code');
        $datagrid->add('cardHolder');
        $datagrid->add('phoneNumber');
        $datagrid->add('balance');
        

    
    }

    protected function configureListFields(ListMapper $list): void
    {
        
        $list->addIdentifier('id');
        $list->addIdentifier('uid');
        $list->addIdentifier('code');
        $list->addIdentifier('cardHolder');
        $list->addIdentifier('phoneNumber');
        $list->addIdentifier('balance');
        $list->addIdentifier('createdAt');
        $list->add('subscriptionFromDate');
        $list->add('subscriptionEndDate');
        $list->add('liness',null, [
            'associated_property' => 'name',  // Specify which property to show from the related Tag entity
            'label' => 'Lines',
            'sortable' => true, // Optional: allows sorting by this field
            'template' => null,  // Remove if you're not using a custom template
        ]
        );
        $list->addIdentifier('updatedAt');

        
    }

    protected function configureShowFields(ShowMapper $show): void
    {
        $show->add('uid');
        $show->add('cardHolder');
        $show->add('phoneNumber');
        $show->add('createdAt');
        $show->add('subscriptionFromDate');
        $show->add('subscriptionEndDate');
        $show->add('liness',null,
        [
            'label'=>"Lines",
            'template'=>"admin/line_list.html.twig"
        ]);
        $show->add('updatedAt');

    }
    public function prePersist(object $card): void
    {
        $generator = new StringGenerator();
        $code = $generator->generate(6);
        $card->setBalance(0);
        $card->setCode($code);
    }

    public function preUpdate(object $card): void
    {
      
    }

}