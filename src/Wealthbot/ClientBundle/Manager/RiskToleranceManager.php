<?php
/**
 * Created by JetBrains PhpStorm.
 * User: amalyuhin
 * Date: 20.06.13
 * Time: 19:20
 * To change this template use File | Settings | File Templates.
 */

namespace Wealthbot\ClientBundle\Manager;


use Doctrine\Common\Persistence\ObjectManager;
use Wealthbot\AdminBundle\Manager\CeModelManager;
use Wealthbot\ClientBundle\Entity\ClientQuestionnaireAnswer;
use Wealthbot\ClientBundle\Model\RiskTolerance;
use Wealthbot\RiaBundle\Entity\RiskAnswer;
use Wealthbot\RiaBundle\Entity\RiskQuestion;
use Wealthbot\RiaBundle\Exception\AdvisorHasNoExistingModel;
use Wealthbot\UserBundle\Entity\User;

class RiskToleranceManager
{
    /** @var \Wealthbot\UserBundle\Entity\User */
    private $user;

    /** @var ObjectManager */
    private $em;

    /** @var array $userAnswers array of ClientQuestionnaireAnswer objects */
    private $userAnswers;

    /** @var \Wealthbot\ClientBundle\Model\RiskTolerance */
    private $riskTolerance;

    public function __construct(User $user, ObjectManager $em, array $answers)
    {
        $this->user = $user;
        $this->em = $em;
        $this->userAnswers = $this->createUserAnswers($answers);
        $this->riskTolerance = new RiskTolerance($user, $this->userAnswers);
    }

    /**
     * Get UserAnswers
     *
     * @return array
     */
    public function getUserAnswers()
    {
        return $this->userAnswers;
    }

    /**
     * Save $userAnswers in db
     */
    public function saveUserAnswers()
    {
        foreach ($this->userAnswers as $userAnswer) {
            $this->em->persist($userAnswer);
        }

        $this->em->flush();
    }

    /**
     * * Returns suggested portfolio
     *
     * @return null|\Wealthbot\AdminBundle\Entity\CeModel
     */
    public function getSuggestedPortfolio()
    {
        $modelManager = new CeModelManager($this->em, '\Wealthbot\AdminBundle\Entity\CeModel');

        $ria = $this->riskTolerance->getRia();
        $parentModel = $ria->getRiaCompanyInformation()->getPortfolioModel();

        return $this->riskTolerance->getSuggestedPortfolio($modelManager->getChildModels($parentModel));
    }


    /**
     * Create and return array of ClientQuestionnaireAnswer objects by $answers array
     *
     * @param array $answers
     * @return array
     */
    private function createUserAnswers(array $answers)
    {
        $userAnswers = array();

        foreach ($answers as $answer) {

            /** @var RiskQuestion $question */
            $question = $answer['question'];
            $data = $answer['data'];

            if ($question->getIsWithdrawAgeInput()) {
                $data = $this->getAnswerForWithdrawAgeQuestion($question, $answer['data']);
            }

            $userAnswer = new ClientQuestionnaireAnswer();
            $userAnswer->setClient($this->user);
            $userAnswer->setQuestion($question);
            $userAnswer->setAnswer($data);

            $userAnswers[] = $userAnswer;
        }

        return $userAnswers;
    }

    /**
     * Return RiskAnswer object for withdraw age input question
     *
     * @param RiskQuestion $question
     * @param $ageDiff
     * @return null|RiskAnswer
     */
    private function getAnswerForWithdrawAgeQuestion(RiskQuestion $question, $ageDiff)
    {
        $answers = $this->em->getRepository('WealthbotRiaBundle:RiskAnswer')->findBy(
            array(
                'risk_question_id' => $question->getId()
            ),
            array(
                'title' => 'DESC'
            )
        );

        $result = null;

        /** @var RiskAnswer $answer */
        foreach ($answers as $answer) {
            $string = $answer->getTitle();
            $symbol = substr($string, 0, 1);
            $number = (int) substr($string, 1);

            if ($symbol == '>') {
                if ($ageDiff >= $number) {
                    return $answer;
                }
            } else {
                if ($ageDiff <= $number) {
                    $result = $answer;
                }
            }
        }

        return $result;
    }
}