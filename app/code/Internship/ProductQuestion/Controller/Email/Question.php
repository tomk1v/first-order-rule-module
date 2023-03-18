<?php

namespace Internship\ProductQuestion\Controller\Email;

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
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Customer\Model\Session $customerSession
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
        $this->customerSession = $customerSession;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute()
    {
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setUrl($this->storeManager->getStore()->getBaseUrl());

        $request = $this->request->getParam('question');
        if(!$request) {
            $this->messageManager->addErrorMessage('Invalid form key');
            return $resultRedirect;
        }

        $emailTempVariables['product'] = '$product';
        $senderName = $this->scopeConfig->getValue(
            'trans_email/ident_custom1/name',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $senderEmail = $this->scopeConfig->getValue(
            'trans_email/ident_custom1/email',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        $receiver = $senderEmail;
        $postObject = new \Magento\Framework\DataObject();
        $postObject->setData($emailTempVariables);
        $sender = [
            'name' => $senderName,
            'email' => $senderEmail,
        ];

        try {
            $transport = $this->transportBuilder->setTemplateIdentifier('email_product_question')
                ->setTemplateOptions(['area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID])
                ->setTemplateVars([
                    'name' => $this->request->getParam('name'),
                    'email' => $this->request->getParam('email'),
                    'question' => $this->request->getParam('question')
                ])
                ->setFromByScope($sender)
                ->addTo($receiver)
                ->setReplyTo($receiver)
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
