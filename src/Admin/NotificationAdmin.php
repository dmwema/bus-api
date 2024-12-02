<?php

namespace App\Admin;

use App\Entity\Competition;
use App\Entity\UserData;
use App\Service\NotificationService;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;


final class NotificationAdmin extends AbstractAdmin{
    public function __construct(private NotificationService $notifyer)
    {
        
    }

    protected function configureFormFields(FormMapper $form): void
    {
       
        $form->add('title', TextType::class);
        $form->add('body', TextType::class);
        $form->add('type', TextType::class);
        //$form->add('isSent', CheckboxType::class);
        $form->add('users', ChoiceType::class,[
            
        
            // uses the User.username property as the visible option string
            'choices'  => $this->getUSerList(),
            //'choice_label' => 'username',
               'multiple' => true,
               'expanded' => false,
        ]);

    }

    protected function configureDatagridFilters(DatagridMapper $datagrid): void
    {
        $datagrid->add('title');
        $datagrid->add('body');
        $datagrid->add('type');
    
    }

    protected function configureListFields(ListMapper $list): void
    {
        
        $list->addIdentifier('title');
        $list->addIdentifier('body');
        $list->addIdentifier('type');
        $list->addIdentifier('isSent');
        $list->addIdentifier('users');
        $list->addIdentifier('createdAt');
        
    }

    protected function configureShowFields(ShowMapper $show): void
    {
        $show->add('title');
        $show->add('body');
        $show->add('type');
        $show->add('isSent');
        $show->add('createdAt');
        $show->add('sentTime');
    }

    public function prePersist(object $notif): void
    {
       /* $choices = array();
        if($notif->getUsers()){
            foreach($notif->getUsers() as $obj){
                array_push($choices,$obj->getId());
            }
        }*/
        $notif->setIsSent(false);
        //$notif->setUsers($choices);
        $users = $this->getModelManager()->getEntityManager(UserData::class)->getRepository(UserData::class)->findBy(array('id' => $notif->getUsers()));
        $devices = array();
        foreach($users as $user){
            array_push($devices,$user->getDeviceToken());
        }
        
        $this->notifyer->notify($devices,$notif);
    }

    function getUSerList():array{
        $users = $this->getModelManager()->getEntityManager(UserData::class)->getRepository(UserData::class)->findAll();
        $choices = array();
        foreach($users as $usr){
            
            $choices[$usr->getUsername()] = $usr->getId();
        }
        return $choices;

    }
  

    

}