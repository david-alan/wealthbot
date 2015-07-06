<?php


namespace Wealthbot\AdminBundle\Manager;

use Doctrine\ORM\EntityManager;
use Wealthbot\AdminBundle\Entity\BillingSpec;
use Wealthbot\AdminBundle\Entity\Fee;
use Wealthbot\ClientBundle\Manager\CashCalculationManager;
use Wealthbot\RiaBundle\Service\Manager\PeriodManager;
use Wealthbot\UserBundle\Entity\User;
use Wealthbot\ClientBundle\Entity\ClientAccount;
use Wealthbot\UserBundle\Manager\UserManager;

class FeeManager {

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var UserManager
     */
    protected $userManager;

    /** @var CashCalculationManager */
    protected $cashManager;

    /** @var PeriodManager */
    protected $periodManager;

    /** @var BillingSpecManager */
    protected $billingSpecManager;

    public function __construct(EntityManager $em, UserManager $userManager, CashCalculationManager $cashManager, PeriodManager $periodManager, BillingSpecManager $billingSpecManager) {
        $this->em = $em;
        $this->userManager = $userManager;
        $this->cashManager = $cashManager;
        $this->periodManager = $periodManager;
        $this->billingSpecManager = $billingSpecManager;
    }

    /**
     * Return ria related system fees.
     *
     * @param User $ria
     * @throws \Exception
     * @return array|\Wealthbot\AdminBundle\Entity\Fee[]
     */
    public function getAdminFee(User $ria) {

        $billingSpec = $ria->getAppointedBillingSpec();

        if (!$billingSpec) {
            //Todo: can it be situation when no billingSpec appointed to ria?
            return array();
        }

        if ($billingSpec->getOwner() !== null) {
            throw new \Exception('Owner of Admin Billing Spec for RIA must be null. May be it is not admin fees?');
        }

        return $billingSpec->getFees()->getValues();
    }

    /**
     * @param User $ria
     * @param array $riaFees
     * @return array
     */
    public function getClientFees(User $ria, $riaFees = array())
    {
        $adminFees = $this->getAdminFee($ria);

        $sortFees = array();
        foreach($adminFees as $adminFee){
            $sortFees[] = $adminFee->getTierTop();
        }

        // Add to array all fees from ria
        foreach ($riaFees as $riaFee) {
            if ($riaFee instanceof Fee) {
                if ($riaFee->getTierTop() && !in_array($riaFee->getTierTop(), $sortFees)) {
                    $sortFees[] = $riaFee->getTierTop();
                }
            } else {
                if ($riaFee['tier_top'] && !in_array($riaFee['tier_top'], $sortFees)) {
                    $sortFees[] = $riaFee['tier_top'];
                }
            }
        }

        sort($sortFees);

        // Search intervals and calculate total fees for client
        $start = 0;
        $currFeeWithoutRetirement = 0;
        $currFeeWithRetirement = 0;
        $clientFees = array();

        foreach ($sortFees as $sortFee) {
            // Search admin fee
            foreach ($adminFees as $adminFee) {
                if ($adminFee->getTierTop() >= $sortFee) {
                    $currFeeWithoutRetirement = $adminFee->getFeeWithoutRetirement();
                    $currFeeWithRetirement = $adminFee->getFeeWithRetirement();
                    break;
                }
            }

            // Search client fee
            foreach ($riaFees as $riaFee) {
                if ($riaFee instanceof Fee) {
                    if ($riaFee->getTierTop() >= $sortFee) {
                        $currFeeWithoutRetirement += $riaFee->getFeeWithoutRetirement();
                        $currFeeWithRetirement += $riaFee->getFeeWithoutRetirement();
                        break;
                    }
                } else {
                    if ($riaFee['tier_top'] >= $sortFee) {
                        $currFeeWithoutRetirement += $riaFee['fee_without_retirement'];
                        $currFeeWithRetirement += $riaFee['fee_without_retirement'];
                        break;
                    }
                }
            }

            // Store data
            $currFeeCard['tier_bottom'] = $start;
            $currFeeCard['tier_top'] = $sortFee;
            $currFeeCard['fee_without_retirement'] = $currFeeWithoutRetirement;
            // deprecated
            //$currFeeCard['fee_with_retirement'] = $currFeeWithRetirement;
            $start = $sortFee + 0.01;
            $clientFees[] = $currFeeCard;
        }

        return $clientFees;
    }

    /**
     * Get ria fee
     *
     * @param ClientAccount $clientAccount
     * @param integer $year
     * @param integer $quarter
     * @return float
     */
    public function getRiaFee(ClientAccount $clientAccount, $year, $quarter)
    {
        $period = $this->periodManager->getPeriod($year, $quarter);
        $client = $clientAccount->getClient();
        $billingSpec = $client->getAppointedBillingSpec();
        $fees = $billingSpec->getFees();

        if ($billingSpec->getType() == BillingSpec::TYPE_FLAT) {
            return $fees[0]->getFeeWithoutRetirement();
        }

        if ($billingSpec->getType() == BillingSpec::TYPE_TIER) {
            $value = $this->cashManager->getAccountValueOnDate($clientAccount, $period['endDate']);

            $feeValue = $this->calculateFee($value, $fees);
            $feeValue = max($billingSpec->getMinimalFee(), $feeValue);
            return $feeValue;
        }

        throw new \Exception("No tier type " . $billingSpec->getType());
    }

    /**
     * Get wealthbot.io fee
     *
     * @param ClientAccount $clientAccount
     * @param integer $year
     * @param integer $quarter
     * @return float
     */
    public function getCEFee(ClientAccount $clientAccount, $year, $quarter)
    {
        $ria = $clientAccount->getClient()->getRia();
        $fees = $this->getAdminFee($ria);

        $period = $this->periodManager->getPeriod($year, $quarter);
        $value  = $this->cashManager->getAccountValueOnDate($clientAccount, $period['endDate']);

        return $this->calculateFee($value, $fees);
    }

    /**
     *
     * Returns calculation tiers array with keys:
     *
     *     fee - float,
     *
     *     tier_top - value top for this tier,
     *
     *     tier_value - got value for this tier,
     *
     *     fee_amount - caculated amount of fee for this tier.
     *
     * @param $value
     * @param Fee[] $fees
     * @return array
     */
    public function getCalculationTiers($value, $fees)
    {
        $result = array();

        $feeArray = array();
        foreach($fees as $fee) {
            $feeArray[] = array(
                'tier_top' => $fee->getTierTop(),
                'fee_without_retirement' => $fee->getFeeWithoutRetirement()
            );
        }

        sort($feeArray);

        $lastTopTier = 0;
        foreach($feeArray as $feeItem){
            $x = min($feeItem['tier_top'], $value);
            $tierValue = ($x - $lastTopTier);
            $feeAmount = $tierValue * $feeItem['fee_without_retirement'];
            $lastTopTier = $x;
            $result[] = array(
                'fee'         => $feeItem['fee_without_retirement'],
                'tier_top'    => $feeItem['tier_top'],
                'tier_value'  => $tierValue,
                'fee_amount'  => $feeAmount
            );
            if ($lastTopTier >= $value) {
                break;
            }
        }

        return $result;
    }

    /**
     * @param float $value
     * @param array|\Wealthbot\AdminBundle\Entity\Fee[]
     */
    public function calculateFee($value, $fees)
    {
        $tiers = $this->getCalculationTiers($value, $fees);
        $feeValue = 0;
        foreach ($tiers as $tier) {
            $feeValue += $tier['fee_amount'];
        }

        return round($feeValue * 100)/100;
    }

    /**
     * TODO: deprecated
     * @param ClientAccount $clientAccount
     * @param $year
     * @param $quarter
     * @return float|int
     */
    public function getBillAmount(ClientAccount $clientAccount, $year, $quarter)
    {
        $repo = $this->em->getRepository('WealthbotClientBundle:ClientAccountValue');
        $period = $this->periodManager->getPeriod($year, $quarter);

        $systemAccount = $clientAccount->getSystemAccount();
        $accountValues = $repo->getAverageAccountValues($systemAccount, $period['startDate'], $period['endDate']);

        if ($accountValues['count_values']) {
            $lastDay = $repo->getLastDayByPeriod($systemAccount, $period['startDate'], $period['endDate']);
            $dayInPeriod = $period['endDate']->diff($period['startDate'])->format('%a');

            //TODO: ???
            $fee = $this->getRiaFee($clientAccount, $year, $quarter);

            //TODO: Formula (BillingSpec fee value) * (account value in last day of quarter) * (number of days when account was open) / (number of days in quarter)
            return ($fee * $lastDay->getTotalValue() * $accountValues['count_values']) / $dayInPeriod;
        }

        return 0;
    }

    /**
     * @param float $fee
     * @param int $daysWorked
     * @param int $daysInPeriod
     * @return float
     * @throws \RangeException
     */
    public function calculateFeeBilled($fee, $daysWorked, $daysInPeriod)
    {
        if ($daysInPeriod == 0) {
            throw new \RangeException('Division by zero.');
        }

        $value = ($fee * $daysWorked) / $daysInPeriod;

        return round($value * 100) / 100;
    }

    /**
     * Reset Ria fee for License Fee Relationship
     *
     * @param User $ria
     */
    public function resetRiaFee(User $ria)
    {
        $appointedBillingSpec = $ria->getAppointedBillingSpec();

        if ($appointedBillingSpec) {

            if (1 === $appointedBillingSpec->getAppointedUsers()->count()) {
                $this->billingSpecManager->remove($appointedBillingSpec);
            } else {
                $appointedBillingSpec->removeAppointedUser($ria);

                $this->em->persist($appointedBillingSpec);
                $this->em->flush();
            }
        }

        $billingSpec = $this->billingSpecManager->initDefaultAdminSpec($ria);
        $ria->setAppointedBillingSpec($billingSpec);

        $fee = $this->initDefaultAdminLicenseFee();

        $billingSpec->addFee($fee);

        $this->em->persist($billingSpec);
        $this->em->persist($ria);

        $this->em->flush();
    }

    /**
     * Init Default Admin fee for License Fee Relationship
     *
     * @return Fee
     */
    public function initDefaultAdminLicenseFee()
    {
        $fee = new Fee();
        $fee->setFeeWithRetirement(0.0);
        $fee->setFeeWithoutRetirement(0.0);
        $fee->setTierTop(Fee::INFINITY);

        return $fee;
    }

    /**
     * @param int $year
     * @param int $quarter
     * @return array
     */
    public function getPeriod($year, $quarter)
    {
        return $this->periodManager->getPeriod($year, $quarter);
    }
}