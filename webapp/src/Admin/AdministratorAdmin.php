<?php

namespace App\Admin;

use App\Entity\Administrator;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class AdministratorAdmin extends AbstractAdmin
{
    public function toString($object): string
    {
        return $object instanceof Administrator
            ? sprintf('Admin %s', $object->getEmail())
            : 'Admin';
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->add('email', null, [
                'label' => 'Email',
            ])
            ->add('enabled', null, [
                'label' => 'Actif ?',
            ])
            ->add('lastLoginAt', null, [
                'label' => 'Dernière connexion',
                'pattern' => 'dd/MM/yyyy HH:mm:ss',
                'locale' => 'fr',
                'timezone' => 'Europe/Paris',
            ])
            ->add('createdAt', null, [
                'label' => 'Date d\'ajout',
                'pattern' => 'dd/MM/yyyy',
                'locale' => 'fr',
                'timezone' => 'Europe/Paris',
            ])
            ->add(ListMapper::NAME_ACTIONS, null, [
                'actions' => [
                    'edit' => [],
                    'delete' => [],
                ],
            ]);
    }

    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->with('Général', [
                'class' => 'col-md-4',
            ])
            ->add('id', null, ['disabled' => true])
            ->add('email', null, [
                'label' => 'Email',
            ])
            ->end()
            ->with('Informations', [
                'class' => 'col-md-4',
            ])
            ->add('createdAt', null, [
                'widget' => 'single_text',
                'disabled' => true,
                'label' => 'Date création',
                'html5' => false,
                'format' => DateTimeType::DEFAULT_DATE_FORMAT,
            ])
            ->add('updatedAt', null, [
                'widget' => 'single_text',
                'disabled' => true,
                'label' => 'Date dernière modification',
                'html5' => false,
                'format' => DateTimeType::DEFAULT_DATE_FORMAT,
            ])
            ->add('lastLoginAt', null, [
                'widget' => 'single_text',
                'disabled' => true,
                'label' => 'Date dernière connexion',
                'html5' => false,
                'format' => DateTimeType::DEFAULT_TIME_FORMAT,
            ])
            ->end()
            ->with('Sécurité', [
                'class' => 'col-md-4',
                'box_class' => 'box box-solid box-danger',
            ])
            ->add('enabled', null, [
                'required' => false,
                'label' => 'Compte actif ?',
            ])
            ->end();
    }

    public function configureShowFields(ShowMapper $show): void
    {
        $show
            ->with('Général', ['class' => 'col-md-8'])
            ->add('id', null, ['label' => 'Identifiant'])
            ->add('email', null, ['label' => 'Email'])
            ->end()
            ->with('Informations', ['class' => 'col-md-8'])
            ->add('createdAt', null, [
                'label' => 'Date d\'ajout',
                'pattern' => 'dd/MM/yyyy',
                'locale' => 'fr',
            ])
            ->add('updatedAt', null, [
                'label' => 'Date de dernière modification',
                'pattern' => 'dd/MM/yyyy',
                'locale' => 'fr',
            ])
            ->add('lastLoginAt', null, [
                'label' => 'Date de dernière connexion',
                'pattern' => 'dd/MM/yyyy',
                'locale' => 'fr',
            ])
            ->end()
            ->with('Sécurité', [
                'class' => 'col-md-8',
                'box_class' => 'box box-solid box-danger',
            ])
            ->add('enabled', null, ['label' => 'Compte actif ?'])
            ->end();
    }
}
