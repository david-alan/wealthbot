<?php
/**
 * Created by JetBrains PhpStorm.
 * User: amalyuhin
 * Date: 14.05.13
 * Time: 13:07
 * To change this template use File | Settings | File Templates.
 */

namespace Wealthbot\RiaBundle\Form\EventListener;


use Wealthbot\ClientBundle\Entity\ClientAccount;
use Wealthbot\ClientBundle\Entity\ClientAdditionalContact;
use Wealthbot\ClientBundle\Form\Type\OtherAccountOwnerFormType;
use Wealthbot\ClientBundle\Model\ClientAccountOwner;
use Wealthbot\UserBundle\Entity\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

class RiaClientAccountOwnerFormEventSubscriber implements EventSubscriberInterface
{
    private $factory;
    private $client;
    private $account;
    private $isJoint;

    public function __construct(FormFactoryInterface $factory, User $client, ClientAccount $account = null, $isJoint = false)
    {
        $this->factory = $factory;
        $this->client = $client;
        $this->account = $account;
        $this->isJoint = $isJoint;
    }

    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::PRE_BIND     => 'preBind',
            //FormEvents::BIND         => 'bind'
        );
    }

    /**
     * PRE_SET_DATA event handler
     *
     * @param FormEvent $event
     */
    public function preSetData(FormEvent $event)
    {
        $data['owner_types'] = $this->getOwnerTypeData();
        $this->update($event->getForm(), $data);
    }

    /**
     * PRE_BIND event handler
     *
     * @param FormEvent $event
     */
    public function preBind(FormEvent $event)
    {
        $this->update($event->getForm(), $event->getData());
    }

    /**
     * Update form fields
     *
     * @param FormInterface $form
     * @param null $data
     */
    private function update(FormInterface $form, $data)
    {
        if (!is_array($data) || !array_key_exists('owner_types', $data)) {
            return;
        }

        if ($this->isJoint) {
            $form->add(
                $this->factory->createNamed('owner_types', 'choice', $data['owner_types'], array(
                    'mapped' => false,
                    'choices' => $this->getOwnerTypesChoices(),
                    'expanded' => true,
                    'multiple' => true,
                    'data' => $data['owner_types']
                ))
            );

            if (is_array($data) && (
                    array_key_exists('other_contact', $data) ||
                    in_array(ClientAccountOwner::OWNER_TYPE_OTHER, $data['owner_types'])
                )
            ) {
                $form->add(
                    $this->factory->createNamed(
                        'other_contact',
                        new OtherAccountOwnerFormType($this->getOtherContactData())
                    )
                );
            }

        } else {
            $form->add(
                $this->factory->createNamed('owner_types', 'choice', $data, array(
                    'mapped' => false,
                    'choices' => $this->getOwnerTypesChoices(),
                    'expanded' => true,
                    'multiple' => false,
                    'data' => $data
                ))
            );
        }
    }

    /**
     * Get data for owner_type field
     *
     * @return array|string
     */
    private function getOwnerTypeData()
    {
        $data = array();

        if ($this->account) {
            $accountOwners = $this->account->getAccountOwners();

            if ($this->isJoint) {
                foreach ($accountOwners as $accountOwner) {
                    $data[] = $accountOwner->getOwnerType();
                }

            } else {
                foreach ($accountOwners as $accountOwner) {
                    $data = $accountOwner->getOwnerType();
                }
            }
        }

        return $data;
    }

    /**
     * Get data for other_contact field
     *
     * @return ClientAdditionalContact|null
     */
    private function getOtherContactData()
    {
        $data = null;

        if ($this->account) {
            foreach ($this->account->getAccountOwners() as $accountOwner) {
                if ($accountOwner->getOwnerType() === ClientAccountOwner::OWNER_TYPE_OTHER) {
                    $data = $accountOwner->getContact();
                }
            }
        }

        return $data;
    }

    /**
     * Get choices for owner_type field
     *
     * @return array
     */
    private function getOwnerTypesChoices()
    {
        $choices = array(
            ClientAccountOwner::OWNER_TYPE_SELF => $this->client->getFirstName()
        );

        if ($this->client->isMarried()) {
            $choices[ClientAccountOwner::OWNER_TYPE_SPOUSE] = $this->client->getSpouseFirstName();
        }

        if ($this->isJoint) {
            $choices[ClientAccountOwner::OWNER_TYPE_OTHER] = 'Other';
        }

        return $choices;
    }
}