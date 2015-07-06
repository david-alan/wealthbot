<?php
/**
 * Created by PhpStorm.
 * User: amalyuhin
 * Date: 11.12.13
 * Time: 13:08
 */

namespace Wealthbot\ClientBundle\Form\Type;


use Wealthbot\UserBundle\Entity\Profile;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormInterface;

class AccountOwnerReviewInformationFormType extends AccountOwnerPersonalInformationFormType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('first_name', 'text', array(
                'required' => false
            ))
            ->add('middle_name', 'text', array(
                'required' => false
            ))
            ->add('last_name', 'text', array(
                'required' => false
            ))
            ->add('street', 'text', array('required' => false))
            ->add('city', 'text', array('required' => false))
            ->add('state', 'entity', array(
                'class' => 'WealthbotAdminBundle:State',
                'label' => 'State',
                'empty_value' => 'Select a State',
                'required' => true
            ))
            ->add('zip', 'text', array('required' => false))
            ->add('is_different_address', 'checkbox', array(
                'label' => 'Is your mailing address different than the one above?',
                'required' => false
            ))
            ->add('mailing_street', 'text', array('required' => false))
            ->add('mailing_city', 'text', array('required' => false))
            ->add('mailingState', 'entity', array(
                'class' => 'WealthbotAdminBundle:State',
                'label' => 'Mailing state',
                'empty_value' => 'Select a State',
                'required' => false
            ))
            ->add('mailing_zip', 'text', array('required' => false))
            ->add('birth_date', 'date', array(
                'widget' => 'single_text',
                'format' => 'MM-dd-yyyy',
                'required' => true,
                'attr' => array('class' => 'jq-date input-small')
            ))
            ->add('phone_number', 'text', array('required' => false))
            ->add('email', 'text', array('required' => true));

        parent::buildForm($builder, $options);
    }

    public function validate(FormEvent $event)
    {
        parent::validate($event);

        $this->validateOwnerInformation($event->getForm(), $event->getData());
    }

    private function validateOwnerInformation(FormInterface $form, $data)
    {
        if ($form->has('first_name') && !$data->getFirstName()) {
            $form->get('first_name')->addError(new FormError('Required.'));
        }
        if ($form->has('middle_name') && !preg_match('/[A-Za-z]/', $data->getMiddleName())) {
            $form->get('middle_name')->addError(new FormError('Enter least 1 letter.'));
        }
        if ($form->has('last_name') && !$data->getLastName()) {
            $form->get('last_name')->addError(new FormError('Required.'));
        }
        if ($form->has('street') && !$data->getStreet()) {
            $form->get('street')->addError(new FormError('Required.'));
        }
        if ($form->has('city') && !$data->getCity()) {
            $form->get('city')->addError(new FormError('Required.'));
        }
        if ($form->has('state') && !$data->getState()) {
            $form->get('state')->addError(new FormError('Required.'));
        }
        if ($form->has('estimated_income_tax') && !$data->getEstimatedIncomeTax()) {
            $form->get('estimated_income_tax')->addError(new FormError('Required.'));
        }

        // Choices validation
        if ($form->has('marital_status') && !in_array($data->getMaritalStatus(), Profile::getMaritalStatusChoices())) {
            $form->get('marital_status')->addError(new FormError('Required.'));
        }
        if ($form->has('annual_income') && !in_array($data->getAnnualIncome(), Profile::getAnnualIncomeChoices())) {
            $form->get('annual_income')->addError(new FormError('Required.'));
        }
        if ($form->has('liquid_net_worth') && !in_array($data->getLiquidNetWorth(), array_keys(Profile::getLiquidNetWorthChoices())) ) {
            $form->get('liquid_net_worth')->addError(new FormError('Required.'));
        }
        if ($form->has('employment_type') && !in_array($data->getEmploymentType(), array_keys(Profile::getEmploymentTypeChoices())) ) {
            $form->get('employment_type')->addError(new FormError('Required.'));
        }

        if ($form->has('birth_date')) {
            $birthDateData = $form->get('birth_date')->getData();

            if ($birthDateData && $birthDateData instanceof \DateTime) {
                $year = (int) $birthDateData->format('Y');

                if ($year < 1900) {
                    $form->get('birth_date')->addError(new FormError('year must start with 19 or 20 e.g. 1980'));
                }
            } else {
                $form->get('birth_date')->addError(new FormError('date format must be MM-DD-YYYY'));
            }
        }
    }
} 