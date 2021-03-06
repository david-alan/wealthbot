<?php
/**
 * Created by JetBrains PhpStorm.
 * User: amalyuhin
 * Date: 07.11.12
 * Time: 12:45
 * To change this template use File | Settings | File Templates.
 */

namespace Wealthbot\ClientBundle\Form\Handler;

use Wealthbot\AdminBundle\Entity\CeModel;
use Wealthbot\ClientBundle\Manager\ClientPortfolioManager;
use Wealthbot\ClientBundle\Manager\RiskToleranceManager;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManager;
use Wealthbot\UserBundle\Entity\User;

class ClientQuestionsFormHandler
{
    protected $form;
    protected $request;
    protected $em;
    protected $options;

    public function __construct(Form $form, Request $request, EntityManager $em, array $options = array())
    {
        $this->form = $form;
        $this->request = $request;
        $this->em = $em;
        $this->options = $options;
    }

    public function process(User $user)
    {
        $this->form->bind($this->request);

        if ($this->form->isValid()) {
            $this->preProcess($user);

            $questionsOwner = $this->em->getRepository('WealthbotRiaBundle:RiskQuestion')->getQuestionsOwner($user);
            $this->success($user, $questionsOwner);

            return true;
        }

        return false;
    }

    protected function preProcess(User $user)
    {
        $userAnswers = $this->em->getRepository('WealthbotClientBundle:ClientQuestionnaireAnswer')->findBy(array(
            'client_id' => $user->getId()
        ));

        foreach ($userAnswers as $answer) {
            $this->em->remove($answer);
        }

        $this->em->flush();
    }

    protected function success(User $user, User $questionsOwner)
    {
        $em = $this->em;
        $questions = $em->getRepository('WealthbotRiaBundle:RiskQuestion')->getOrderedQuestionsByOwnerId($questionsOwner->getId());

        $withdrawAge = 0;
        $answers = array();

        foreach ($questions as $question) {
            $key = 'answer_'.$question->getId();
            $data = $this->form->get($key)->getData();

            $answer = array(
                'question' => $question,
            );

            if ($question->getIsWithdrawAgeInput()) {
                $withdrawAge = (int) $answer;

                $age = $this->getClientAge($user);
                $ageDiff = $withdrawAge - (int) $age;

                $answer['data'] = $ageDiff;
            } else {
                $answer['data'] = $data;
            }

            $answers[] = $answer;
        }

        $riskToleranceManager = new RiskToleranceManager($user, $this->em, $answers);
        $riskToleranceManager->saveUserAnswers();

        if (!$this->request->isXmlHttpRequest()) {
            $suggestedModel = $riskToleranceManager->getSuggestedPortfolio();
            $this->processSuggestedPortfolio($user, $suggestedModel, $withdrawAge);
        }
    }

    /**
     * Process suggested portfolio.
     * Submit final portfolio if client's ria has Straight-Through portfolio processing
     *
     * @param User $user
     * @param CeModel $suggestedModel
     * @param $withdrawAge
     * @throws \InvalidArgumentException
     */
    protected function processSuggestedPortfolio(User $user, CeModel $suggestedModel, $withdrawAge)
    {
        $profile = $user->getProfile();
        $profile->setWithdrawAge($withdrawAge);

        //$profile->setSuggestedPortfolio($suggestedPortfolio);
        $clientPortfolioManager = $this->getOption('client_portfolio_manager');
        if (!($clientPortfolioManager instanceof ClientPortfolioManager)) {
            throw new \InvalidArgumentException('Option client_portfolio_manager must be instance of ClientPortfolioManager');
        }

        $clientPortfolioManager->proposePortfolio($user, $suggestedModel);

        $riaCompanyInfo = $user->getRiaCompanyInformation();
        if ($riaCompanyInfo && $riaCompanyInfo->isStraightThroughProcessing()) {
            $clientPortfolioManager->approveProposedPortfolio($user);
        }

        $this->em->persist($user);
        $this->em->flush();
    }

    /**
     * Get age of the client
     *
     * @param \Wealthbot\UserBundle\Entity\User $user
     * @return int
     */
    protected function getClientAge(User $user)
    {
        $age = $user->getProfile()->getBirthDate()->diff(new \DateTime('now'))->y;

        return $age;
    }

    protected function getOption($name, $defaultValue = null) {
        if ($this->hasOption($name)) {
            return $this->options[$name];
        }

        return $defaultValue;
    }

    protected function hasOption($name) {
        return isset($this->options[$name]);
    }
}
