<?php

namespace Inernship\ProductQuestion\Controller\Email;

use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\Escaper;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Message\ManagerInterface;
use Magento\Store\Model\StoreManagerInterface;

class Question implements \Magento\Framework\App\ActionInterface
{
    /**
     * @var TransportBuilder
     */
    protected $transportBuilder;

    /**
     * @var Escaper
     */
    protected $escaper;

    /**
     * @var ManagerInterface
     */
    protected $messageManager;

    /**
     * @var ResultFactory
     */
    protected $resultFactory;

    /**
     * @var Validator
     */
    protected $formKeyValidator;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * Email constructor.
     * @param Context $context
     * @param ManagerInterface $messageManager
     * @param Escaper $escaper
     * @param TransportBuilder $transportBuilder
     * @param ResultFactory $resultFactory
     * @param Validator $formKeyValidator
     * @param ScopeConfigInterface $scopeConfig
     * @param RequestInterface $request
     * @param StoreManagerInterface $storeManager
     * @param Session $checkoutSession
     */
    public function __construct(
        Context $context,
        ManagerInterface $messageManager,
        Escaper $escaper,
        TransportBuilder $transportBuilder,
        ResultFactory $resultFactory,
        Validator $formKeyValidator,
        ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Checkout\Model\Session $checkoutSession
    ){
        $this->messageManager = $messageManager;
        $this->escaper = $escaper;
        $this->transportBuilder = $transportBuilder;
        $this->resultFactory = $resultFactory;
        $this->formKeyValidator = $formKeyValidator;
        $this->scopeConfig = $scopeConfig;
        $this->request = $request;
        $this->storeManager = $storeManager;
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute()
    {
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setUrl($this->storeManager->getStore()->getBaseUrl());

        $request = $this->request->getParam('answer');
        if(!$this->formKeyValidator->validate($this->request)) {
            $this->messageManager->addErrorMessage('Invalid form key');
            return $resultRedirect;
        }

        $emailTempVariables['myvar'] = '$myvar';
        $senderName = $this->scopeConfig->getValue(
            'general/store_information/name',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $wholaEmail = 'marketing@whola.com.au';
        $postObject = new \Magento\Framework\DataObject();
        $postObject->setData($emailTempVariables);
        $sender = [
            'name' => $senderName,
            'email' => $wholaEmail,
        ];

        try {
            $transport = $this->transportBuilder->setTemplateIdentifier('email_win_template')
                ->setTemplateOptions(['area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID])
                ->setTemplateVars([
                    'answer' => $this->request->getParam('answer'),
                    'orderId' => $this->checkoutSession->getLastRealOrderId()
                ])
                ->setFromByScope($sender)
                ->addTo($wholaEmail)
                ->setReplyTo($wholaEmail)
                ->getTransport();
            $transport->sendMessage();
            $this->messageManager->addSuccessMessage(__('We have received your message'));
            return $resultRedirect;
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('Something went wrong'));
            return $resultRedirect;
        }
    }
}
