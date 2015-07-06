<?php
/**
 * Created by JetBrains PhpStorm.
 * User: amalyuhin
 * Date: 22.10.12
 * Time: 12:04
 * To change this template use File | Settings | File Templates.
 */

namespace Wealthbot\AdminBundle\Form\EventListener;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Wealthbot\AdminBundle\Entity\CeModel;
use Wealthbot\AdminBundle\Entity\CeModelEntity;
use Wealthbot\AdminBundle\Entity\SecurityAssignment;
use Wealthbot\AdminBundle\Entity\Subclass;
use Wealthbot\AdminBundle\Repository\CeModelEntityRepository;
use Wealthbot\AdminBundle\Repository\SecurityAssignmentRepository;
use Wealthbot\RiaBundle\RiskManagement\BaselinePortfolio;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvents;
use Wealthbot\UserBundle\Entity\User;

class CeModelEntityTypeEventsListener implements EventSubscriberInterface
{
    /** @var $factory FormFactoryInterface */
    private $factory;

    /** @var EntityManager $em */
    private $em;

    /** @var CeModel */
    private $ceModel;

    /** @var User */
    private $user;

    private $isQualifiedModel;

    public function __construct(FormFactoryInterface $factory, EntityManager $em, CeModel $ceModel, User $user, $isQualifiedModel = false)
    {
        $this->factory = $factory;
        $this->em = $em;
        $this->ceModel = $ceModel;
        $this->user = $user;
        $this->isQualifiedModel = $isQualifiedModel;
    }

    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::POST_SET_DATA => 'preSetData',
            FormEvents::PRE_BIND => 'preBind',
            FormEvents::BIND => 'bind'
        );
    }

    public function preSetData(FormEvent $event)
    {
        $form = $event->getForm();
        /** @var $data CeModelEntity */
        $data = $event->getData();

        if ($data == null) {
            $this->updateMuniSubstitutionSymbol($form, null);
            $this->updateSecuritySymbol($form, null);
            $this->updateSecurity($form, null);
            $this->updateSubclass($form, null);
            $this->updateTaxLossHarvesting($form, null, array());
            $this->updateTaxLossHarvestingIdSymbol($form, null);
        } else {
            if ($data->getMuniSubstitution()) {
                $this->updateMuniSubstitutionSymbol($form, $data->getMuniSubstitutionId());
            }

            if ($data->getSecurityAssignment()) {
                $this->updateSecuritySymbol($form, $data->getSecurityAssignmentId());
                $this->updateTaxLossHarvesting($form, $data->getSubclassId(), array($data->getSecurityAssignmentId(), $data->getMuniSubstitutionId()));
            }

            if ($data->getAssetClass()) {
                $this->updateSubclass($form, $data->getAssetClassId());
            }

            if ($data->getSubClass()) {
                $this->updateSecurity($form, $data->getSubclassId());
                $this->updateMuniSubstitution($form, $data->getSubclass());
            }

            if ($data->getTaxLossHarvesting()) {
                $this->updateTaxLossHarvestingIdSymbol($form, $data->getTaxLossHarvestingId());
            } else {
                $this->updateTaxLossHarvestingIdSymbol($form, null);
            }
        }
    }

    public function preBind(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();

        if (array_key_exists('muniSubstitution', $data)) {
            $this->updateMuniSubstitutionSymbol($form, $data['muniSubstitution']);
        }

        if (array_key_exists('tax_loss_harvesting', $data)) {
            $this->updateTaxLossHarvestingIdSymbol($form, $data['tax_loss_harvesting']);
        }

        //update security symbol
        $securityAssignment = array_key_exists('securityAssignment', $data) ? $data['securityAssignment'] : null;
        $this->updateSecuritySymbol($form, $securityAssignment);

        $withoutIds = array($securityAssignment);
        if (array_key_exists('muniSubstitution', $data)) {
            $withoutIds[] = $data['muniSubstitution'];
        }

        $this->updateTaxLossHarvesting($form, isset($data['subclass']) ? $data['subclass'] : null, $withoutIds);

        // end update security symbol

        if (array_key_exists('assetClass', $data)) {
            $this->updateSubclass($form, $data['assetClass']);
        }

        if (array_key_exists('subclass', $data)) {
            //$obj = $form->getData();

            //if (is_object($obj) && $obj->getId()) {
                $this->updateSecurity($form, $data['subclass']);
//            } else {
//                $this->updateSecurity($form, $data['subclass']);
//            }
            $this->updateMuniSubstitution($form, $data['subclass']);
        }
    }

    public function bind(FormEvent $event)
    {
        /** @var $data CeModelEntity */
        $data = $event->getData();

        if($data === null) return;

        $em = $this->em;
        $form = $event->getForm();

        /** @var $ceModelEntityRepo CeModelEntityRepository */
        $ceModelEntityRepo = $em->getRepository('WealthbotAdminBundle:CeModelEntity');

        if($data->getSubclass()){
            $exist = $ceModelEntityRepo->isExistSameSubclassesForModel(
                $this->ceModel->getId(),
                $data->getSubclass()->getId(),
                $this->isQualifiedModel,
                $data->getId()
            );

            if($exist) {
                if($data->getId()){
                    if($exist->getId() != $data->getId()){
                        $form->get('subclass')->addError(new FormError('You already hold this subclass in the model.'));
                    }
                }else{
                    $form->get('subclass')->addError(new FormError('You already hold this subclass in the model.'));
                }
            }
        }

        //TODO: Andrey
        $stock = 0;
        $bond = 0;

        $modelEntities = $ceModelEntityRepo->findBy(array(
            'modelId' => $this->ceModel->getId(),
            'isQualified' => $this->isQualifiedModel
        ));

        foreach ($modelEntities as $entity) {
            if (!$data->getId() || ($data->getId() != $entity->getId())) {
                if ($entity->getAssetClass()->getType() == 'Stocks') {
                    $stock += $entity->getPercent();
                }

                if ($entity->getAssetClass()->getType() == 'Bonds') {
                    $bond += $entity->getPercent();
                }
            }
        }

        $overAll = $stock + $bond + $data->getPercent();

        if (($overAll) > 100) {
            $form->get('percent')->addError(new FormError('Sum of the percents must be equal 100.'));
        }
    }

    protected function updateSubclass(FormInterface $form, $assetClassId)
    {
        $queryBuilder = $this->em->getRepository('WealthbotAdminBundle:Subclass')->getAvailableSubclassesQuery($assetClassId, $this->user);

        $form->add($this->factory->createNamed('subclass', 'entity', null, array(
            'class' => 'WealthbotAdminBundle:Subclass',
            'property' => 'name',
            'required' => false,
            'empty_value' => 'Choose Subclass',
            'query_builder' => $queryBuilder,
            'attr' => is_null($assetClassId) ? array('disabled' => 'disabled') : array()
        )));
    }

    protected function updateMuniSubstitution(FormInterface $form, $subclass)
    {
        if (!($subclass instanceof Subclass)) {
            $subclass = $this->em->getRepository('WealthbotAdminBundle:Subclass')->find($subclass);
        }

        if ( $this->user->hasRole('ROLE_ADMIN') || $this->user->hasRole('ROLE_SUPER_ADMIN') ||
            ($this->user->hasRole('ROLE_RIA') && $this->user->getRiaCompanyInformation()->getUseMunicipalBond())
        ) {

            /** @var SecurityAssignmentRepository $repo */
            $repo = $this->em->getRepository('WealthbotAdminBundle:SecurityAssignment');
            $parentModel = $this->ceModel->getParent();

            $existMuniSubstitution = $repo->hasMuniSubstitution($parentModel, $subclass, $this->user);

            if($existMuniSubstitution) {
                $queryBuilder = $repo->getAvailableMuniSubstitutionsQuery($parentModel->getId(), $subclass->getId());

                $form->add($this->factory->createNamed('muniSubstitution', 'entity', null, array(
                    'class' => 'WealthbotAdminBundle:SecurityAssignment',
                    'property' => 'security.name',
                    'required' => false,
                    'empty_value' => 'Choose Muni Substitution',
                    'query_builder' => $queryBuilder
                )));
            }
        }
    }

    protected function updateSecurity(FormInterface $form, $subclassId, $currentEntityId = null)
    {
        $queryBuilder = $this->em->getRepository('WealthbotAdminBundle:SecurityAssignment')->getAvailableSecuritiesQuery($this->ceModel, $subclassId, $currentEntityId);

        $form->add($this->factory->createNamed('securityAssignment', 'entity', null, array(
            'class' => 'WealthbotAdminBundle:SecurityAssignment',
            'property' => 'security.name',
            'empty_value' => 'Choose Security',
            'query_builder' => $queryBuilder,
            'attr' => is_null($subclassId) ? array('disabled' => 'disabled') : array()
        )));
    }

    protected function updateTaxLossHarvesting(FormInterface $form, $subclassId, $withoutIds = array())
    {
        if ($this->user->hasRole('ROLE_RIA') && $this->user->getRiaCompanyInformation()->getIsTaxLossHarvesting() &&
            (!$this->user->getRiaCompanyInformation()->getIsUseQualifiedModels() || ($this->user->getRiaCompanyInformation()->getIsUseQualifiedModels() && !$this->isQualifiedModel))) {
            
            /** @var $securityAssignmentRepo SecurityAssignmentRepository */
            $securityAssignmentRepo = $this->em->getRepository('WealthbotAdminBundle:SecurityAssignment');
            $securityQueryBuilder = $securityAssignmentRepo->getSecuritiesQBBySubclassIdAndWithoutSecuritiesIds($subclassId, $withoutIds);

            $form->add($this->factory->createNamed('tax_loss_harvesting', 'entity', null, array(
                'class' => 'WealthbotAdminBundle:SecurityAssignment',
                'property' => 'security.name',
                'empty_value' => 'Choose TLH Substitution',
                'query_builder' => $securityQueryBuilder,
                'attr' => empty($withoutIds) ? array('disabled' => 'disabled') : array(),
                'required' => false
            )));
        }
    }

    protected function updateSecuritySymbol(FormInterface $form, $securityId)
    {
        $this->updateSymbol($form, $securityId, 'symbol');
    }

    protected function updateMuniSubstitutionSymbol(FormInterface $form, $muniSubstitutionId)
    {
        $this->updateSymbol($form, $muniSubstitutionId, 'muni_substitution_symbol');
    }

    protected function updateTaxLossHarvestingIdSymbol(FormInterface $form, $taxLossHarvestingId)
    {
        $this->updateSymbol($form, $taxLossHarvestingId, 'tax_loss_harvesting_symbol');
    }

    protected function updateSymbol(FormInterface $form, $securityAssignmentId, $name)
    {
        $value = '';
        if ($securityAssignmentId) {
            /** @var $obj SecurityAssignment */
            $obj = $this->em->getRepository('WealthbotAdminBundle:SecurityAssignment')->find($securityAssignmentId);
            $value = $obj->getSecurity()->getSymbol();
        }

        $form->add($this->factory->createNamed($name, 'text', null, array(
            'property_path' => false,
            'required' => false,
            'attr' => array(
                'readonly' => 'readonly',
                'value' => $value
            ),
        )));
    }
}
