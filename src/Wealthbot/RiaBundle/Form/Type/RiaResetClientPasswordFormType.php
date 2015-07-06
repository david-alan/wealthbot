<?php
/**
 * Created by JetBrains PhpStorm.
 * User: amalyuhin
 * Date: 25.07.13
 * Time: 12:34
 * To change this template use File | Settings | File Templates.
 */

namespace Wealthbot\RiaBundle\Form\Type;


use Doctrine\ORM\EntityRepository;
use Wealthbot\UserBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class RiaResetClientPasswordFormType extends AbstractType
{
    private $ria;

    public function __construct(User $ria)
    {
        $this->ria = $ria;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $ria = $this->ria;

        $builder->add('user', 'entity', array(
            'class' => 'WealthbotUserBundle:User',
            'property' => 'email',
            'query_builder' => function(EntityRepository $er) use ($ria){
                return $er->createQueryBuilder('u')
                    ->leftJoin('u.profile', 'p')
                    ->where('p.ria_user_id = :ria_id')
                    ->andWhere('u.roles LIKE :role')
                    ->setParameters(array(
                        'ria_id' => $ria->getId(),
                        'role' => '%ROLE_RIA%'
                    ))
                    ->orderBy("u.email", "ASC");
            }
        ));
    }

    public function getName()
    {
        return 'reset_password';
    }

}